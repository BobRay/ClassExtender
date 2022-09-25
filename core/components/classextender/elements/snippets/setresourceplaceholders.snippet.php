<?php
/**
 * SetResourcePlaceholders snippet for ClassExtender extra
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
 * Set placeholders for extra resource fields
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

$cePrefix = '';
$modxPrefix = '';

require_once MODX_CORE_PATH . 'components/classextender/model/ce_autoload.php';

$isModx3 = $modx->getVersionData()['version'] >= 3;

if ($isModx3) {
    $cePrefix = 'extendedresource\\';
    $modxPrefix = 'MODX\Revolution\\';
}

$sp = $scriptProperties;

$cssFile = $modx->getOption('cssFile', $scriptProperties,
    '', true);

if (!empty($cssFile)) {
    $modx->regClientCSS($cssFile);
}

$resourceId = $modx->getOption('resourceId', $sp, NULL, true);
$placeholderPrefix = $modx->getOption('prefix', $sp, '', true);

if ($resourceId != NULL) {
    $resource = $modx->getObject($modxPrefix . 'modResource', $resourceId);
    if (!$resource) {
        return $modx->lexicon('ce.resource_not_found');
    }
} else {
    $resource = $modx->resource;
}

$data = $modx->getObject($cePrefix . 'resourceData',
    array('resourcedata_id' => $resource->get('id')));

if ($data) {
    $modx->toPlaceholders($data, $placeholderPrefix);
} else {
    $modx->log(modX::LOG_LEVEL_ERROR, 'No extra fields set for resource ' . $resource->get('id'));
}

$modx->toPlaceholders($resource->toArray(), $placeholderPrefix);

return '';