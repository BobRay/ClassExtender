<?php
/**
 * Resolver for ClassExtender extra
 *
 * Copyright 2013 by Bob Ray <http://bobsguides.com>
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
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            break;

        case xPDOTransport::ACTION_UNINSTALL:
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
            $setting = $modx->getObject('modSystemSetting',
                array('key' => 'extension_packages'));
            if (! empty($setting)) {
                $modx->removeExtensionPackage('extendeduser');
                $modx->removeExtensionPackage('extendedresource');
            }

            $pages = array(
                 'ClassExtender',
                 'Extend modResource',
                 'Extend modUser',
            );
            foreach ($pages as $page) {
                $resource = $modx->getObject('modResource', array('pagetitle' => $page));
                if ($resource) {
                    @$resource->remove();
                }
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

            $cm = $modx->getCacheManager();
            $cm->refresh();

            break;
    }
}

return true;