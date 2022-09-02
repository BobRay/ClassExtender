<?php

/**
 * ClassExtender class file for ClassExtender extra
 *
 * Copyright 2012-2022 Bob Ray <https://bobsguides.com>
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
    /* Note: MODX 3 will use the package name as
       a namespace in the classes */
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
    protected $isMODX3;
    protected $classPrefix;


    function __construct(&$modx, &$config = array()) {
        $this->modx =& $modx;
        $this->props =& $config;

    }

    public function init() {
        $this->hasError = false;
        $this->output = array();
        $this->isMODX3 = $this->modx->getVersionData()['version'] >= 3;
        $this->classPrefix = $this->isMODX3
            ? 'MODX\Revolution\\'
            : '';

        $cssFile = $this->modx->getOption('ce.assets_url', null,
                MODX_ASSETS_URL . 'components/classextender/') . 'css/classextender.css?v=' . $this->version;

        $this->modx->regClientCSS($cssFile);

        $this->dirPermission = (int) $this->modx->getOption('dirPermission',
            $this->props, 0755);

        $manager = $this->modx->getManager();
        $this->generator = $manager->getGenerator();
        if (! $this->generator) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "Could not get generator");
        }

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
            : $this->modx->getOption('parentObject', $this->props, $this->classPrefix . 'modUser');

        $this->ce_class = isset($_POST['ce_class'])
            ? $_POST['ce_class']
            :$this->modx->getOption('class',
                $this->props, 'userData');

        /* Strip off 'mod' to produce 'User' or 'Resource' */
     $this->objectPrefix = substr(basename($this->ce_parent_object), 3);
     $this->objectPrefixLower = strtolower($this->objectPrefix);

        $this->schemaChunk = isset($_POST['ce_schema_tpl_name'])
            ? $_POST['ce_schema_tpl_name']
            : $this->modx->getOption('schemaTpl', $this->props, 'MyExtUserSchema');

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

        /* Move extension package to modExtensionPackage object */
        $this->createExtensionPackageObject();
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

    /* Create schema file from schema chunk */
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
        } else {

            /* Convert schema to MODX 3 format -- should not alter
               schemas that are already correct */
            if ($this->isMODX3) {
                $replacements = array(
                    'extends="modUser"' => 'extends="MODX\Revolution\modUser"',
                    'extends="xPDOObject"' => 'extends="xPDO\Om\xPDOObject"',
                    'extends="xPDOSimpleObject"' =>
                        'extends="xPDO\Om\xPDOSimpleObject"',
                    'extends="modResource"' =>
                        'extends="MODX\Revolution\modResource"',
                );
                foreach ($replacements as $key => $value) {
                    if (strpos($content, $key) !== false) {
                        $content = str_replace($key, $value, $content);
                    }
                }
            }

            $fp = fopen($this->ce_schema_file, 'w');

            if (!$fp) {
                $success = false;
                $this->addOutput($this->modx->lexicon('ce.could_not_open_schema_file'), true);
            } else {
                fwrite($fp, $content);
                fclose($fp);
            }
        }
        return $success;
    }

    /* Use xPDO generator to parse schema and write
       class files to disk */
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
        $pkg = $this->packageLower;
        $dir = $this->modelPath;
        require_once $this->modelPath . 'ce_autoload.php';

        /* Required!!! */
       //  $this->modx->log(modX::LOG_LEVEL_ERROR, 'Package: ' . $this->ce_package_name);
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
                $class = $this->objectPrefixLower . 'Data';
                if ($this->isMODX3) {
                    /* MODX 3 will use package name as namespace
                       for all classes */
                    $finalClass = $pkg . '\\' . $class;
                } else {
                    $finalClass = $class;
                }
                $success = $manager->createObjectContainer($finalClass);
            }
        }

        if (! $success) {
            $this->addOutput(
                 $this->modx->lexicon('ce.create_object_container_failed'), true);

        } else {
            $this->addOutput($this->modx->lexicon('ce.table_created'));
            /* Add modExtensionPackage object to DB */
            $extensionPackage = $this->modx->getObject($this->classPrefix . 'modExtensionPackage', array('namespace' => $pkg));
            $namespace = $this->modx->getObject($this->classPrefix . 'modNamespace', array('name' => $pkg));

            if (! $namespace) {
                $namespace = $this->modx->newObject($this->classPrefix . 'modNamespace');
                $namespace->set('name', $pkg);
                $namespace->set('path', '{core_path}' . 'components/classextender/');
                $namespace->set('assets_path' . '{assets_path}' . 'components/classextender/');
                if (! $namespace->save()) {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not save namespace');
                }
            }

            if (!$extensionPackage) {
                $fields = array(
                    'namespace' => $pkg,
                    'name' => $pkg,
                    'path' => '[[++core_path]]' . 'components/classextender/',
                    'table_prefix' => $this->ce_table_prefix,
                );
                $extensionPackage = $this->modx->newObject($this->classPrefix . 'modExtensionPackage');
                $extensionPackage->fromArray($fields);
                if (!$extensionPackage->save()) {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not save extension package object');
                } else {
                    $this->addOutput('Created extension package object');
                }
            }
        }
        return $success;
    }


    public function CreateExtensionPackageObject() {

       /* Clear existing registration */
        $setting = $this->modx->getObject('modSystemSetting',
            array('key' => 'extension_packages'));
        if (! empty($setting)) {
            $this->modx->removeExtensionPackage($this->packageLower);
        }
        /* If extension_packages is now empty, remove the setting
           (it's deprecated) */
        $setting = $this->modx->getObject('modSystemSetting',
            array('key' => 'extension_packages'));
        if ($setting) {
            $val = $setting->get('value');
            if (empty($val) || strlen($val) < 5) {
                $setting->remove();
            }
        }
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

    /* Called to remove package directory (e.g., extendeduser)
       before parsing and writing class files */
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