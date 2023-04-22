<?php
/**
 * ExtraUserFields plugin for ClassExtender extra
 *
 * Copyright 2012-2023 Bob Ray <https://bobsguides.com>
 * Created on 12-10-2013
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
 * Add and process extra fields in Create/Edit User form
 *
 * Variables
 * ---------
 *
 * @var $modx modX
 * @var $scriptProperties array
 * @var $user modUser
 * @package classextender
 *
 * Events: OnUserFormPrerender, OnUserFormRender, OnUserFormSave
 *
 **/

require_once MODX_CORE_PATH . 'components/classextender/model/ce_autoload.php';


// $packageName = 'extendeduser';
// $modelPath = MODX_CORE_PATH . 'components/classextender/model/';
// $tablePrefix = 'ext_';

/* $success = $modx->addPackage($packageName,
            $modelPath, $tablePrefix);

        if (!$success) {
            $modx->log(modX::LOG_LEVEL_ERROR, $this->modx->lexicon("ce.addpackage_failed"));
        }
*/

/* Define extra fields */
$fields = array();

$prefix = $modx->getVersionData()['version'] >= 3
    ? 'extendeduser\\'
    : '';

// $table = $modx->getTableName($prefix . 'UserData');
// $modx->log(modX::LOG_LEVEL_ERROR, 'Table: ' . $table);

/* Make sure we have an extUser object to work with */
if (isset($user)) {
    $data = $modx->getObject($prefix . 'UserData', array('userdata_id' => $user->get('id')));
}
/* @var $data UserData */

/* Create related object if it doesn't exist */
if (!$data) {
    $data = $modx->newObject($prefix . 'UserData');
}

if ($data) {
    $fields = $data->toArray();
}

switch ($modx->event->name) {
    case 'OnUserFormPrerender':
        /* if you want to add custom scripts, css, etc., register them here */
        break;
    case 'OnUserFormRender':

        if ($data) {
            /* Set fields with values from DB (if any) */
            foreach ($fields as $key => $value) {
                $dbValue = $data->get($key);
                /* Make sure there are no null values */
                $dbValue = $dbValue === null ? '' : $dbValue;
                $fields[$key] = $dbValue;
            }
        }

        /* Now do the HTML */
        $extraFields = $modx->getChunk('MyExtraUserFields', $fields);

        /* Add our custom fields to the Create/Edit User form */
        $modx->event->output($extraFields);
        break;


    case 'OnUserFormSave':
        /* do processing logic here. */
        /* @var $user extUser */
        if (!$data) {
            $modx->log(modX::LOG_LEVEL_ERROR, '[ExtraUserFields] No Data object');
            return;
        }
        if (!$user) {
            $modx->log(modX::LOG_LEVEL_ERROR, '[ExtraUserFields] No User object');
            return;
        }
        $data->set('userdata_id', $user->get('id'));
        $fields = array_keys($fields);
        $postKeys = array_keys($_POST);
        $dirty = false;

        foreach ($fields as $field) {
            if ($field === 'id') {
                continue;
            }
            if (in_array($field, $postKeys)) {
                /* Convert NULL to '', but preserve '0' */
                if (empty($_POST[$field]) && ($_POST[$field] !== '0')) {
                    $_POST[$field] = '';
                }
                /* If $_POST values don't match DB value,
                   update field and set dirty flag */
                if ($_POST[$field] !== $data->get($field)) {
                    $data->set($field, $_POST[$field]);
                    $dirty = true;
                }
            }
        }

        /* Set registration date to today -- delete if not needed */
        if (in_array('registrationDate', $fields)) {
            $rDate = $data->get('registrationDate');
            if (empty($rDate)) {
                $dirty = true;
                $data->set('registrationDate', strtotime(date('Y-m-d')));
            }
        }
        /* End of registration date section */

        /* Save the data, if necessary */
        if ($dirty) {

            if (!$data->save()) {
                $modx->log(modX::LOG_LEVEL_ERROR, 'Could not save user');
            }
        }

        break;
    case 'OnUserRemove':
        $extData = $modx->getObject($prefix . 'UserData', array('userdata_id' => $user->get('id')));
        if ($extData) {
            $extData->remove();
        }
        break;
}
return '';