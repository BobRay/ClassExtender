<?php
/**
 * GetExtResources snippet for ClassExtender extra
 *
 * Copyright 2013 by Bob Ray <http://bobsguides.com>
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
 * @package classextender
 **/

/* Load extendedresource package if not registered in
   extension_packages System Setting */

$modx->lexicon->load('classextender:default');

if (!class_exists('extResource')) {
    $package = 'extendedresource';
    $prefix = 'ext_';
    $basePath = $modx->getOption('ce.core_path', NULL, $modx->getOption('core_path') . 'components/' . $package . '/');

    $modelPath = $basePath . 'model/';

    $success = $modx->addPackage($package, $modelPath, $prefix);
    if (!$success) {
        return $modx->lexicon('ce.addpackage_failed');
    }
}


/* @var $scriptProperties array */
$scriptProperties = isset($scriptProperties)
    ? $scriptProperties
    : array();
$sp = $scriptProperties;

$resourceClass = $modx->getOption('resourceClass', $sp, 'extResource');

$where = $modx->getOption('where', $sp, array());

$sortBy = $modx->getOption('sortby', 'pagetitle');
$sortDir = $modx->getOption('sortdir', 'ASC');

$outerTpl = $modx->getOption('extResourceOuterTpl', $sp, 'extResourceOuterTpl');
$innerTpl = $modx->getOption('extResourceInnerTpl', $sp, 'extResourceInnerTpl');
$rowTpl = $modx->getOption('extResourceRowTpl', $sp, 'extResourceRowTpl');


$c = $modx->newQuery($resourceClass);
$c->sortby($sortBy, $sortDir);

if (! empty($where)) {
    $c->where($where);
}


$resources = $modx->getCollectionGraph($resourceClass, '"Data":{}}', $c);

$count = count($resources);

if (!$count) {
    return $modx->lexicon('ce.no_resources_found');

}

/* @var $resource modResource */
$i = 0;
$outer = $modx->getChunk($outerTpl);

$output = '';
$innerOutput = '';
foreach ($resources as $resource) {
    $fields = $resource->toArray();

    if ($resource->Data) {
        $fields = array_merge($resource->Data->toArray(), $fields);
    }
    $inner = $modx->getChunk($innerTpl, $fields);
    $row = $modx->getChunk($rowTpl, $fields);
    $innerOutput .= str_replace('[[+extResourceRow]]', $row, $inner);
    $i++;
}

$output = str_replace('[[+extResourceInner]]', $innerOutput, $outer);

return $output;