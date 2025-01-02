<?php
/**
 * Properties file for SetUserPlaceholders snippet
 *
 * Copyright 2012-2023 Bob Ray <https://bobsguides.com>
 * Created on 04-16-2014
 *
 * @package classextender
 * @subpackage build
 */

$properties = array (
  'prefix' =>
  array (
    'name' => 'prefix',
    'desc' => 'ce.user_prefix_desc',
    'type' => 'textfield',
    'options' =>
    array (
    ),
    'value' => '',
    'lexicon' => 'classextender:properties',
    'area' => '',
  ),
  'UserDataClass' =>
  array (
    'name' => 'UserDataClass',
    'desc' => 'ce.class_for_user_data_object',
    'type' => 'textfield',
    'options' =>
    array (
    ),
    'value' => 'UserData',
    'lexicon' => 'classextender:properties',
    'area' => '',
  ),
  'userId' =>
  array (
    'name' => 'userId',
    'desc' => 'ce.user_id_desc',
    'type' => 'textfield',
    'options' =>
    array (
    ),
    'value' => '',
    'lexicon' => 'classextender:properties',
    'area' => '',
  ),
);

return $properties;
