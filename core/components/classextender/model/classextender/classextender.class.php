<?php

/**
 * ClassExtender class file for ClassExtender extra
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
 *
 * @package classextender
 */

/* $modx->lexicon->load('classextender:default'); */

class ClassExtender {
    public modX $modx;
    public array $props;
    /* Note: MODX 3 will use the package name as
       a namespace in the classes */
    public string $ce_package_name = '';
    public string $ce_table_prefix = '';
    public $generator; /* Do not set type declaration */
    public string $modelPath;
    public int $dirPermission;
    public string $ce_schema_file = '';
    protected array $output = array();
    protected array $fields = array();
    protected string $packageLower = '';
    protected bool $hasError = false;
    protected string $schemaChunk = '';
    protected bool $isMODX3;
    protected string $classPrefix;


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

        $cssFile = $this->modx->getOption('cssFile', $this->props,
            '', true);

        if (!empty($cssFile)) {
            $this->modx->regClientCSS($cssFile);
        }

        $this->ce_table_prefix = $this->modx->getOption('tablePrefix', $this->props,
            '', true);

        if (empty($this->ce_table_prefix)) {
            $this->addOutput('' .
                $this->modx->lexicon('ce.empty_table_prefix') , true);
        }

        $this->ce_package_name = $this->modx->getOption('package', $this->props,
            '', true);

        if (empty($this->ce_package_name)) {
            $this->addOutput('' . $this->modx->lexicon('ce.package_name_is_empty'), true);
        }

        $this->dirPermission = (int)$this->modx->getOption('dirPermission',
            $this->props, 0755);

