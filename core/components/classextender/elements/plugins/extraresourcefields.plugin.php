<?php
/**
 * ExtraResourceFields plugin for ClassExtender extra
 *
 * Copyright 2013 by Bob Ray <http://bobsguides.com>
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

// $chunk = $modx->getObject('modChunk', array('name' => 'Debug'));

$fields = array(
    'name' => '',
    'color' => '',
    'breed' => '',
    'age' => '',
);

$data = null;

/* Make sure we have an extResource object to work with */
if (isset($resource) && ($resource instanceof  modResource)) {
    if ($resource instanceof extResource) {
        $data = $resource->getOne('Data');
    } else {
        $resource->set('class_key', 'extResource');
        $resource->save();
        $resource = $modx->getObject('extResource', $resource->get('id'));
    }
}
/* @var $data resourceData */


if (!$data) {
    $data = $modx->newObject('resourceData');
}


switch ($modx->event->name) {
    case 'OnDocFormPrerender':
        /* if you want to add custom scripts, css, etc, register them here */
        break;
    case 'OnDocFormRender':
        if ($data) {
            /* Set fields with values from DB (if any) */
            foreach ($fields as $key => $value) {
                $dbValue = $data->get($key);
                /* Make sure there are no null values */
                $dbValue = $dbValue === NULL
                    ? ''
                    : $dbValue;
                $fields[$key] = $dbValue;
            }
        }

        /* now do the HTML */

        $extraFields = $modx->getChunk('ExtraResourceFields', $fields);

        /* Add our custom fields to the Create/Edit Resource form */
        $modx->event->output($extraFields);
        break;
    case 'OnDocFormSave':
        /* do processing logic here. */
        /* @var $resource extResource */
        if (!$data) {
            $modx->log(modX::LOG_LEVEL_ERROR, '[ExtraResourceFields] No Data object');
            return;
        }
        if (!$resource) {
            $modx->log(modX::LOG_LEVEL_ERROR, '[ExtraResourceFields] No Resource object');
            return;
        }
        $fields = array_keys($fields);
        $postKeys = array_keys($_POST);
        $dirty = false;
        foreach($fields as $field) {
            if (in_array($field, $postKeys)) {
                // $debug .= "\nIn Array" . $field;
                if ($_POST[$field] != $data->get($field)) {
                    // $debug .= "\nDirty" . $field;
                    if (empty($_POST[$field])) {
                        $_POST[$field] = '';
                    }
                    $data->set($field, $_POST[$field]);
                    $dirty = true;
                }
            }
        }

        if ($dirty) {
            $resource->addOne($data);
            $resource->save();
        }

        // $chunk->setContent($debug . "\n" . print_r($_POST, true));
        // $chunk->save();
        break;
}
return;