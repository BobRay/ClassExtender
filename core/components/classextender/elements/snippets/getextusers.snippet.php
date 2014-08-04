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

/* The extendeduser package should be pre-loaded
   due to being registered in the extension_packages
   System Setting */

$modx->lexicon->load('classextender:default');


/* @var $scriptProperties array */
$scriptProperties = isset($scriptProperties)? $scriptProperties : array();
$sp = $scriptProperties;

$userClass = $modx->getOption('userClass', $sp, 'userData' );

$where = $modx->getOption('where', $sp, array());
$where = !empty($where)
    ? $modx->fromJSON($where)
    : array();

$outerTpl = $modx->getOption('extUserOuterTpl', $sp, 'extUserOuterTpl');
$innerTpl = $modx->getOption('extUserInnerTpl', $sp, 'extUserInnerTpl');
$rowTpl = $modx->getOption('extUserRowTpl', $sp, 'extUserRowTpl');
$sortBy = $modx->getOption('sortby', $sp, 'username');
$sortDir = $modx->getOption('sortdir', $sp, 'ASC');


$c = $modx->newQuery($userClass);
$c->sortby($sortBy, $sortDir);
$c->where($where);

$users = $modx->getCollectionGraph($userClass, '{"Profile":{},"Data":{}}', $c);

$count = count($users);

if (! $count) {
    return $modx->lexicon('ce.no_users_found');

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
    }
    $inner = $modx->getChunk($innerTpl, $fields);
    $row = $modx->getChunk($rowTpl, $fields);
    $innerOutput .= str_replace('[[+extUserRow]]', $row, $inner);
    $i++;
}

$output = str_replace('[[+extUserInner]]', $innerOutput, $outer);

return $output;