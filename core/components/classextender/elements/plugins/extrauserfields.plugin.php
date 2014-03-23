<?php
/**
 * ExtraUserFields plugin for ClassExtender extra
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
 * Add and process extra fields in Create/Edit User form
 *
 * Variables
 * ---------
 *
*@var $modx modX
 * @var $scriptProperties array
 *
 * @package classextender
 *
 * Events: OnDocFormRender, OnDocFormPrerender, OnDocFormSave
 *
 **/

// $chunk = $modx->getObject('modChunk', array('name' => 'Debug'));

$fields = array(
    'firstName'        => '',
    'lastName'         => '',
    'title'            => '',
    'company'          => '',
    'registrationDate' => 0,
    'category1'        => '',
    'category1_Other'  => '',
    'category2'        => '',
    'category2_Other'  => '',
    'category3'        => '',
    'category3_Other'  => '',
);

$data = null;
if (isset($user) && ($user instanceof  modUser)) {
    if ($user instanceof extUser) {
        $data = $user->getOne('Data');
    } else {
        $user->set('class_key', 'extUser');
        $user->save();
        $user = $modx->getObject('extUser', $user->get('id'));
    }
}
/* @var $data userData */


if (!$data) {
    $data = $modx->newObject('userData');
}


switch ($modx->event->name) {
    case 'OnUserFormPrerender':
        /* if you want to add custom scripts, css, etc, register them here */
        break;
    case 'OnUserFormRender':
        $list = $modx->getChunk('UserCategories');
        $categoryList = explode(',', trim($list));

/*         $categoryList = array(
            '',
            'Bakery/Pastry Chef',
            'Beverage',
            'Candy Making',
            'Catering',
            'Chef',
            'Consulting',
            'Editor/Writer',
            'Education/Cooking School',
            'Food Artisan/Farmer/Producer',
            'Food Stylist',
            'Graphic Design',
            'Grocery/Retail',
            'Marketing',
            'Media/TV/Radio/Internet',
            'Nutrition Services',
            'Personal Chef',
            'Product Development',
            'Public Relations/Event Planner',
            'Recipe Tester/Developer',
            'Restaurant Owner',
            'Retired',
            'Sales Representative',
            'Student',
            'Wholesale Products',
        ); */

        if ($data) {
            $fields['firstName'] = $data->get('firstName');
            $fields['lastName'] = $data->get('lastName');
            $fields['title'] = $data->get('title');
            $fields['company'] = $data->get('company');
            $fields['category1'] = $data->get('category1');
            $fields['category2'] = $data->get('category2');
            $fields['category3'] = $data->get('category3');
            $fields['category1_Other'] = $data->get('category1_Other');
            $fields['category2_Other'] = $data->get('category2_Other');
            $fields['category3_Other'] = $data->get('category3_Other');



            foreach ($fields as $key => $field) {
                if (empty($field)) {
                    $fields[$key] = '';
                }
            }

            $categories1 = $categories2 = $categories3 = '';
            foreach ($categoryList as $cat) {
                $selected = $cat == $fields['category1']? 'selected="selected "': ' ';
                $categories1 .= "\n<option " . $selected . "value=\"" . $cat . '">' . $cat . '</option>';

                $selected = $cat == $fields['category2'] ? 'selected="selected "' : ' ';
                $categories2 .= "\n<option " . $selected . "value=\"" . $cat . '">' . $cat . '</option>';

                $selected = $cat == $fields['category3'] ? 'selected="selected "' : ' ';
                $categories3 .= "\n<option " . $selected . "value=\"" . $cat . '">' . $cat . '</option>';


            }
            $fields['categories1'] = $categories1;
            $fields['categories2'] = $categories2;
            $fields['categories3'] = $categories3;
        }

        /* now do the HTML */

        $extraFields = $modx->getChunk('ExtraUserFields', $fields);

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
        $fields = array_keys($fields);
        $postKeys = array_keys($_POST);
        $dirty = false;
        foreach($fields as $field) {
            if (in_array($field, $postKeys)) {
                $debug .= "\nIn Array" . $field;
                if ($_POST[$field] != $data->get($field)) {
                    $debug .= "\nDirty" . $field;
                    if (empty($_POST[$field])) {
                        $_POST[$field] = '';
                    }
                    $data->set($field, $_POST[$field]);
                    $dirty = true;
                }
            }
        }
        $rDate = $data->get('registrationDate');
        if (empty($rDate)) {
            $dirty = true;
            $data->set('registrationDate', strtotime(date('Y-m-d')));
        }
        if ($dirty) {
            $user->addOne($data);
            $user->save();
        }

       // $chunk->setContent($debug . "\n" . print_r($_POST, true));
       // $chunk->save();
        break;
}
return;