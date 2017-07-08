<?php
/**
 * plugins transport file for ClassExtender extra
 *
 * Copyright 2012-2017 Bob Ray <https://bobsguides.com>
 * Created on 12-10-2013
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
/* @var xPDOObject[] $plugins */


$plugins = array();

$plugins[1] = $modx->newObject('modPlugin');
$plugins[1]->fromArray(array (
  'id' => 1,
  'property_preprocess' => false,
  'name' => 'ExtraResourceFields',
  'description' => 'Add and process extra fields in Create/Edit Resource form',
  'properties' => 
  array (
  ),
  'disabled' => true,
), '', true, true);
$plugins[1]->setContent(file_get_contents($sources['source_core'] . '/elements/plugins/extraresourcefields.plugin.php'));

$plugins[2] = $modx->newObject('modPlugin');
$plugins[2]->fromArray(array (
  'id' => 2,
  'property_preprocess' => false,
  'name' => 'ExtraUserFields',
  'description' => 'Add and process extra fields in Create/Edit User form',
  'properties' => NULL,
  'disabled' => true,
), '', true, true);
$plugins[2]->setContent(file_get_contents($sources['source_core'] . '/elements/plugins/extrauserfields.plugin.php'));

return $plugins;
