<?php
/**
 * Properties file for GetExtUsers snippet
 *
 * Copyright 2013 by Bob Ray <http://bobsguides.com>
 * Created on 01-07-2014
 *
 * @package classextender
 * @subpackage build
 */




$properties = array (
  'showInactive' => 
  array (
    'name' => 'showInactive',
    'desc' => 'ce.show_inactive_users',
    'type' => 'combo-boolean',
    'options' => 
    array (
    ),
    'value' => false,
    'lexicon' => 'classextender:properties',
    'area' => '',
  ),
  'userClass' => 
  array (
    'name' => 'userClass',
    'desc' => 'ce.class_for_user_object',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'extUser',
    'lexicon' => 'classextender:properties',
    'area' => '',
  ),
  'extUserouterTpl' => 
  array (
    'name' => 'extUserouterTpl',
    'desc' => 'ce.outer_tpl',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'extUserOuterTpl',
    'lexicon' => 'classextender:properties',
    'area' => '',
  ),
  'extUserinnerTpl' => 
  array (
    'name' => 'extUserinnerTpl',
    'desc' => 'ce.inner_tpl',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'extUserInnerTpl',
    'lexicon' => 'classextender:properties',
    'area' => '',
  ),
  'extUserRowTpl' => 
  array (
    'name' => 'extUserRowTpl',
    'desc' => 'ce.row_tpl',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'extUserRowTpl',
    'lexicon' => 'classextender:properties',
    'area' => '',
  ),
);

return $properties;

