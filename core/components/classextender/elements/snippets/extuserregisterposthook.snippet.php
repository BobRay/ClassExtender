<?php
/**
 * ExtUserRegisterPosthook snippet for ClassExtender extra
 *
 * Copyright 2012-2023 Bob Ray <https://bobsguides.com>
 * Created on 05-04-2015
 *
 * Thanks to MODX Forum contributor Karl Forsyth for his contributions to this code.
 *
 * ClassExtender is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * ClassExtender is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * ClassExtender; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package classextender
 */

/**
 * Description
 * -----------
 * Update extended user data on registration
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package classextender
 **/

/** Usage:
 * See the example resource under the ClassExtender folder
 * */

/*

[[!Register?
    &submitVar=`login-register-btn`
    ...
    &useExtended=`0`
    &postHooks=`ExtUserRegister`
]]

*/

require_once MODX_CORE_PATH . 'components/classextender/model/ce_autoload.php';

$isModx3 = $modx->getVersionData()['version'] >= 3
    ? true
    : false;


$cePrefix = '';
$modxPrefix = '';

if ($isModx3) {
    $cePrefix = 'extendeduser\\';
    $modxPrefix = 'MODX\Revolution\\';
}

$submission = isset($_POST['login-register-btn']) && ($_POST['login-register-btn'] == 'Register');

$data = NULL;
$newUser = NULL;
$userName = NULL;
$userID = NULL;
$fields = array();

/* @var $data UserData */

if (isset($modx->user)) {
    $usernameField = $modx->getOption('usernameField', $scriptProperties, 'username', true);

    /** @var $hook LoginHooks */
    $userName = $hook->getValue($usernameField);

    /* Get new user ID via username (created by Register snippet) */
    $newUser = $modx->getObject($modxPrefix . "modUser", array('username' => $userName));

    if (!$newUser) {
        $modx->log(modX::LOG_LEVEL_ERROR, "Could not get user object, username: {$userName}");
        return false;
    }

    $userId = $newUser->get('id');


    $data = $modx->getObject($cePrefix . 'UserData',
        array('userdata_id' => $userId), false);
    if ($data) {
        $fields = $data->toArray();
    } else {
        $data = $modx->newObject($cePrefix . 'UserData');
        if ($data) {
            $data->set('userdata_id', $userId);
            $fields = $data->toArray();
        }
    }
}

if (!is_array($fields) || empty($fields)) {
    /** @var $hook LoginHooks */
    $hook->addError('username', '[ExtUserRegisterPosthook] Error getting user data');
    return false;
}

/* Convert any nulls to '' */
if (!empty($fields)) {
    foreach ($fields as $key => $value) {
        if (empty($value) && ($value !== '0')) {
            $fields[$key] = '';
        }
    }
    $modx->setPlaceholders($fields);
}

if ($submission) {
    // $modx->log(modX::LOG_LEVEL_ERROR, '[ClassExtender] ' . 'Form Submitted');
    $modx->request->sanitizeRequest();
    $dirty = false;

    foreach ($fields as $key => $value) {
        if (isset($_POST[$key])) {
            if ($value !== $_POST[$key]) {
                $data->set($key, $_POST[$key]);
                $dirty = true;
            }
        }
    }
    /* Set registration date to today -- delete if not needed */
    if (isset($fields['registrationDate'])) {
        $rDate = $data->get('registrationDate');
        if (empty($rDate)) {
            $dirty = true;
            $data->set('registrationDate', strtotime(date('Y-m-d')));
        }
    }
    /* End of registration date section */

    if ($dirty) {
        if ($data->save()) {
            // $msg = '[ExtUserRegisterPosthook] Saved successfully';
            // $modx->log(modX::LOG_LEVEL_ERROR, $msg);
        } else {
            $msg = '[ExtUserRegisterPosthook] Save failed';
            $hook->addError('username', $msg);
            return false;
        }
    }
}
$_POST = array();
return true;