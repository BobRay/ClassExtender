<?php
/**
 * GetExtUsers snippet for ClassExtender extra
 *
 * Copyright 2013 by Bob Ray <http://bobsguides.com>
 * Created on 01-05-2014
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
 * Show user information
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package classextender
 **/

/* Load extendeduser package if not registered in
   extension_packages System Setting */

$modx->lexicon->load('classextender:default');

if (! class_exists('extUser')) {
    $package = 'extendeduser';
    $prefix = 'ext_';
    $basePath = $modx->getOption('ce.core_path', NULL, $modx->getOption('core_path') . 'components/' . $package . '/');

    $modelPath = $basePath . 'model/';

    $success = $modx->addPackage($package, $modelPath, $prefix);
    if (!$success) {
        return $modx->lexicon('ce.addpackage_failed');
    }
}



/* @var $scriptProperties array */
$scriptProperties = isset($scriptProperties)? $scriptProperties : array();
$sp = $scriptProperties;

$userClass = $modx->getOption('userClass', $sp, 'extUser' );
$category = $modx->getOption('category', $sp, 'All');
$showInactive = $modx->getOption('showInactive', $sp, false);


$where = $modx->getOption('where', $sp, array(
     array(
         'Data.category1:='    => $category,
         'OR:Data.category2:=' => $category,
         'OR:Data.category3:=' => $category,
     ),
));

$outerTpl = $modx->getOption('extUserOuterTpl', $sp, 'extUserOuterTpl');
$innerTpl = $modx->getOption('extUserInnerTpl', $sp, 'extUserInnerTpl');
$rowTpl = $modx->getOption('extUserRowTpl', $sp, 'extUserRowTpl');



$c = $modx->newQuery($userClass);
$c->sortby('Data.lastName', 'ASC');

/* No where clause if 'All' and showInactive */
if ($category == 'All') {
    if (!$showInactive) {
        $c->where(array('active' => true));
    }
} else {
    $c->where($where);
}

$users = $modx->getCollectionGraph($userClass, '{"Profile":{},"Data":{}}', $c);

$count = count($users);

if (! $count) {
    return 'No Users Found in category';

}

/* @var $user modUser */
$i = 0;
$outer = $modx->getChunk($outerTpl);

$output = '';
$innerOutput = '';
foreach ($users as $user) {
    $fields = $user->toArray();
    unset($fields['password'], $fields['cachepwd'], $fields['salt'], $fields['hash_class'] );
    if ($user->Profile) {
        $fields = array_merge($user->Profile->toArray(), $fields);
    }
    if ($user->Data) {
        $fields = array_merge($user->Data->toArray(), $fields);
        $fields['category1'] = !empty($fields['category1_other'])
            ? $fields['category1_other']
            : isset($fields['category1'])? $fields['category1'] : '';
        $fields['category2'] = !empty($fields['category2_other'])
            ? $fields['category2_other']
            : isset($fields['category2'])
                ? $fields['category2']
                : '';
        $fields['category3'] = !empty($fields['category3_other'])
            ? $fields['category3_other']
            : isset($fields['category3'])
                ? $fields['category3']
                : '';

    }
    $inner = $modx->getChunk($innerTpl, $fields);
    $row = $modx->getChunk($rowTpl, $fields);
    $innerOutput .= str_replace('[[+extUserRow]]', $row, $inner);
    $i++;
}

$output = str_replace('[[+extUserInner]]', $innerOutput, $outer);

return $output;