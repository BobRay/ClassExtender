<?php
/**
 * resources transport file for ClassExtender extra
 *
 * Copyright 2012-2023 Bob Ray <https://bobsguides.com>
 * Created on 11-18-2013
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
/* @var xPDOObject[] $resources */


$resources = array();

$resources[1] = $modx->newObject('modResource');
$resources[1]->fromArray(array (
  'id' => 1,
  'type' => 'document',
  'contentType' => 'text/html',
  'pagetitle' => 'Extend modUser',
  'longtitle' => '',
  'description' => '',
  'alias' => 'extend-moduser',
  'alias_visible' => true,
  'link_attributes' => '',
  'published' => false,
  'isfolder' => false,
  'introtext' => '',
  'richtext' => false,
  'template' => 'default',
  'menuindex' => 0,
  'searchable' => true,
  'cacheable' => true,
  'createdby' => 1,
  'editedby' => 1,
  'deleted' => false,
  'deletedon' => 0,
  'deletedby' => 0,
  'menutitle' => '',
  'donthit' => false,
  'privateweb' => false,
  'privatemgr' => false,
  'content_dispo' => 0,
  'hidemenu' => false,
  'context_key' => 'web',
  'content_type' => 1,
  'hide_children_in_tree' => 0,
  'show_in_tree' => 1,
  'properties' => NULL,
), '', true, true);
$resources[1]->setContent(file_get_contents($sources['data'].'resources/extend_moduser.content.html'));

$resources[2] = $modx->newObject('modResource');
$resources[2]->fromArray(array (
  'id' => 2,
  'type' => 'document',
  'contentType' => 'text/html',
  'pagetitle' => 'Extend modResource',
  'longtitle' => '',
  'description' => '',
  'alias' => 'extend-modresource',
  'alias_visible' => true,
  'link_attributes' => '',
  'published' => false,
  'isfolder' => false,
  'introtext' => '',
  'richtext' => false,
  'template' => 'default',
  'menuindex' => 1,
  'searchable' => true,
  'cacheable' => true,
  'createdby' => 1,
  'editedby' => 1,
  'deleted' => false,
  'deletedon' => 0,
  'deletedby' => 0,
  'menutitle' => '',
  'donthit' => false,
  'privateweb' => false,
  'privatemgr' => false,
  'content_dispo' => 0,
  'hidemenu' => false,
  'context_key' => 'web',
  'content_type' => 1,
  'hide_children_in_tree' => 0,
  'show_in_tree' => 1,
  'properties' => NULL,
), '', true, true);
$resources[2]->setContent(file_get_contents($sources['data'].'resources/extend_modresource.content.html'));

$resources[3] = $modx->newObject('modResource');
$resources[3]->fromArray(array (
  'id' => 3,
  'type' => 'document',
  'contentType' => 'text/html',
  'pagetitle' => 'ClassExtender',
  'longtitle' => '',
  'description' => '',
  'alias' => 'class-extender',
  'alias_visible' => true,
  'link_attributes' => '',
  'published' => false,
  'isfolder' => true,
  'introtext' => NULL,
  'richtext' => false,
  'template' => 'default',
  'menuindex' => 67,
  'searchable' => true,
  'cacheable' => true,
  'createdby' => 1,
  'editedby' => 0,
  'deleted' => false,
  'deletedon' => 0,
  'deletedby' => 0,
  'menutitle' => '',
  'donthit' => false,
  'privateweb' => false,
  'privatemgr' => false,
  'content_dispo' => 0,
  'hidemenu' => false,
  'context_key' => 'web',
  'content_type' => 1,
  'hide_children_in_tree' => 0,
  'show_in_tree' => 1,
  'properties' => NULL,
), '', true, true);
$resources[3]->setContent(file_get_contents($sources['data'].'resources/classextender.content.html'));

$resources[4] = $modx->newObject('modResource');
$resources[4]->fromArray(array (
  'id' => 4,
  'type' => 'document',
  'contentType' => 'text/html',
  'pagetitle' => 'Create Schema',
  'longtitle' => 'CreateSchema',
  'description' => 'Creates a schema based on an existing DB table. Writes the schema to a file and to a chunk specified in the snippet properties.',
  'alias' => 'createschema',
  'alias_visible' => true,
  'link_attributes' => '',
  'published' => false,
  'isfolder' => false,
  'introtext' => '',
  'richtext' => false,
  'template' => 'default',
  'menuindex' => 2,
  'searchable' => true,
  'cacheable' => true,
  'createdby' => 1,
  'editedby' => 1,
  'deleted' => false,
  'deletedon' => 0,
  'deletedby' => 0,
  'menutitle' => '',
  'donthit' => false,
  'privateweb' => false,
  'privatemgr' => false,
  'content_dispo' => 0,
  'hidemenu' => false,
  'context_key' => 'web',
  'content_type' => 1,
  'hide_children_in_tree' => 0,
  'show_in_tree' => 1,
  'properties' => NULL,
), '', true, true);
$resources[4]->setContent(file_get_contents($sources['data'].'resources/create_schema.content.html'));

