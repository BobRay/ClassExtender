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
    public $ce_package_name = '';
    public $ce_class = '';
    public $ce_parent_object = '';
    public $ce_table_prefix = '';
    public $ce_table_name = '';
    public $ce_method = '';
    public $ce_register = 'Yes';
    public $generator;
    public $modelPath;
    public $devMode = false;
    public $dirPermission;
    protected $output = '';
    protected $errors = array();


    function __construct(&$modx, &$config = array()) {
        $this->modx =& $modx;
        $this->props =& $config;

    }

    public function init() {
        $this->output = '';
        $this->dirPermission = (int) $this->modx->getOption('dirPermission',
            $this->props, 0755);
        $fields = array(
            'ce_package_name',
            'ce_class',
            'ce_parent_object',
            'ce_table_prefix',
            'ce_table_name',
        );
        if (isset($_POST['submitVar']) && $_POST['submitVar'] == 'submitVar') {
            echo '<br>POSTED<br>';
            echo print_r($_POST, true);
            foreach($fields as $field) {
                if (isset($_POST[$field])) {
                    $this->$field = $_POST[$field];
                }
            }
            $this->ce_method = isset($_POST['ce_method'])
                ? $_POST['ce_method']
                : 'use_schema';

            $this->ce_register = isset($_POST['ce_register'])
                ? $_POST['ce_register']
                : 'Yes';

            $manager = $this->modx->getManager();
            $this->generator = $manager->getGenerator();
            $corePath = $this->modx->getOption('ce.core_path', NULL, NULL);

            /* See if we're running under MyComponent */
            if (!empty($corePath)) {
                $this->devMode = true;
            }

            $basePath = $this->modx->getOption('ce.core_path', NULL,
                $this->modx->getOption('core_path') .
                'components/classextender/');
            $this->modelPath = $basePath . 'model/';

        } else {
            echo '<br>NOT POSTED<br>';
            $this->ce_package_name = $this->modx->getOption('package',
                $this->props, 'extendeduser');
            $this->ce_class = $this->modx->getOption('class',
                $this->props, 'extUser');
            $this->ce_parent_object = $this->modx->getOption('parentObject',
                $this->props, 'modUser');
            $this->ce_table_prefix = $this->modx->getOption('tablePrefix',
                $this->props, 'ext_');
            $this->ce_table_name = $this->modx->getOption('tableName',
                $this->props, 'user_data');
            $this->ce_method = $this->modx->getOption('method',
                $this->props, 'use_schema');
            $this->ce_register = $this->modx->getOption('registerPackage',
                $this->props, 'Yes');
        }


    }

    public function process() {
        $fields = array(
            'ce_package_name' => $this->ce_package_name,
            'ce_class' => $this->ce_class,
            'ce_parent_object' => $this->ce_parent_object,
            'ce_table_prefix' => $this->ce_table_prefix,
            'ce_table_name' => $this->ce_table_name,


        );
        if ($this->ce_method == 'use_table') {
            $fields['ce_table_checked'] = 'checked="checked"';
        } else {
            $fields['ce_schema_checked'] = 'checked="checked"';
        }
        if ($this->ce_register == 'register_no') {
            $fields['ce_register_no_checked'] = 'checked="checked"';
        } else {
            $fields['ce_register_yes_checked'] = 'checked="checked"';
        }


        $this->displayForm($fields);
    }

    public function displayForm($fields) {
        $this->output .= $this->modx->getChunk('ClassExtenderForm', $fields);
    }

    protected function addError($msg) {
        $this->errors[] = $msg;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function hasError() {
        return !empty($this->errors);
    }

    public function getOutput() {
        return $this->output;
    }


    public function generateSchema() {
        $success = true;
        $path = $this->modelPath . 'schema';
        if (!is_dir($path)) {
            mkdir($path, $this->dirPermission, true);
        }
        $file = $path . '/' . strtolower($this->ce_package_name) . '.mysql.schema.xml';
        $tables = array(
            $this->ce_table_prefix . $this->ce_table_name => $this->ce_class
        );
        $xml = $this->generator->writeSchema($file, $this->ce_package_name,
            $this->ce_parent_object, $this->ce_table_prefix, true, $tables);

        if ($xml) {
            $this->modx->log(modX::LOG_LEVEL_INFO,
                'Schema file written to ' . $file);
        } else {
            $this->addError($this->modx->lexicon('ce.error_writing_schema_file~~Error writing schema file'));
            $success = false;
        }

        $path = $this->modelPath . 'schema';
        if (! is_dir($path)) {
            mkdir($path, $this->dirPermission, true);
        }
        $content = $this->modx->getChunk($schemaChunk);
        $fp = fopen($path . '/' . strtolower($this->ce_package_name) . '.mysql.schema.xml', 'w');
        if (!$fp) {
            $this->addError($this->modx->lexicon('ce.could_not_open_schema_file~~Could not open schema file'));
            return false;
        } else {
            fwrite($fp, $content);
            fclose($fp);
        }
        return $success;


    }

    public function dumpSchema() {
        $success = true;
        $schemaChunk = stripos($this->ce_class, 'user' !== false)
            ? 'ExtUserSchema'
            : 'ExtResourceSchema';
        $path = $this->modelPath . 'schema';
        if (!is_dir($path)) {
            mkdir($path, $this->dirPermission, true);
        }
        $content = $this->modx->getChunk($schemaChunk);
        $fp = fopen($path . '/' . strtolower($this->ce_package_name) . '.mysql.schema.xml', 'w');
        if (!$fp) {
            $success = false;
            $this->addError($this->modx->lexicon('ce.could_not_open_schema_file~~Could not open schema file'));
        } else {
            fwrite($fp, $content);
            fclose($fp);
        }

        return $success;
    }

    public function generateClassFiles() {
        /** $generator xPDOGenerator */
        $path = $this->modelPath . 'schema/';
        $this->generator->parseSchema($path . $this->ce_package_name .
            '.mysql.schema.xml', $this->modelPath);
        $this->copyDir($this->modelPath, MODX_CORE_PATH .
            'components/' . $this->ce_package_name);

    }

    public function createTables() {
        $success = $this->modx->addPackage($this->ce_package_name,
            $this->modelPath, $this->ce_table_prefix);
        if (!$success) {
            $this->addError($this->modx->lexicon("ce.addpackage_failed~~addPackage failed."));
            return;
        }
        $manager = $this->modx->getManager();
        if (!$manager) {
            $this->addError($this->modx->lexicon("ce.getmanager_failed~~getManager failed."));

            return;
        }
        $manager->createObjectContainer($this->ce_table_name);
    }

    /* This only needs to be run once, but it doesn't hurt to re-run it.
       -- Developers: note that if you're
       using MyComponent, this references the actual core path, not
       your dev. path. ClassExtender will copy the package files to
       core/components/classextender/model/ */

    public function add_extension_package() {
        $path = '[[++core_path]]' . 'components/classextender/model/';
        $this->modx->addExtensionPackage($this->ce_package_name, $path,
            array('tablePrefix' => $this->ce_table_prefix));
    }

    public function loadPackage() {
        $success = $this->modx->addPackage($this->ce_package_name,
            $this->modelPath, $this->ce_table_prefix);
        if (!$success) {
            $this->addError($this->modx->lexicon("ce.addpackage_faile~~naddPackage failed."));

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
                    if ($file == "." || $file == ".." ||
                        $file == '.git' || $file == '.svn') {
                        continue;
                    }

                    if (is_dir($source . '/' . $file)) {
                        $this->copyDir($source . '/' . $file,
                            $destination . '/' . $file);
                    } else {
                        copy($source . '/' . $file,
                            $destination . '/' . $file);
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