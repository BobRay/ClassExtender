<?php
/**
 * snippets transport file for ClassExtender extra
 *
 * Copyright 2012-2022 Bob Ray <https://bobsguides.com>
 * Created on 11-10-2013
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
/* @var xPDOObject[] $snippets */


$snippets = array();

$snippets[1] = $modx->newObject('modSnippet');
$snippets[1]->fromArray(array (
  'id' => 1,
  'property_preprocess' => false,
  'name' => 'GetExtUsers',
  'description' => 'Show user information',
), '', true, true);
$snippets[1]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/getextusers.snippet.php'));


$properties = include $sources['data'].'properties/properties.getextusers.snippet.php';
$snippets[1]->setProperties($properties);
unset($properties);

$snippets[2] = $modx->newObject('modSnippet');
$snippets[2]->fromArray(array (
  'id' => 2,
  'property_preprocess' => false,
  'name' => 'GetExtResources',
  'description' => 'Show resource information',
), '', true, true);
$snippets[2]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/getextresources.snippet.php'));


$properties = include $sources['data'].'properties/properties.getextresources.snippet.php';
$snippets[2]->setProperties($properties);
unset($properties);

$snippets[3] = $modx->newObject('modSnippet');
$snippets[3]->fromArray(array (
  'id' => 3,
  'property_preprocess' => false,
  'name' => 'SetUserPlaceholders',
  'description' => 'Set placeholders for extra user fields',
), '', true, true);
$snippets[3]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/setuserplaceholders.snippet.php'));


$properties = include $sources['data'].'properties/properties.setuserplaceholders.snippet.php';
$snippets[3]->setProperties($properties);
unset($properties);

$snippets[4] = $modx->newObject('modSnippet');
$snippets[4]->fromArray(array (
  'id' => 4,
  'property_preprocess' => false,
  'name' => 'SetResourcePlaceholders',
  'description' => 'Set placeholders for extra resource fields',
), '', true, true);
$snippets[4]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/setresourceplaceholders.snippet.php'));


$properties = include $sources['data'].'properties/properties.setresourceplaceholders.snippet.php';
$snippets[4]->setProperties($properties);
unset($properties);

$snippets[5] = $modx->newObject('modSnippet');
$snippets[5]->fromArray(array (
  'id' => 5,
  'property_preprocess' => false,
  'name' => 'ExtUserUpdateProfile',
  'description' => 'Set placeholders for and update extended user data',
  'properties' => NULL,
), '', true, true);
$snippets[5]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/extuserupdateprofile.snippet.php'));

$snippets[6] = $modx->newObject('modSnippet');
$snippets[6]->fromArray(array (
  'id' => 6,
  'property_preprocess' => false,
  'name' => 'UserSearchForm',
  'description' => 'Show users selected by category',
), '', true, true);
$snippets[6]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/usersearchform.snippet.php'));


$properties = include $sources['data'].'properties/properties.usersearchform.snippet.php';
$snippets[6]->setProperties($properties);
unset($properties);

$snippets[7] = $modx->newObject('modSnippet');
$snippets[7]->fromArray(array (
  'id' => 7,
  'property_preprocess' => false,
  'name' => 'ClassExtender',
  'description' => 'Extend a MODX class',
), '', true, true);
$snippets[7]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/classextender.snippet.php'));


$properties = include $sources['data'].'properties/properties.classextender.snippet.php';
$snippets[7]->setProperties($properties);
unset($properties);

$snippets[8] = $modx->newObject('modSnippet');
$snippets[8]->fromArray(array (
  'id' => 8,
  'property_preprocess' => false,
  'name' => 'ExtUserRegisterPosthook',
  'description' => 'Update extended user data on registration',
  'properties' => NULL,
), '', true, true);
$snippets[8]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/extuserregisterposthook.snippet.php'));

return $snippets;
