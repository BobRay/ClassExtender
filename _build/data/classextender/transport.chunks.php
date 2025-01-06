<?php
/**
 * chunks transport file for ClassExtender extra
 *
 * Copyright 2012-2025 Bob Ray <https://bobsguides.com>
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
/* @var xPDOObject[] $chunks */


$chunks = array();

$chunks[1] = $modx->newObject('modChunk');
$chunks[1]->fromArray(array (
  'id' => 1,
  'property_preprocess' => false,
  'name' => 'ExtUserSchema',
  'description' => 'Chunk',
  'properties' => NULL,
), '', true, true);
$chunks[1]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/extuserschema.chunk.xml'));

$chunks[2] = $modx->newObject('modChunk');
$chunks[2]->fromArray(array (
  'id' => 2,
  'property_preprocess' => false,
  'name' => 'ExtResourceSchema',
  'description' => 'Chunk',
  'properties' => NULL,
), '', true, true);
$chunks[2]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/extresourceschema.chunk.xml'));

$chunks[3] = $modx->newObject('modChunk');
$chunks[3]->fromArray(array (
  'id' => 3,
  'property_preprocess' => false,
  'name' => 'ExtUserRegisterFormTpl',
  'description' => 'Chunk',
  'properties' => NULL,
), '', true, true);
$chunks[3]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/extuserregisterformtpl.chunk.html'));

$chunks[4] = $modx->newObject('modChunk');
$chunks[4]->fromArray(array (
  'id' => 4,
  'property_preprocess' => false,
  'name' => 'ClassExtenderForm',
  'description' => 'Chunk',
  'properties' =>
  array (
  ),
), '', true, true);
$chunks[4]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/classextenderform.chunk.html'));

$chunks[5] = $modx->newObject('modChunk');
$chunks[5]->fromArray(array (
  'id' => 5,
  'property_preprocess' => false,
  'name' => 'ExtraUserFields',
  'description' => 'Chunk',
  'properties' => NULL,
), '', true, true);
$chunks[5]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/extrauserfields.chunk.html'));

$chunks[6] = $modx->newObject('modChunk');
$chunks[6]->fromArray(array (
  'id' => 6,
  'property_preprocess' => false,
  'name' => 'ExtraResourceFields',
  'description' => 'Chunk',
  'properties' => NULL,
), '', true, true);
$chunks[6]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/extraresourcefields.chunk.html'));

$chunks[7] = $modx->newObject('modChunk');
$chunks[7]->fromArray(array (
  'id' => 7,
  'property_preprocess' => false,
  'name' => 'ExtUserOuterTpl',
  'description' => 'Chunk',
  'properties' => NULL,
), '', true, true);
$chunks[7]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/extuseroutertpl.chunk.html'));

$chunks[8] = $modx->newObject('modChunk');
$chunks[8]->fromArray(array (
  'id' => 8,
  'property_preprocess' => false,
  'name' => 'ExtUserInnerTpl',
  'description' => 'Chunk',
  'properties' => NULL,
), '', true, true);
$chunks[8]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/extuserinnertpl.chunk.html'));

$chunks[9] = $modx->newObject('modChunk');
$chunks[9]->fromArray(array (
  'id' => 9,
  'property_preprocess' => false,
  'name' => 'ExtUserRowTpl',
  'description' => 'Chunk',
  'properties' => NULL,
), '', true, true);
$chunks[9]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/extuserrowtpl.chunk.html'));

$chunks[10] = $modx->newObject('modChunk');
$chunks[10]->fromArray(array (
  'id' => 10,
  'property_preprocess' => false,
  'name' => 'ExtResourceOuterTpl',
  'description' => 'Chunk',
  'properties' => NULL,
), '', true, true);
$chunks[10]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/extresourceoutertpl.chunk.html'));

$chunks[11] = $modx->newObject('modChunk');
$chunks[11]->fromArray(array (
  'id' => 11,
  'property_preprocess' => false,
  'name' => 'ExtResourceInnerTpl',
  'description' => 'Chunk',
  'properties' => NULL,
), '', true, true);
$chunks[11]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/extresourceinnertpl.chunk.html'));

$chunks[12] = $modx->newObject('modChunk');
$chunks[12]->fromArray(array (
  'id' => 12,
  'property_preprocess' => false,
  'name' => 'ExtResourceRowTpl',
  'description' => 'Chunk',
  'properties' => NULL,
), '', true, true);
$chunks[12]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/extresourcerowtpl.chunk.html'));

$chunks[13] = $modx->newObject('modChunk');
$chunks[13]->fromArray(array (
  'id' => 13,
  'property_preprocess' => false,
  'name' => 'ExtUserSearchFormTpl',
  'description' => 'Chunk',
  'properties' => NULL,
), '', true, true);
$chunks[13]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/extusersearchformtpl.chunk.html'));

$chunks[14] = $modx->newObject('modChunk');
$chunks[14]->fromArray(array (
  'id' => 14,
  'property_preprocess' => false,
  'name' => 'ExtUpdateProfileLoggedIn',
  'description' => 'Chunk',
  'properties' => NULL,
), '', true, true);
$chunks[14]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/extupdateprofileloggedin.chunk.html'));

$chunks[15] = $modx->newObject('modChunk');
$chunks[15]->fromArray(array (
  'id' => 15,
  'property_preprocess' => false,
  'name' => 'ExtUpdateProfileNotLoggedIn',
  'description' => 'Chunk',
  'properties' => NULL,
), '', true, true);
$chunks[15]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/extupdateprofilenotloggedin.chunk.html'));

$chunks[16] = $modx->newObject('modChunk');
$chunks[16]->fromArray(array (
  'id' => 16,
  'property_preprocess' => false,
  'name' => 'CreateSchemaForm',
  'description' => 'Form for Create Schema to use in creating a schema from an existing DB table',
  'properties' =>
  array (
  ),
), '', true, true);
$chunks[16]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/createschemaform.chunk.html'));

return $chunks;
