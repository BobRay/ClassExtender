<?php
/**
 * Properties file for ClassExtender snippet
 *
 * Copyright 2013 by Bob Ray <http://bobsguides.com>
 * Created on 04-15-2014
 *
 * @package classextender
 * @subpackage build
 */




$properties = array (
  'package' => 
  array (
    'name' => 'package',
    'desc' => 'ce.package_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'classextender:properties',
    'area' => '',
  ),
  'class' => 
  array (
    'name' => 'class',
    'desc' => 'ce.class_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'classextender:properties',
    'area' => '',
  ),
  'parentObject' => 
  array (
    'name' => 'parentObject',
    'desc' => 'ce.parent_object_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'classextender:properties',
    'area' => '',
  ),
  'tablePrefix' => 
  array (
    'name' => 'tablePrefix',
    'desc' => 'ce.table_prefix_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'ext_',
    'lexicon' => 'classextender:properties',
    'area' => '',
  ),
  'tableName' => 
  array (
    'name' => 'tableName',
    'desc' => 'ce.table_name_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'classextender:properties',
    'area' => '',
  ),
  'method' => 
  array (
    'name' => 'method',
    'desc' => 'ce.method_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'use_schema',
    'lexicon' => 'classextender:properties',
    'area' => '',
  ),
  'registerPackage' => 
  array (
    'name' => 'registerPackage',
    'desc' => 'ce.register_package_desc',
    'type' => 'combo-boolean',
    'options' => 
    array (
    ),
    'value' => true,
    'lexicon' => 'classextender:properties',
    'area' => '',
  ),
  'updateClassKey' => 
  array (
    'name' => 'updateClassKey',
    'desc' => 'ce.update_class_key_desc',
    'type' => 'combo-boolean',
    'options' => 
    array (
    ),
    'value' => false,
    'lexicon' => 'classextender:properties',
    'area' => '',
  ),
);

return $properties;

