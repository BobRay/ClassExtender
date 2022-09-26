<?php
/**
 * ExtraResourceFields plugin for ClassExtender extra
 *
 * Copyright 2012-2022 Bob Ray <https://bobsguides.com>
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
 * Add and process extra fields in Create/Edit Resource form
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package classextender
 **/

require_once(MODX_CORE_PATH . 'components/classextender/model/ce_autoload.php');


$cePrefix = $modx->getVersionData()['version'] >= 3
    ? 'extendedresource\\'
    : '';

$fields = array();
$data = null;

/** @var $data xPDOObject */
/** @var $resource modResource */
if ($resource) {
    $data = $modx->getObject($cePrefix . 'resourceData',
        array('resourcedata_id' => $resource->get('id')));
}

if (!$data) {
    $data = $modx->newObject($cePrefix . 'resourceData');
}
if ($data) {
    $fields = $data->toArray();
} else {
    return '';
}

// $modx->log(modX::LOG_LEVEL_ERROR, 'Event: ' . $modx->event->name);

switch ($modx->event->name) {
    case 'OnDocFormPrerender':
        /* if you want to add custom scripts, css, etc, register them here */
        break;
    case 'OnDocFormRender':
        // $modx->log(modX::LOG_LEVEL_ERROR, 'In OnDocFormRender');
        if ($data) {
            /* Set fields with values from DB (if any) */
            foreach ($fields as $key => $value) {
                /* Make sure there are no null values */
                if ($value === null) {
                    $fields[$key] = '';
                }
            }
        }

        /* now do the HTML */

        $extraFields = $modx->getChunk('MyExtraResourceFields', $fields);

        /* Add our custom fields to the Create/Edit Resource form */
        // $modx->log(modX::LOG_LEVEL_ERROR, print_r($extraFields, true));
        $modx->event->output($extraFields);
        break;
    case 'OnDocFormSave':

        if (!$data) {
            $modx->log(modX::LOG_LEVEL_ERROR, '[ExtraResourceFields] No Data object');
            return;
        }
        if (!$resource) {
            $modx->log(modX::LOG_LEVEL_ERROR, '[ExtraResourceFields] No Resource object');
            return;
        }
        $data->set('resourcedata_id', $resource->get('id'));
        $fields = array_keys($fields);
        $postKeys = array_keys($_POST);
        $dirty = false;
        foreach ($fields as $field) {
            if ($field === 'id') {
                continue;
            }
            if (in_array($field, $postKeys)) {
                if ($_POST[$field] != $data->get($field)) {
                    if (empty($_POST[$field])) {
                        $_POST[$field] = '';
                    }
                    $data->set($field, $_POST[$field]);
                    $dirty = true;
                }
            }
        }

        if ($dirty) {
            if (!$data->save()) {
                $modx->log(modX::LOG_LEVEL_ERROR, 'Could not save resourceData object');
            }
        }

        break;

    case 'OnEmptyTrash':
        /** @var $resources array() */
        foreach ($resources as $resource) {
            $data = $modx->getObject($cePrefix . 'resourceData', array('resourcedata_id' => $resource->get('id')));
            if ($data) {
                $data->remove();
            }
        }

        break;
}
return;