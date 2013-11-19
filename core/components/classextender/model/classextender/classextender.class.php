<?php
/**
 * ClassExtender class file for ClassExtender extra
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
 *
 * @package classextender
 */


 class ClassExtender {
    /** @var $modx modX */
    public $modx;
    /** @var $props array */
    public $props;
    /* this is not the extended user class.
       it is the class of the object that holds
       the extended data */
    public $class = '';
    public $package = '';
    public $prefix = '';
    public $tableName = '';
    public $generator;
    public $modelPath;
    public $devMode = false;
    public $dirPermission;


    function __construct(&$modx, &$config = array()) {
        $this->modx =& $modx;
        $this->props =& $config;

    }

    public function init() {
        $this->package = $this->modx->getOption('package', $this->props, 'extendeduser');
        $this->prefix = $this->modx->getOption('tablePrefix', $this->props, 'ext_');
        $this->tableName = $this->modx->getOption('tableName', $this->props, 'userData');
        $this->dirPermission = (int) $this->modx->getOption('dirPermission', $this->props, 0755);
        $manager = $this->modx->getManager();
        $this->generator = $manager->getGenerator();
        $corePath = $this->modx->getOption('ce.core_path', null, null);

        /* See if we're running under MyComponent */
        if (! empty($corePath)) {
            $this->devMode = true;
        }

        $basePath = $this->modx->getOption('ce.core_path', null, $this->modx->getOption('core_path') . 'components/' . $this->package . '/');
        $this->modelPath = $basePath . 'model/';

    }

    public function generateSchema() {
        /* someday */
    }

    public function generateClassFiles() {
        /** $generator xPDOGenerator */
        $path = $this->modelPath . 'schema/';
        $this->generator->parseSchema($path . $this->package . '.mysql.schema.xml', $this->modelPath);
        $this->copyDir($this->modelPath, MODX_CORE_PATH . 'components/' . $this->package);

    }
    public function createTables() {
        $success = $this->modx->addPackage($this->package, $this->modelPath, $this->prefix);
        if (! $success) {
            echo "\naddPackage failed.";
            return;
        }
        $manager = $this->modx->getManager();
        if (! $manager) {
            echo "\ngetManager failed.";
            return;
        }
        $manager->createObjectContainer($this->tableName);
    }

    /* This only needs to be run once, but it doesn't hurt to re-run it.
       -- Developers: note that if you're
       using MyComponent, this references the actual core path, not
       your dev. path. ClassExtender will copy the package files to
       core/components/classextender/model/ */

    public function add_extension_package() {
        $path = '[[++core_path]]' . 'components/classextender/model/';
        $this->modx->addExtensionPackage($this->package, $path, array('tablePrefix' => $this->prefix));
    }

    public function loadPackage() {
        $success = $this->modx->addPackage($this->package, $this->modelPath, $this->prefix);
        if (!$success) {
            echo "\naddPackage failed.";
            return;
        }
    }

     public function copyDir($source, $destination) {
         //echo "SOURCE: " . $source . "\nDESTINATION: " . $destination . "\n";
         if (is_dir($source)) {
             if (!is_dir($destination)) {
                 mkdir($destination, $this->dirPermission, true);
             }
             $objects = scandir($source);
             if (sizeof($objects) > 0) {
                 foreach ($objects as $file) {
                     if ($file == "." || $file == ".." || $file == '.git' || $file == '.svn') {
                         continue;
                     }

                     if (is_dir($source . '/' . $file)) {
                         $this->copyDir($source . '/' . $file, $destination . '/' . $file);
                     } else {
                         copy($source . '/' . $file, $destination . '/' . $file);
                     }
                 }
             }

             return true;
         } elseif (is_file($source)) {
             return copy($source, $destination);
         } else {
             return false;
         }
     }

}