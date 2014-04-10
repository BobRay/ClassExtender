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

/* $modx->lexicon->load('classextender:default'); */
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
    public $ce_register = 'ce_register_yes';
    public $ce_update_class_key = 'ce_update_class_key_no';
    public $ce_schema_file;
    /** @var  $generator xPDOGenerator */
    public $generator;
    public $modelPath;
    public $devMode = false;
    public $dirPermission;
    protected $output = array();
    protected $fields = array();
    protected $packageLower = '';
    protected $objectPrefix = '';
    protected $objectPrefixLower = '';
    protected $hasError = false;


    function __construct(&$modx, &$config = array()) {
        $this->modx =& $modx;
        $this->props =& $config;

    }

    public function init() {
        $this->hasError = false;
        $this->output = array();

        $cssFile = $this->modx->getOption('ce.assets_url', $this->props,
                MODX_ASSETS_URL . 'components/classextender/') . 'css/classextender.css';

        $this->modx->regClientCSS($cssFile);


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

        $this->ce_package_name = isset($_POST['ce_package'])
            ? $_POST['ce_package']
            : $this->modx->getOption('package', $this->props, 'extendeduser');

        $this->packageLower = strtolower($this->ce_package_name);

        $this->ce_class = isset($_POST['ce_class'])
            ? $_POST['ce_class']
            :$this->modx->getOption('class', $this->props, 'extUser');

        $this->ce_parent_object = isset($_POST['ce_parent_object'])
            ? $_POST['ce_parent_object']
            :$this->modx->getOption('parentObject', $this->props, 'modUser');

        /* Strip off 'mod' to produce 'User' or 'Resource' */

        $this->objectPrefix = substr($this->ce_parent_object, 3);
        $this->objectPrefixLower = strtolower($this->objectPrefix);

        $this->ce_table_prefix = isset($_POST['ce_table_prefix'])
            ? $_POST['ce_table_prefix']
            : $this->modx->getOption('tablePrefix', $this->props, 'ext_');

        $this->ce_table_name = isset($_POST['ce_table_name'])
            ? $_POST['ce_table_name']
            : $this->modx->getOption('tableName', $this->props, 'user_data');

        $this->ce_method = isset($_POST['ce_method'])
            ? $_POST['ce_method']
            : $this->modx->getOption('method', $this->props, 'use_schema');

        $this->ce_register = isset($_POST['ce_register'])
            ? $_POST['ce_register'] === 'ce_register_yes'
            : $this->modx->getOption('registerPackage', $this->props, true);

        $this->ce_update_class_key = isset($_POST['ce_update_class_key'])
            ? $_POST['ce_update_class_key'] === 'ce_update_class_key_yes'
            : $this->modx->getOption('updateClassKey', $this->props, false);

        $this->ce_schema_file = $this->modx->getOption('schemaFile',
            $this->props, '' );

        if (($this->ce_method !== 'use_table') && $this->ce_method !== 'use_schema') {
            $this->addOutput($this->modx->lexicon('ce.bad_method'), true);
            return;
        }
        if (empty($this->ce_schema_file)) {
            $path = $this->modelPath . 'schema';
            if (!is_dir($path)) {
                mkdir($path, $this->dirPermission, true);
            }
            $this->ce_schema_file = $path . '/' . $this->packageLower . '.mysql.schema.xml';
        }

        if ($this->ce_method == 'use_schema') {
            if (! file_exists($this->ce_schema_file)) {
                $this->addOutput($this->modx->lexicon('ce.no_schema'), true);
                return;
            }


        } else {
            $table = 'ext_' . $this->objectPrefixLower . '_data';
            $tableExists = gettype($this->modx->exec("SELECT count(*) FROM $table")) == 'integer';

            if (! $tableExists) {
                $this->addOutput($this->modx->lexicon('ce.no_table') .
                    $table, true);
                return;
            }
        }

    }

    public function process() {

       // $this->displayForm($fields);


        if ($this->ce_method == 'use_table') {
           if(! $this->generateSchema()) {
               return;
           }
        } else {
            if (!$this->dumpSchema()) {
                return;
            }
        }
        $this->addOutput($this->modx->lexicon('ce.generating_class_files'));
        if (!$this->generateClassFiles()) {
            return;
        };

        if ($this->ce_method == 'use_schema') {
            if (!$this->createTables()) {
                return;
            }
        }

        if ($this->ce_register) {
            $this->addExtensionPackage();
        }

        if ($this->ce_update_class_key) {
            $this->addOutput($this->modx->lexicon('ce.updating_class_key'));
            $this->updateClassKey();

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
        if ($this->ce_register) {
            $fields['ce_register_yes_checked'] = 'checked="checked"';
        } else {
            $fields['ce_register_no_checked'] = 'checked="checked"';
        }
        if ($this->ce_update_class_key) {
            $fields['ce_update_class_key_yes_checked'] = 'checked="checked"';
        } else {
            $fields['ce_update_class_key_no_checked'] = 'checked="checked"';
        }
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


    public function generateSchema() {

        $this->addOutput("ce.generating_schema");
        $path = $this->modelPath . 'schema';
        if (!is_dir($path)) {
            mkdir($path, $this->dirPermission, true);
        }
        $file = $this->ce_schema_file;

        if (file_exists($file)) {
            if (unlink($file)) {
                $this->addOutput($this->modx->lexicon('ce.deleting_schema'));
            }
        }

        $fakePrefix = substr($this->ce_table_prefix . $this->ce_table_name, 0, -1);
        $success = $this->generator->writeSchema($file, $this->ce_package_name,
            $this->ce_parent_object, $fakePrefix, true);

        $content = file_get_contents($file);

        $prefix = $this->ce_table_prefix;
        $objectPrefix = $this->objectPrefix;
        $objectPrefixLower = $this->objectPrefixLower;

        $pattern = '/baseClass="[a-zA-Z]+"/';
        $replace = 'baseClass="xPDOObject" tablePrefix="' . $prefix . '"';
        $content = preg_replace($pattern, $replace, $content);

        $pattern = '/<object class=.+?>/';
        $replace = "<object class=\"{$objectPrefixLower}Data\" table=\"{$objectPrefixLower}_data\" extends=\"xPDOSimpleObject\">";
        $content = preg_replace($pattern, $replace, $content);

        $extObject = "\t<object class=\"ext{$objectPrefix}\" extends=\"mod{$objectPrefix}\">
        <composite alias=\"Data\" local=\"id\" class=\"{$objectPrefixLower}Data\" foreign=\"{$objectPrefixLower}data_id\"
         cardinality=\"one\"
         owner=\"local\"/>
    </object>";

        $content = str_replace("\t<object", $extObject . "\n\t<object", $content);

        $pattern = "#<index.*</index>#s";
        $replace = "<index alias=\"{$objectPrefixLower}data_id\" name=\"{$objectPrefixLower}data_id\" primary=\"false\" unique=\"true\" type=\"BTREE\">
            <column key=\"{$objectPrefixLower}data_id\" length=\"\" collation=\"A\" null=\"false\"/>
        </index>
        <aggregate alias=\"{$objectPrefix}\" class= \"mod{$objectPrefix}\" local=\"{$objectPrefixLower}data_id\" foreign=\"id\" cardinality=\"one\" owner=\"foreign\"/>";

        $content = preg_replace($pattern, $replace, $content);

        $fp = fopen($file, 'w');
        if ($fp) {
            $success = fwrite($fp, $content);
            fclose($fp);
        } else {
            $this->addOutput($this->modx->lexicon("ce.no_file_write")
                . $file, true);
        }


        if ($success) {
            $this->addOutput($this->modx->lexicon('ce.new_schema_written'));
        } else {
            $this->addOutput($this->modx->lexicon('ce.error_writing_schema_file'), true);
            $success = false;
        }

        return $success;


    }

    public function dumpSchema() {

        $success = true;
        // echo "<br>PackageLower: " . $this->packageLower;
        $schemaChunk = 'Ext' . $this->objectPrefix . 'Schema';
        $this->addOutput($this->modx->lexicon('ce.saving_schema'));

        $path = $this->modelPath . 'schema';
        if (!is_dir($path)) {
            mkdir($path, $this->dirPermission, true);
        }
        $content = $this->modx->getChunk($schemaChunk);

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
            $classFile = $this->modelPath . $this->packageLower . '/' . $this->ce_class . '.class.php';
            $inserts = $this->getConstructorText();

            if ($this->objectPrefix === 'Resource') {
                $inserts .= $this->getResourceOverrides();
                /*$this->createControllers();*/
            }



            if (file_exists($classFile)) {
                $content = file_get_contents($classFile);
                /* don't add if we've done it already */
                if (strpos($content, 'construct') === false) {
                    $fp = fopen($classFile, 'w');
                    if ($fp) {
                        $content = str_replace('}', $inserts . "\n}", $content);
                        fwrite($fp, $content);
                        fclose($fp);
                    }
                }
            }
        }
        return $success;

    }

    /*public function createControllers() {

    }*/
    public function getConstructorText() {
       return  "\n
    function __construct(xPDO & \$xpdo) {
        parent::__construct(\$xpdo);
        \$this->set('class_key', '" . $this->ce_class . "');
        \$this->showInContextMenu = true;
    }\n";

    }

    public function getResourceOverrides() {

        $overRides = "\n    public static function getControllerPath(xPDO &\$modx) {
            return \$modx->getOption('ce.core_path', NULL, \$modx->getOption('core_path') .
             'components/classextender/') . 'controllers/';\n
    }\n";

    $overRides .= "\n    public function getContextMenuText() {
        \$this->xpdo->lexicon->load('classextender:default');
        return array(
            'text_create' => \$this->xpdo->lexicon('ce.extResource'),
            'text_create_here' => \$this->xpdo->lexicon('ce.ext_resource_create_here'),
        );
    }\n";

    $overRides .= "\n    public function getResourceTypeName() {
        \$this->xpdo->lexicon->load('classextender:default');
        return \$this->xpdo->lexicon('ce.extResource');
    }\n";

    return $overRides;


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

    public function addExtensionPackage() {

        $path = '[[++core_path]]' . 'components/classextender/model/';
        $this->modx->addExtensionPackage($this->packageLower, $path,
            array('tablePrefix' => $this->ce_table_prefix));
        $this->addOutput($this->modx->lexicon('ce.extension_package_registered'));
    }

    public function updateClassKey() {
        switch($this->objectPrefix) {
            case 'Resource':
                $this->modx->updateCollection(
                     'modResource',
                         array('class_key' => $this->ce_class),
                         array('class_key' => 'modDocument')
                );
            break;

            case 'User':
                $this->modx->updateCollection(
                    'modUser',
                       array('class_key' => $this->ce_class),
                       array('class_key' => 'modUser')
                );
        }
        $this->addOutput($this->modx->lexicon('ce.class_key_updated'));
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