$resources[5] = $modx->newObject('modResource');
$resources[5]->fromArray(array (
  'id' => 5,
  'type' => 'document',
  'contentType' => 'text/html',
  'pagetitle' => 'Examples',
  'longtitle' => '',
  'description' => '',
  'alias' => 'examples',
  'alias_visible' => true,
  'link_attributes' => '',
  'published' => false,
  'isfolder' => true,
  'introtext' => NULL,
  'richtext' => false,
  'template' => 'default',
  'menuindex' => 3,
  'searchable' => true,
  'cacheable' => true,
  'createdby' => 1,
  'editedby' => 0,
  'deleted' => false,
  'deletedon' => 0,
  'deletedby' => 0,
  'menutitle' => '',
  'donthit' => false,
  'privateweb' => false,
  'privatemgr' => false,
  'content_dispo' => 0,
  'hidemenu' => false,
  'context_key' => 'web',
  'content_type' => 1,
  'hide_children_in_tree' => 0,
  'show_in_tree' => 1,
  'properties' => NULL,
), '', true, true);
$resources[5]->setContent(file_get_contents($sources['data'].'resources/examples.content.html'));

$resources[6] = $modx->newObject('modResource');
$resources[6]->fromArray(array (
  'id' => 6,
  'type' => 'document',
  'contentType' => 'text/html',
  'pagetitle' => 'ClassExtender Register PostHook',
  'longtitle' => '',
  'description' => '',
  'alias' => 'classextender-register-posthook',
  'alias_visible' => true,
  'link_attributes' => '',
  'published' => false,
  'isfolder' => false,
  'introtext' => NULL,
  'richtext' => false,
  'template' => 'default',
  'menuindex' => 0,
  'searchable' => true,
  'cacheable' => true,
  'createdby' => 1,
  'editedby' => 0,
  'deleted' => false,
  'deletedon' => 0,
  'deletedby' => 0,
  'menutitle' => '',
  'donthit' => false,
  'privateweb' => false,
  'privatemgr' => false,
  'content_dispo' => 0,
  'hidemenu' => false,
  'context_key' => 'web',
  'content_type' => 1,
  'hide_children_in_tree' => 0,
  'show_in_tree' => 1,
  'properties' => NULL,
), '', true, true);
$resources[6]->setContent(file_get_contents($sources['data'].'resources/classextender_register_posthook.content.html'));

$resources[7] = $modx->newObject('modResource');
$resources[7]->fromArray(array (
  'id' => 7,
  'type' => 'document',
  'contentType' => 'text/html',
  'pagetitle' => 'ClassExtender Update Profile',
  'longtitle' => '',
  'description' => '',
  'alias' => 'class-extender-update-profile',
  'alias_visible' => true,
  'link_attributes' => '',
  'published' => false,
  'isfolder' => false,
  'introtext' => NULL,
  'richtext' => false,
  'template' => 'default',
  'menuindex' => 1,
  'searchable' => true,
  'cacheable' => true,
  'createdby' => 1,
  'editedby' => 0,
  'deleted' => false,
  'deletedon' => 0,
  'deletedby' => 0,
  'menutitle' => '',
  'donthit' => false,
  'privateweb' => false,
  'privatemgr' => false,
  'content_dispo' => 0,
  'hidemenu' => false,
  'context_key' => 'web',
  'content_type' => 1,
  'hide_children_in_tree' => 0,
  'show_in_tree' => 1,
  'properties' => NULL,
), '', true, true);
$resources[7]->setContent(file_get_contents($sources['data'].'resources/classextender_update_profile.content.html'));

$resources[8] = $modx->newObject('modResource');
$resources[8]->fromArray(array (
  'id' => 8,
  'type' => 'document',
  'contentType' => 'text/html',
  'pagetitle' => 'ClassExtender GetExtUsers',
  'longtitle' => '',
  'description' => '',
  'alias' => 'class-extender-getextusers',
  'alias_visible' => true,
  'link_attributes' => '',
  'published' => false,
  'isfolder' => false,
  'introtext' => NULL,
  'richtext' => false,
  'template' => 'default',
  'menuindex' => 2,
  'searchable' => true,
  'cacheable' => true,
  'createdby' => 1,
  'editedby' => 0,
  'deleted' => false,
  'deletedon' => 0,
  'deletedby' => 0,
  'menutitle' => '',
  'donthit' => false,
  'privateweb' => false,
  'privatemgr' => false,
  'content_dispo' => 0,
  'hidemenu' => false,
  'context_key' => 'web',
  'content_type' => 1,
  'hide_children_in_tree' => 0,
  'show_in_tree' => 1,
  'properties' => NULL,
), '', true, true);
$resources[8]->setContent(file_get_contents($sources['data'].'resources/classextender_getextusers.content.html'));

