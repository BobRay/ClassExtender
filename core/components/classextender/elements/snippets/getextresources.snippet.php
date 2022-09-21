<?php
/**
 * GetExtResources snippet for ClassExtender extra
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
 * Show resource information
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * See the example resource under the ClassExtender folder
 *
 * @package classextender
 **/

/* The extendedresource package should be preloaded
   due to being registered in the extension_packages
   System Setting */

require_once MODX_CORE_PATH . 'components/classextender/model/ce_autoload.php';


$cePrefix = $modx->getVersionData()['version'] >= 3
    ? 'extendedresource\\'
    : '';


$modx->lexicon->load('classextender:default');

/* @var $scriptProperties array */
$scriptProperties = isset($scriptProperties)
    ? $scriptProperties
    : array();
$sp = $scriptProperties;

$cssFile = $modx->getOption('cssFile', $scriptProperties,
    '', true);

if (!empty($cssFile)) {
    $modx->regClientCSS($cssFile);
}

$resourceClass = $modx->getOption('resourceDataClass', $sp, 'resourceData');

if (strpos($resourceClass, '\\') === false) {
    $resourceClass = $cePrefix . $resourceClass;
}


$where = $modx->getOption('where', $sp, array());
$where = !empty($where)
    ? $modx->fromJSON($where)
    : array();

$sortBy = $modx->getOption('sortby', $sp, 'pagetitle');
$sortDir = $modx->getOption('sortdir', $sp, 'ASC');

$outerTpl = $modx->getOption('extResourceOuterTpl', $sp, 'extResourceOuterTpl');
$innerTpl = $modx->getOption('extResourceInnerTpl', $sp, 'extResourceInnerTpl');
$rowTpl = $modx->getOption('extResourceRowTpl', $sp, 'extResourceRowTpl');


$c = $modx->newQuery($resourceClass);
$c->sortby($sortBy, $sortDir);

if (!empty($where)) {
    $c->where($where);
}


$resources = $modx->getCollectionGraph($resourceClass, '{"Resource":{}}', $c);

$count = count($resources);

if (!$count) {
    return '<p class="ce_error">' . $modx->lexicon('ce.no_resources_found') . '</p>';

}

/* @var $resource modResource */
$i = 0;
$outer = $modx->getChunk($outerTpl);

$output = '';
$innerOutput = '';
foreach ($resources as $resource) {
    $fields = $resource->toArray();

    if ($resource->Resource) {
        $fields = array_merge($resource->Resource->toArray(), $fields);
    }
    $inner = $modx->getChunk($innerTpl, $fields);
    $row = $modx->getChunk($rowTpl, $fields);
    $innerOutput .= str_replace('[[+extResourceRow]]', $row, $inner);
    $i++;
}

$output = str_replace('[[+extResourceInner]]', $innerOutput, $outer);

return $output;