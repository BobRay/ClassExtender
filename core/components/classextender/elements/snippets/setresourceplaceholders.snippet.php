<?php
/**
 * SetResourcePlaceholders snippet for ClassExtender extra
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
 * Set placeholders for extra resource fields
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package classextender
 **/

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

$sp = $scriptProperties;
$resourceId = $modx->getOption('resourceId', $sp, NULL);
$prefix = $modx->getOption('prefix', $sp, '');

if ($resourceId != NULL) {
    $resource = $modx->getObject('extResource', $resourceId);
    if (!$resource) {
        return $modx->lexicon('ce.resource_not_found');
    }
} else {
    $resource = $modx->resource;
}

$data = $resource->getOne('Data');

if ($data) {
    $modx->toPlaceholders($data, $prefix);
}