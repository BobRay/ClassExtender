<?php
/**
 * Validator for ClassExtender extra
 *
 * Copyright 2012-2023 Bob Ray <https://bobsguides.com>
 * Created on 08-13-2014
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
 * @package classextender
 * @subpackage build
 */

/* @var $object xPDOObject */
/* @var $modx modX */
/* @var array $options */

$chunks = array(
    'ExtraUserFields',
    'ExtUserSchema',
    'ExtraResourceFields',
    'ExtResourceSchema',
);

/* @var modTransportPackage $transport */
if ($transport) {
    $modx =& $transport->xpdo;
} else {
    $modx =& $object->xpdo;
}

$prefix = $modx->getVersionData()['version'] >= 3
    ? 'MODX\Revolution\\'
    : '';
if (isset($_SESSION['validator_run'])) {
    return true;
}
$catObj = $modx->getObject($prefix . 'modCategory', array('category' => 'ClassExtender'));
$categoryId = $catObj ? $catObj->get('id') : 0;

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
        break;

    case xPDOTransport::ACTION_UPGRADE:
        unset($_SESSION['']);
        /* Attempt to preserve plugin enabled status */
        if (!empty($categoryId)) {
            $plugins = $modx->getCollection($prefix .
                'modPlugin',array('category' => $categoryId));
            if (!empty($plugins)) {
                $enablePlugins = array();
                foreach($plugins as $plugin) {
                    $enablePlugins[$plugin->get('id')] = $plugin->get('disabled');
                }
                $_SESSION['enable_plugins'] = $enablePlugins;
                $_SESSION['validator_run'] = 1;
            }
        }

        foreach ($chunks as $chunk) {
            $newName = 'My' . $chunk;
            $obj = $modx->getObject($prefix . 'modChunk', array('name' => $newName));
            if (!$obj) {
                $oldChunk = $modx->getObject($prefix . 'modChunk', array('name' => $chunk));
                if ($oldChunk) {
                    $newChunk = $modx->newObject($prefix . 'modChunk');
                    $newChunk->set('name', $newName);
                    $content = $oldChunk->getContent();
                    if ($chunk == 'ExtUserSchema') {
                        /* Don't replace if already done */
                        if (strpos($content, 'Profile') === false) {
                            $content = str_replace('<aggregate alias="User" class= "modUser" local="userdata_id" foreign="id" cardinality="one" owner="foreign"/>', '<aggregate alias="User" class= "modUser" local="userdata_id" foreign="id" cardinality="one" owner="foreign"/>
    <aggregate alias="Profile" class="modUserProfile" local="userdata_id" foreign="internalKey" cardinality="one" owner="foreign"/>', $content);
                        }
                    }

                    $newChunk->setContent($content);
                    if ($categoryId) {
                        $newChunk->set('category', $categoryId);
                    }
                    $newChunk->save();
                }
            }
        }

        break;

    case xPDOTransport::ACTION_UNINSTALL:
        break;
}


return true;