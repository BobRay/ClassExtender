<?php

/**
 * ClassExtender class file for ClassExtender extra
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
 *
 * @package classextender
 */

/* $modx->lexicon->load('classextender:default'); */
class ClassExtender {
    public $version = '2.0.0';
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
    public $ce_schema_tpl_name = '';
    /** @var  $generator xPDOGenerator */
    public $generator;
    public $modelPath;
    public $dirPermission;
    public $ce_schema_file;
    protected $output = array();
    protected $fields = array();
    protected $packageLower = '';
    protected $objectPrefix = '';
    protected $objectPrefixLower = '';
    protected $hasError = false;
    protected $schemaChunk = '';


    function __construct(&$modx, &$config = array()) {
        $this->modx =& $modx;
        $this->props =& $config;

    }

    public function init() {
        $this->hasError = false;
        $this->output = array();

        $cssFile = $this->modx->getOption('ce.assets_url', null,
                MODX_ASSETS_URL . 'components/classextender/') . 'css/classextender.css?v=' . $this->version;

        $this->modx->regClientCSS($cssFile);

        $this->dirPermission = (int) $this->modx->getOption('dirPermission',
            $this->props, 0755);

        $manager = $this->modx->getManager();
        $this->generator = $manager->getGenerator();

        $basePath = $this->modx->getOption('ce.core_path', NULL,
            $this->modx->getOption('core_path') .
            'components/classextender/');
        $this->modelPath = $basePath . 'model/';

        $this->ce_package_name = isset($_POST['ce_package'])
            ? $_POST['ce_package']
            : $this->modx->getOption('package', $this->props, 'extendeduser');

        $this->packageLower = strtolower($this->ce_package_name);

        $this->ce_parent_object = isset($_POST['ce_parent_object'])
            ? $_POST['ce_parent_object']
            : $this->modx->getOption('parentObject', $this->props, 'modUser');

        $this->ce_class = isset($_POST['ce_class'])
            ? $_POST['ce_class']
            :$this->modx->getOption('class',
                $this->props, $this->ce_parent_object);

        /* Strip off 'mod' to produce 'User' or 'Resource' */

        $this->objectPrefix = substr($this->ce_parent_object, 3);
        $this->objectPrefixLower = strtolower($this->objectPrefix);

        $this->schemaChunk = isset($_POST['ce_schema_tpl_name'])
            ? $_POST['ce_schema_tpl_name']
            : $this->modx->getOption('schemaTpl', $this->props, '');

        $this->ce_table_prefix = isset($_POST['ce_table_prefix'])
            ? $_POST['ce_table_prefix']
            : $this->modx->getOption('tablePrefix', $this->props, 'ext_');

        $this->ce_table_name = isset($_POST['ce_table_name'])
            ? $_POST['ce_table_name']
            : $this->modx->getOption('tableName', $this->props, 'user_data');

        $this->ce_method = isset($_POST['ce_method'])
            ? $_POST['ce_method']
            : $this->modx->getOption('method', $this->props, 'use_schema');

        $this->ce_schema_file = $this->modx->getOption('schemaFile',
            $this->props, '' );

        if (empty($this->ce_schema_file)) {
            $path = $this->modelPath . 'schema';
            if (!is_dir($path)) {
                mkdir($path, $this->dirPermission, true);
            }
            $this->ce_schema_file = $path . '/' . $this->packageLower . '.mysql.schema.xml';
        }
    }

    public function process() {

        $this->addOutput($this->modx->lexicon('ce.generating_class_files'));
        if (!$this->dumpSchema()) {
            return;
        }

        if (!$this->generateClassFiles()) {
            return;
        };
        if (!$this->createTables()) {
                return;
        }

        $this->activatePlugin();

        $this->registerExtensionPackage();

    }

    public function displayForm() {
        $fields = array(
            'ce_package_name'  => $this->ce_package_name,
            'ce_class'         => $this->ce_class,
            'ce_parent_object' => $this->ce_parent_object,
            'ce_table_prefix'  => $this->ce_table_prefix,
            'ce_table_name'    => $this->ce_table_name,
            'ce_schema_tpl_name' => $this->schemaChunk,
        );

        return $this->modx->getChunk('ClassExtenderForm', $fields);
    }

