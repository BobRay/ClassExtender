<?php
/**
 * systemSettings transport file for ClassExtender extra
 *
 * Copyright 2012-2019 Bob Ray <https://bobsguides.com>
 * Created on 08-27-2022
 *
 * @package classextender
 * @subpackage build
 */

if (! function_exists('stripPhpTags')) {
    function stripPhpTags($filename) {
        $o = file_get_contents($filename);
        $o = str_replace('<' . '?' . 'php', '', $o);
        $o = str_replace('?>', '', $o);
        $o = trim($o);
        return $o;
    }
}
/* @var $modx modX */
/* @var $sources array */
/* @var xPDOObject[] $systemSettings */


$systemSettings = array();

$systemSettings[1] = $modx->newObject('modSystemSetting');
$systemSettings[1]->fromArray(array (
  'key' => 'ce_autoload_directories',
  'value' => 'extendeduser,extendedresource',
  'xtype' => 'textfield',
  'namespace' => 'classextender',
  'area' => 'classextender',
  'name' => 'setting_ce_autoload_directories',
  'description' => 'setting_ce_autoload_directories_desc',
), '', true, true);
return $systemSettings;
