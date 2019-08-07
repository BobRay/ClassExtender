<?php
/**
 * ExtUserUpdateProfile snippet for ClassExtender extra
 *
 * Copyright 2012-2019 Bob Ray <https://bobsguides.com>
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
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package classextender
 **/

$modx->lexicon->load('login:updateprofile');

$submission = isset($_POST['login-updprof-btn']) && ($_POST['login-updprof-btn'] == $modx->lexicon('login.update_profile'));

$data = null;
$user = null;
$fields = array();

/* @var $data userData */

if (isset($modx->user) && ($modx->user instanceof modUser)) {
    
    $user =& $modx->user;
    $data = $modx->getObject('userData',
        array('userdata_id' => $user->get('id')), false);
    if ($data) {
        $fields = $data->toArray();
    } else {
        $data = $modx->newObject('userData');
        if ($data) {
            $data->set('userdata_id', $user->get('id'));
            $fields = $data->toArray();
            
        }
    }
}

if (! is_array($fields) || empty($fields)) {
    return '';
}

/* Convert any nulls to '' */
if (!empty($fields)) {
    foreach($fields as $key => $value) {
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
        $data->save();
    }
}

return '';