    protected function addOutput($msg, $isError = false) {
        if ($isError) {
            $this->hasError = true;
            $this->output[] = '<p class="ce_error">' . $msg . '</p>';
        } else {
            $this->output[] = '<p class="ce_success">' . $msg . '</p>';
        }


    }



    public function hasError() {
        return $this->hasError;
    }

    public function getOutput() {
        return implode("\n", $this->output);
    }


    public function dumpSchema() {

        $success = true;

        $this->addOutput($this->modx->lexicon('ce.saving_schema'));

        $path = $this->modelPath . 'schema';
        if (!is_dir($path)) {
            mkdir($path, $this->dirPermission, true);
        }
        $content = $this->modx->getChunk($this->schemaChunk);
        if (! $content) {
            $this->addOutput($this->modx->lexicon('ce.could_not_find_schema_chunk') .
                ': ' . $this->schemaChunk , true);
        }

        $fp = fopen($this->ce_schema_file, 'w');

        if (!$fp) {
            $success = false;
            $this->addOutput($this->modx->lexicon('ce.could_not_open_schema_file'), true);
        } else {
            fwrite($fp, $content);
            fclose($fp);
        }

        return $success;
    }

    public function generateClassFiles() {

        $dir = $this->modelPath . $this->packageLower;

        if (is_dir($dir)) {
            $this->rrmdir($dir);
            $this->addOutput(
                 $this->modx->lexicon('ce.old_class_files_removed'));
        }
        $path = $this->ce_schema_file;
        $success = $this->generator->parseSchema($path, $this->modelPath);

        if (!$success) {
            $this->addOutput($this->modx->lexicon('ce.parse_schema_failed'), true);
        } else {
            $this->addOutput($this->modx->lexicon('ce.schema_parsed'));
        }
        return $success;

    }

    public function createTables() {
        $success = $this->modx->addPackage($this->ce_package_name,
            $this->modelPath, $this->ce_table_prefix);
        if (!$success) {
            $this->addOutput($this->modx->lexicon("ce.addpackage_failed"), true);
        } else {
            $manager = $this->modx->getManager();
            if (!$manager) {
                $this->addOutput($this->modx->lexicon("ce.getmanager_failed"), true);
                $success = false;
            } else {
                $object = $this->objectPrefixLower . 'Data';
                $success = $manager->createObjectContainer($object);
            }
        }


        if (! $success) {
            $this->addOutput(
                 $this->modx->lexicon('ce.create_object_container_failed'), true);
        } else {
            $this->addOutput($this->modx->lexicon('ce.table_created'));
        }
        return $success;
    }

    public function registerExtensionPackage() {

        $path = '[[++core_path]]' . 'components/classextender/model/';
        /* Clear existing registration so it can be updated */
        $setting = $this->modx->getObject('modSystemSetting',
            array('key' => 'extension_packages'));
        if (! empty($setting)) {
            $this->modx->removeExtensionPackage($this->packageLower);
        }
        $this->modx->addExtensionPackage($this->packageLower, $path,
            array('tablePrefix' => $this->ce_table_prefix));
        $this->addOutput($this->modx->lexicon('ce.extension_package_registered'));
    }

    public function activatePlugin() {
        $package = $this->ce_package_name;
        $plugin = '';
        if ($package === 'extendeduser') {
            $plugin = 'ExtraUserFields';
        } elseif ($package === 'extendedresource') {
            $plugin = 'ExtraResourceFields';
        }
        $pluginObj = $this->modx->getObject('modPlugin', array('name' => $plugin));
        if ($pluginObj) {
            $pluginObj->set('disabled', false);
            $pluginObj->save();
            $this->addOutput($plugin . ' ' . $this->modx->lexicon('ce.plugin_enabled'));
        }
    }


    public function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        $this->rrmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
}