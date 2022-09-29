<?php
/**
 * ExtUserUpdateProfile snippet for ClassExtender extra
 *
 * Copyright 2012-2022 Bob Ray <https://bobsguides.com>
 * Created on 03-23-2014
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
 * Set placeholders for and update extended user data
 *
 * See the example resource under the ClassExtender folder
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package classextender
 **/

$modx->lexicon->load('login:updateprofile');
$modx->lexicon->load('classextender:default');

if (! $modx->user->hasSessionContext($modx->context->get('key'))) {
    return "<h3>" . $modx->lexicon('ce_login_required') . "</h3>";
}
$cssFile = $modx->getOption('cssFile', $scriptProperties,
    '', true);

if (!empty($cssFile)) {
    $modx->regClientCSS($cssFile);
}

require_once(MODX_CORE_PATH . 'components/classextender/model/ce_autoload.php');

$cePrefix = $modx->getVersionData()['version'] >= 3
    ? 'extendeduser\\'
    : '';

$submission = isset($_POST['login-updprof-btn']) && ($_POST['login-updprof-btn'] == $modx->lexicon('login.update_profile'));

$data = null;
$user = null;
$fields = array();

/* @var $data userData */

if (isset($modx->user)) {

    $user =& $modx->user;
    $data = $modx->getObject($cePrefix . 'userData',
        array('userdata_id' => $user->get('id')), false);
    if ($data) {
        $fields = $data->toArray();
    } else {
        $data = $modx->newObject($cePrefix . 'userData');
        if ($data) {
            $data->set('userdata_id', $user->get('id'));
            $fields = $data->toArray();

        }
    }
}

if (!is_array($fields) || empty($fields)) {
    return '';
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

    if ($dirty) {
        if (!$data->save()) {
            $modx->log(modX::LOG_LEVEL_ERROR, 'could not save userData object');
        }
    }
}

return '';