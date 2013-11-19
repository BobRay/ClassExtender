<?php
$xpdo_meta_map['extUser']= array (
  'package' => 'extendeduser',
  'version' => NULL,
  'extends' => 'modUser',
  'fields' => 
  array (
  ),
  'fieldMeta' => 
  array (
  ),
  'composites' => 
  array (
    'Data' => 
    array (
      'local' => 'id',
      'class' => 'userData',
      'foreign' => 'userdata_id',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
  ),
);