$resources[9] = $modx->newObject('modResource');
$resources[9]->fromArray(array (
  'id' => 9,
  'type' => 'document',
  'contentType' => 'text/html',
  'pagetitle' => 'ClassExtender GetExtResources',
  'longtitle' => '',
  'description' => '',
  'alias' => 'class-extender-getextresources',
  'alias_visible' => true,
  'link_attributes' => '',
  'published' => false,
  'isfolder' => false,
  'introtext' => NULL,
  'richtext' => false,
  'template' => 'default',
  'menuindex' => 3,
  'searchable' => true,
  'cacheable' => true,
  'createdby' => 1,
  'editedby' => 0,
  'deleted' => false,
  'deletedon' => 0,
  'deletedby' => 0,
  'menutitle' => '',
  'donthit' => false,
  'privateweb' => false,
  'privatemgr' => false,
  'content_dispo' => 0,
  'hidemenu' => false,
  'context_key' => 'web',
  'content_type' => 1,
  'hide_children_in_tree' => 0,
  'show_in_tree' => 1,
  'properties' => NULL,
), '', true, true);
$resources[9]->setContent(file_get_contents($sources['data'].'resources/classextender_getextresources.content.html'));

$resources[10] = $modx->newObject('modResource');
$resources[10]->fromArray(array (
  'id' => 10,
  'type' => 'document',
  'contentType' => 'text/html',
  'pagetitle' => 'ClassExtender Set Resource Placeholders',
  'longtitle' => '',
  'description' => '',
  'alias' => 'class-extender-set-resource-placeholders',
  'alias_visible' => true,
  'link_attributes' => '',
  'published' => false,
  'isfolder' => false,
  'introtext' => NULL,
  'richtext' => false,
  'template' => 'default',
  'menuindex' => 4,
  'searchable' => true,
  'cacheable' => true,
  'createdby' => 1,
  'editedby' => 0,
  'deleted' => false,
  'deletedon' => 0,
  'deletedby' => 0,
  'menutitle' => '',
  'donthit' => false,
  'privateweb' => false,
  'privatemgr' => false,
  'content_dispo' => 0,
  'hidemenu' => false,
  'context_key' => 'web',
  'content_type' => 1,
  'hide_children_in_tree' => 0,
  'show_in_tree' => 1,
  'properties' => NULL,
), '', true, true);
$resources[10]->setContent(file_get_contents($sources['data'].'resources/classextender_set_resource_placeholders.content.html'));

$resources[11] = $modx->newObject('modResource');
$resources[11]->fromArray(array (
  'id' => 11,
  'type' => 'document',
  'contentType' => 'text/html',
  'pagetitle' => 'ClassExtender Set User Placeholders',
  'longtitle' => '',
  'description' => '',
  'alias' => 'class-extender-set-user-placeholders',
  'alias_visible' => true,
  'link_attributes' => '',
  'published' => false,
  'isfolder' => false,
  'introtext' => NULL,
  'richtext' => false,
  'template' => 'default',
  'menuindex' => 5,
  'searchable' => true,
  'cacheable' => true,
  'createdby' => 1,
  'editedby' => 0,
  'deleted' => false,
  'deletedon' => 0,
  'deletedby' => 0,
  'menutitle' => '',
  'donthit' => false,
  'privateweb' => false,
  'privatemgr' => false,
  'content_dispo' => 0,
  'hidemenu' => false,
  'context_key' => 'web',
  'content_type' => 1,
  'hide_children_in_tree' => 0,
  'show_in_tree' => 1,
  'properties' => NULL,
), '', true, true);
$resources[11]->setContent(file_get_contents($sources['data'].'resources/classextender_set_user_placeholders.content.html'));

$resources[12] = $modx->newObject('modResource');
$resources[12]->fromArray(array (
  'id' => 12,
  'type' => 'document',
  'contentType' => 'text/html',
  'pagetitle' => 'ClassExtender User Search Form',
  'longtitle' => '',
  'description' => '',
  'alias' => 'class-extender-user-search-form',
  'alias_visible' => true,
  'link_attributes' => '',
  'published' => false,
  'isfolder' => false,
  'introtext' => NULL,
  'richtext' => false,
  'template' => 'default',
  'menuindex' => 6,
  'searchable' => true,
  'cacheable' => true,
  'createdby' => 1,
  'editedby' => 0,
  'deleted' => false,
  'deletedon' => 0,
  'deletedby' => 0,
  'menutitle' => '',
  'donthit' => false,
  'privateweb' => false,
  'privatemgr' => false,
  'content_dispo' => 0,
  'hidemenu' => false,
  'context_key' => 'web',
  'content_type' => 1,
  'hide_children_in_tree' => 0,
  'show_in_tree' => 1,
  'properties' => NULL,
), '', true, true);
$resources[12]->setContent(file_get_contents($sources['data'].'resources/classextender_user_search_form.content.html'));

return $resources;
