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
    public $ce_schema_file;
    /** @var  $generator xPDOGenerator */
    public $generator;
    public $modelPath;
    public $devMode = false;
    public $dirPermission;
    protected $output = '';
    protected $errors = array();
    protected $fields = array();
    protected $packageLower = '';
    protected $objectPrefix = '';
    protected $objectPrefixLower = '';


    function __construct(&$modx, &$config = array()) {
        $this->modx =& $modx;
        $this->props =& $config;

    }

    public function init() {
        $this->output = '';
        $this->dirPermission = (int) $this->modx->getOption('dirPermission',
            $this->props, 0755);

            $manager = $this->modx->getManager();
            $this->generator = $manager->getGenerator();

            $corePath = $this->modx->getOption('ce.core_path', NULL, NULL);


            if (!empty($corePath)) {
                $this->devMode = true;
            }

            $basePath = $this->modx->getOption('ce.core_path', NULL,
                $this->modx->getOption('core_path') .
                'components/classextender/');
            $this->modelPath = $basePath . 'model/';

        // *******************
           // echo '<br>NOT POSTED<br>';
            $this->ce_package_name = isset($_POST['package'])
                ? $_POST['package']
                : $this->modx->getOption('package', $this->props, 'extendeduser');

            $this->packageLower = strtolower($this->ce_package_name);

            $this->ce_class = isset($_POST['class'])
                ? $_POST['class']
                :$this->modx->getOption('class', $this->props, 'extUser');

            $this->ce_parent_object = isset($_POST['parentObject'])
                ? $_POST['parentObject']
                :$this->modx->getOption('parentObject', $this->props, 'modUser');

            /* Strip off 'mod' to produce 'User' or 'Resource' */

            $this->objectPrefix = substr($this->ce_parent_object, 3);
            $this->objectPrefixLower = strtolower($this->objectPrefix);

            $this->ce_table_prefix = isset($_POST['tablePrefix'])
                ? $_POST['tablePrefix']
                : $this->modx->getOption('tablePrefix', $this->props, 'ext_');

            $this->ce_table_name = isset($_POST['tableName'])
                ? $_POST['tableName']
                : $this->modx->getOption('tableName', $this->props, 'user_data');

            $this->ce_method = isset($_POST['method'])
                ? $_POST['method']
                : $this->modx->getOption('method', $this->props, 'use_schema');

            $this->ce_register = isset($_POST['registerPackage'])
                ? $_POST['registerPackage']
                : $this->modx->getOption('registerPackage', $this->props, 'Yes');

            $this->ce_schema_file = $this->modx->getOption('schemaFile',
                $this->props, '' );

            if (($this->ce_method !== 'use_table') && $this->ce_method !== 'use_schema') {
                $this->addError($this->modx->lexicon('ce.bad_method~~Invalid Method (must be use_table or use_schema'));
                return;
            }
            if (empty($this->ce_schema_file)) {
                $path = $this->modelPath . 'schema';
                if (!is_dir($path)) {
                    mkdir($path, $this->dirPermission, true);
                }
                $this->ce_schema_file = $path . '/' . $this->packageLower . '.mysql.schema.xml';
            }

    }

    public function process() {

       // $this->displayForm($fields);


        if ($this->ce_method == 'use_table') {
            echo '<br>Generating Schema';
            $this->generateSchema();
        } else {
            $this->dumpSchema();
        }

        $this->generateClassFiles();
            echo '<br>generating Class files';
        if ($this->ce_method == 'use_schema') {
            $this->createTables();
        }

        if ($this->ce_register) {
            echo '<br>registering extension package';
            $this->addExtensionPackage();
        }
    }

    public function displayForm() {
        $fields = array(
            'ce_package_name'  => $this->ce_package_name,
            'ce_class'         => $this->ce_class,
            'ce_parent_object' => $this->ce_parent_object,
            'ce_table_prefix'  => $this->ce_table_prefix,
            'ce_table_name'    => $this->ce_table_name,
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
        return $this->modx->getChunk('ClassExtenderForm', $fields);
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

        echo "<br>Generating Schema File from Table";
        $success = true;
        $path = $this->modelPath . 'schema';
        if (!is_dir($path)) {
            mkdir($path, $this->dirPermission, true);
        }
        $file = $this->ce_schema_file;

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

        return $success;


    }

    public function dumpSchema() {

        $success = true;
        echo "<br>PackageLower: " . $this->packageLower;
        $schemaChunk = 'Ext' . $this->objectPrefix . 'Schema';
        /*$schemaChunk = strpos($this->packageLower, 'user') !== false
            ? 'ExtUserSchema'
            : 'ExtResourceSchema';*/

        echo '<br>saving schema chunk(' . $schemaChunk . ' to file ' . $this->ce_schema_file;

        $path = $this->modelPath . 'schema';
        if (!is_dir($path)) {
            mkdir($path, $this->dirPermission, true);
        }
        $content = $this->modx->getChunk($schemaChunk);

        $fp = fopen($this->ce_schema_file, 'w');

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
        $manager = $this->modx->getManager();
        /** @var @var $generator xPDOGenerator */

        $path = $this->ce_schema_file;
        $success = $this->generator->parseSchema($path, $this->modelPath);

        if (!$success) {
            $this->addError($this->modx->lexicon('ce.parse_schema_failed~~parseSchema() failed'));
        } else {
            $classFile = $this->modelPath . $this->packageLower . '/' . $this->ce_class . '.class.php';
            $constructor = "
    function __construct(xPDO & \$xpdo) {
        parent::__construct(\$xpdo);
        \$this->set('class_key', '" . $this->ce_class . "');
    }";
            if (file_exists($classFile)) {
                $content = file_get_contents($classFile);
                if (strpos($content, 'constructor') === false) {
                    $fp = fopen($classFile, 'w');
                    if ($fp) {
                        $content = str_replace('}', $constructor . "\n}", $content);
                        fwrite($fp, $content);
                        fclose($fp);
                    }
                }
            }
        }
        return $success;

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
        $object = $this->objectPrefixLower . 'Data';
        /*$object = strpos($this->packageLower, 'user') !== false? 'userData' : 'resourceData';*/
        $success = $manager->createObjectContainer($object);

        if (!  $success) {
            $this->addError($this->modx->lexicon('ce.create_object_container_failed~~createObjectContainer() failed'));
        }
    }

    public function addExtensionPackage() {

        $path = '[[++core_path]]' . 'components/classextender/model/' . $this->packageLower . '/';
        $this->modx->addExtensionPackage($this->packageLower, $path,
            array('tablePrefix' => $this->ce_table_prefix));
    }

    public function loadPackage() {
        $success = $this->modx->addPackage($this->ce_package_name,
            $this->modelPath, $this->ce_table_prefix);
        if (!$success) {
            $this->addError($this->modx->lexicon("ce.addpackage_failed~~addPackage failed."));

            return;
        }
    }

}