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
    'desc' => 'Show inactive users in list; default: No',
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
    'desc' => 'Class for user object',
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
    'desc' => 'Name of outer Tpl chunk to use for user listing; default: extUserOuterTpl',
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
    'desc' => 'Name of inner Tpl chunk to use for user listing; default: extUserInnerTpl.',
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
    'desc' => 'Name of row Tpl chunk to use for user listing -- displays individual user data; default: extUserRowTpl',
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

