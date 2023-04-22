<?php
/**
 * Resolver for ClassExtender extra
 *
 * Copyright 2012-2023 Bob Ray <https://bobsguides.com>
 * Created on 11-10-2013
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
/** @var modTransportPackage $transport */
if ($transport) {
    $modx =& $transport->xpdo;
} else {
    $modx =& $object->xpdo;
}

    unset($_SESSION['validator_run']);

    $prefix = $modx->getVersionData()['version'] >= 3
        ?'MODX\Revolution\\'
        : '';


    $catObj = $modx->getObject($prefix . 'modCategory', array('category' => 'ClassExtender'));
    $categoryId = $catObj? $catObj->get('id') : 0;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_UPGRADE:
            if (isset($_SESSION['enable_plugins'])) {
                $pArray = $_SESSION['enable_plugins'];
                foreach ($pArray as $pluginId => $pluginDisabled) {
                    $plugin = $modx->getObject($prefix . 'modPlugin', $pluginId);
                    if ($plugin->get('disabled') != $pluginDisabled) {
                        $plugin->set('disabled', $pluginDisabled);
                        $plugin->save();
                    }
                }
            }
            /* Set class keys back to original values */
            $modx->updateCollection(
                'modResource',
                array('class_key' => 'modDocument'),
                array('class_key' => 'extResource')
            );
            $modx->updateCollection(
                'modUser',
                array('class_key' => 'modUser'),
                array('class_key' => 'extUser')
            );
        /* Intentional fallthrough */
        case xPDOTransport::ACTION_INSTALL:


            foreach ($chunks as $chunk) {
                $newName = 'My' . $chunk;
                $obj = $modx->getObject($prefix . 'modChunk', array('name'=> $newName));
                if (! $obj) {
                    $oldChunk = $modx->getObject($prefix . 'modChunk', array('name' => $chunk));
                    if ($oldChunk) {
                        $newChunk = $modx->newObject($prefix . 'modChunk');
                        $newChunk->set('name', $newName);
                        $newChunk->setContent($oldChunk->getContent());
                        if ($categoryId) {
                            $newChunk->set('category', $categoryId);
                        }
                        $newChunk->save();
                    }
                } else {
                    /* Just set the category */
                    if ($categoryId && ($obj->get('category') !== $categoryId)) {
                        $obj->set('category', $categoryId);
                        $obj->save();
                    }
                }
            }
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            $modx->updateCollection(
                $prefix . 'modResource',
                           array('class_key' => $prefix . 'modDocument'),
                           array('class_key' => $prefix . 'extResource')
            );
            $modx->updateCollection(
                $prefix . 'modUser',
                           array('class_key' => $prefix . 'modUser'),
                           array('class_key' => $prefix . 'extUser')
            );

            $modx->log(modX::LOG_LEVEL_INFO, "To prevent the possible loss of important data, the database tables have not been removed. Remove them manually if you don't need them");

            /* Remove modExtensionPackage objects
               and their corresponding namespaces */

            $query = $modx->newQuery($prefix . 'modExtensionPackage');
            /* Get records with 'classextender' in path */
            $criteria = array(
                'path:LIKE' => '%' . 'classextender' . '%',
            );

            $query->where($criteria);

            $extensionPackages = $modx->getCollection($prefix . 'modExtensionPackage', $query);

            foreach ($extensionPackages as $extensionPackage) {
                $namespaceName = $extensionPackage->get('namespace');

                /* Don't remove core ns or classextender ns */
                $delete = ($namespaceName !== 'classextender' &&
                    $namespaceName !== 'core');
                $nameSpaceObject = $modx->getObject($prefix .
                    'modNamespace', array('name' => $namespaceName));
                if ($delete) {
                    if ($nameSpaceObject) {
                            $nameSpaceObject->remove();
                    }
                    $extensionPackage->remove();
                }
            }

            foreach($chunks as $chunk) {
                $newName = 'My' . $chunk;
                $obj = $modx->getObject($prefix . 'modChunk', array('name' => $newName));
                if ($obj) {
                    $obj->remove();
                }
            }
            $cm = $modx->getCacheManager();
            $cm->refresh();

            break;
    }


return true;