<?php
/**
 * Resolver for ClassExtender extra
 *
 * Copyright 2012-2019 Bob Ray <https://bobsguides.com>
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

if ($object->xpdo) {
    $modx =& $object->xpdo;

    $prefix = $modx->getVersionData()['version'] >= 3
        ?'MODX\Revolution\\'
        : '';

    $chunks = array(
        'ExtraUserFields',
        'ExtUserSchema',
        'ExtraResourceFields',
        'ExtResourceSchema',
    );
    $catObj = $modx->getObject($prefix . 'modCategory', array('category' => 'ClassExtender'));
    $categoryId = $catObj? $catObj->get('id') : null;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
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
            $setting = $modx->getObject($prefix . 'modSystemSetting',
                array('key' => 'extension_packages'));
            if (! empty($setting)) {
                $modx->removeExtensionPackage('extendeduser');
                $modx->removeExtensionPackage('extendedresource');
            }

            $table = 'ext_resource_data';
            $sql = "DROP TABLE IF EXISTS $table ";
            $results = $modx->query($sql);
            while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
            };
            
            $table = 'ext_user_data';
            $sql = "DROP TABLE IF EXISTS $table";
            $results = $modx->query($sql);
            while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
            };
            /* Remove modExtensionPackage objects in 2.3 */
            if (class_exists($prefix . 'modExtensionPackage')) {
                /** @var $rec xPDOObject */
                $recs = $modx->getCollection($prefix . 'modExtensionPackage',
                    array('namespace' => 'extendeduser'));
                foreach ($recs as $rec) {
                    $rec->remove();
                }
                $recs = $modx->getCollection($prefix . 'modExtensionPackage',
                    array('namespace' => 'extendedresource'));
                foreach ($recs as $rec) {
                    $rec->remove();
                }
            }
            foreach($chunks as $chunk) {
                $newName = 'My' . $chunk;
                $obj = $modx->getObject($prefix . 'modChunk', array('name' => $newName));
                if ($obj) {
                    $obj->remove();
                }
            }

            /* Remove extensionPackage objects and namespaces */
            $packages = array(
                'extendeduser',
                'extendedresource',
            );

            foreach ($packages as $name) {
                $extPackage = $modx->getObject($prefix . 'modExtensionPackage', array('name' => $name));
                if ($extPackage) {
                    $extPackage->remove();
                }
                $nameSpace = $modx->getObject($prefix . 'modNamespace', array('name' => $name));
                if ($nameSpace) {
                    $nameSpace->remove();
                }
            }

            $cm = $modx->getCacheManager();
            $cm->refresh();

            break;
    }
}

return true;