        $manager = $this->modx->getManager();
        $this->generator = $manager->getGenerator();
        if (!$this->generator) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "Could not get generator");
        }

        $modelPath = $this->modx->getOption('ce.core_path', NULL,
            $this->modx->getOption('core_path') .
            'components/classextender/model/');
        $this->modelPath = $modelPath;



        $this->packageLower = strtolower($this->ce_package_name);

        $this->schemaChunk = $this->modx->getOption('schemaTpl', $this->props, '');

        /* File to be written containing schema */
        $path = $this->modelPath . 'schema';
        if (!is_dir($path)) {
            mkdir($path, $this->dirPermission, true);
        }

        $this->ce_schema_file = $path . '/' . $this->packageLower . '.mysql.schema.xml';
    }

    public function updateSystemSetting($packageLower) {
        $ss = $this->modx->getObject(
            $this->classPrefix . 'modSystemSetting',
            array('key' => 'ce_autoload_directories')
        );
        $val = $ss->get('value');
        $finalVal = null;
        if (empty($val)) {
            $finalVal = $packageLower;
        } elseif (strpos($val, $packageLower) === false) {
            $finalVal = $packageLower . ',' . $val;
        }
        if ($finalVal !== null) {
            $ss->set('value', $finalVal);
            if (!$ss->save()) {
                $this->addOutput($this->modx->lexicon('ce.could_not_update_system_setting'), true);
                return false;
            }
        }

        return true;
    }

    public function process() {

        $this->addOutput($this->modx->lexicon('ce.generating_class_files'));

        if (empty($this->schemaChunk)) {
            $this->addOutput($this->modx->lexicon('ce.schema_chunk_is_empty'), true);
            return;
        }

        if (!$this->dumpSchema()) {
            return;
        }

        if (!$this->generateClassFiles()) {
            return;
        }

        if (!$this->createTables()) {
            return;
        }
        if (!$this->createNamespace($this->packageLower)) {
            return;
        }

        if (!$this->activatePlugin()) {
            return;
        }

        /* Move extension package to modExtensionPackage object */
        if (!$this->createExtensionPackageObject()) {
            return;
        }
        if (!$this->updateSystemSetting($this->packageLower)) {
            return;
        }
    }

    public function displayForm(): string {
        $fields = array(
            'ce_package_name' => $this->ce_package_name,
            'ce_schema_tpl_name' => $this->schemaChunk,
            'ce_table_prefix' => $this->ce_table_prefix,
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

    public function hasError(): bool {
        return $this->hasError;
    }

    public function getOutput(): string {
        return implode("\n", $this->output);
    }

    /* Create schema file from schema chunk */
    public function dumpSchema(): bool {

        $success = true;

        $this->addOutput($this->modx->lexicon('ce.saving_schema'));

        $path = $this->modelPath . 'schema';
        if (!is_dir($path)) {
            mkdir($path, $this->dirPermission, true);
        }
        $content = $this->modx->getChunk($this->schemaChunk);
        if (empty($content)) {
            $this->addOutput($this->modx->lexicon('ce.could_not_find_schema_chunk') .
                ': ' . $this->schemaChunk, true);
            return false;
        } else {
            /* Get package name from Schema */
            $pattern = '/<model.*package\s*=\s*["\']([-a-zA-Z_][^"]*)["\']/';
            preg_match($pattern, $content, $matches);
            if (!empty($matches[1])) {
                $this->ce_package_name = $matches[1];
                $this->packageLower = strtolower($this->ce_package_name);
            } else {
                $this->addOutput($this->modx->lexicon('ce.could_not_get_package_name_from_schema'), true);
            }

            /* Convert schema to MODX 3 format -- should not alter
               schemas that are already correct */
            if ($this->isMODX3) {
                $replacements = array(
                    'class="modUser"' =>
                        'class="MODX\Revolution\modUser"',
                    'class="modResource"' =>
                        'class="MODX\Revolution\modResource"',
                    'extends="modUser"' =>
                        'extends="MODX\Revolution\modUser"',
                    'extends="xPDOObject"' =>
                        'extends="xPDO\Om\xPDOObject"',
                    'extends="xPDOSimpleObject"' =>
                        'extends="xPDO\Om\xPDOSimpleObject"',
                    'extends="modResource"' =>
                        'extends="MODX\Revolution\modResource"',
                    'class="ResourceData" foreign' =>
                        'class="extendedresource\ResourceData" foreign',
                    'class="UserData" foreign' =>
                        'class="extendeduser\UserData" foreign',
                    'class="modUserProfile"' =>
                        'class="MODX\Revolution\modUserProfile"',
                );
                foreach ($replacements as $key => $value) {
                    if (strpos($content, $key) !== false) {
                        $content = str_replace($key, $value, $content);
                    }
                }


            } /* End MODX 3 Section */

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
    public function generateClassFiles(): bool {

        $dir = $this->modelPath . $this->packageLower;

        if (is_dir($dir)) {
            $this->rrmdir($dir);
            $this->addOutput(
                $this->modx->lexicon('ce.old_class_files_removed'));
        } else {
            mkdir($dir, $this->dirPermission, true);
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

    /* Returns a list of filenames of objects listed
       in the schema file. Each one will get its own DB table */
    public function getClassFiles(): array {
        $pattern = '/<object class="([^"]+)"/';
        $content = file_get_contents($this->ce_schema_file);
        preg_match_all($pattern, $content, $matches);
        if (!empty($matches[1])) {
            return $matches[1];
        } else {
            $this->addOutput(
                $this->modx->lexicon('ce.could_not_find_classes_in_schema_file'),
                true);
            return (array());
        }


    }

    public function createTables(): bool {
        $pkg = $this->packageLower;
        $dir = $this->modelPath;
        require_once $this->modelPath . 'ce_autoload.php';

        /* Required!!! */
        $success = $this->modx->addPackage($this->ce_package_name,
            $this->modelPath, $this->ce_table_prefix);

        if (!$success) {
            $this->addOutput($this->modx->lexicon('ce.add_package_failed'), true);
            $success = false;
        } else {
            $manager = $this->modx->getManager();
            if (!$manager) {
                $this->addOutput($this->modx->lexicon('ce.getmanager_failed'), true);
                $success = false;
            } else {
                $files = $this->getClassFiles();
                if (empty($files)) {
                    return false;
                }
                foreach ($files as $class) {
                    if ($this->isMODX3) {
                        /* MODX 3 will use package name as namespace
                           for all classes */
                        $finalClass = $pkg . '\\' . $class;
                    } else {
                        $finalClass = $class;
                    }
                    $success = $manager->createObjectContainer($finalClass);
                    if (!$success) {
                        $this->addOutput(
                            $this->modx->lexicon('ce.create_object_container_failed'), true);
                        return false;
                    } else {
                        $this->addOutput(
                            $this->modx->lexicon('ce.table_created') . $class);
                    }
                }
            }
        }
        return $success;
    }

    public function createNamespace($pkg): bool {

        $namespace = $this->modx->getObject($this->classPrefix . 'modNamespace', array('name' => $pkg));

        if (!$namespace) {
            $namespace = $this->modx->newObject($this->classPrefix . 'modNamespace');
            $namespace->set('name', $pkg);
            $namespace->set('path', '{core_path}' . 'components/classextender/');
            $namespace->set('assets_path' . '{assets_path}' . 'components/classextender/');
            if (!$namespace->save()) {
                $this->addOutput($this->modx->lexicon('ce.could_not_save_namespace'), true);
                return false;
            }
        }
        return true;
    }


    public function createExtensionPackageObject(): bool {

        /* Clear existing registration */
        $setting = $this->modx->getObject('modSystemSetting',
            array('key' => 'extension_packages'));
        if (!empty($setting)) {
            $this->modx->removeExtensionPackage($this->packageLower);
        }

        /* Clear existing registration (deprecated) */
        $setting = $this->modx->getObject('modSystemSetting',
            array('key' => 'extension_packages'));
        if (!empty($setting)) {
            $this->modx->removeExtensionPackage($this->packageLower);
        }

        /* If extension_packages is now empty, clear the setting
         */
        $setting = $this->modx->getObject('modSystemSetting',
            array('key' => 'extension_packages'));
        if ($setting) {
            $val = $setting->get('value');
            if (empty($val) || strlen($val) < 5) {
                $setting->set('value', "");
            }
        }
        $extensionPackage = $this->modx->getObject($this->classPrefix . 'modExtensionPackage', array('namespace' => $this->packageLower));
        if (!$extensionPackage) {
            $fields = array(
                'namespace' => $this->packageLower,
                'name' => $this->packageLower,
                'path' => '[[++core_path]]' . 'components/classextender/',
                'table_prefix' => $this->ce_table_prefix,
            );
            $extensionPackage = $this->modx->newObject($this->classPrefix . 'modExtensionPackage');
            $extensionPackage->fromArray($fields);
            if (!$extensionPackage->save()) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not save extension package object');
                return false;
            } else {
                $this->addOutput($this->modx->lexicon('ce.created_extension_package_object'));
            }
        }
        return true;
    }

    public function activatePlugin(): bool {
        $package = $this->ce_package_name;
        $plugin = '';
        if ($package === 'extendeduser') {
            $plugin = 'ExtraUserFields';
        } elseif ($package === 'extendedresource') {
            $plugin = 'ExtraResourceFields';
        } else {
            return true;
        }
        $pluginObj = $this->modx->getObject('modPlugin', array('name' => $plugin));
        if ($pluginObj) {
            $pluginObj->set('disabled', false);
            if ($pluginObj->save()) {
                $this->addOutput(
                    $this->modx->lexicon('ce.plugin_enabled')
                    . $plugin);
            } else {
                $this->addOutput($this->modx->lexicon('ce.could_not_enable') .
                    $plugin, true);
                return false;
            }
        }
        return true;
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
            rmdir($dir);
        }
    }
}
