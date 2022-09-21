<?php
/**
 * SetUserPlaceholders snippet for ClassExtender extra
 *
 * Copyright 2012-2022 Bob Ray <https://bobsguides.com>
 * Created on 04-13-2014
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
 * Set placeholders for extra user fields
 *
 * See the example resource under the ClassExtender folder
 *
 * Variables
 * ---------
 *
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package classextender
 **/

$modx->lexicon->load('classextender:default');

require_once(MODX_CORE_PATH . 'components/classextender/model/ce_autoload.php');

$sp = $scriptProperties;

$cssFile = $modx->getOption('cssFile', $scriptProperties,
    '', true);

if (!empty($cssFile)) {
    $modx->regClientCSS($cssFile);
}

$userId = $modx->getOption('userId', $sp, NULL);
$prefix = $modx->getOption('prefix', $sp, '');
$dataClass = $modx->getOption('userDataClass', $sp, 'userData');

$cePrefix = $modx->getVersionData()['version'] >= 3
    ? 'extendeduser\\'
    : '';

$c = $modx->newQuery($cePrefix . $dataClass);


if (!empty($userId)) {
    $c->where(array('userdata_id' => $userId));
} else {
    $c->where(array('userdata_id' => $modx->user->get('id')));
}

$data = $modx->getObjectGraph($cePrefix . $dataClass, '{"Profile":{},"User":{}}', $c);

if (!$data) {
    return $modx->lexicon('ce.user_not_found');
}

if ($data) {
    $modx->toPlaceholders($data, $prefix);
}
if ($data->Profile) {
    $modx->toPlaceholders($data->Profile, $prefix);
}

if ($data->User) {
    $modx->toPlaceholders($data->User, $prefix);
}

return '';