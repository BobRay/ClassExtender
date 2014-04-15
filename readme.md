ClassExtender Extra for MODx Revolution
=======================================


**Author:** Bob Ray <http://bobsguides.com> [Bob's Guides](http://bobsguides.com)

Documentation is available at [Bob's Guides](http://bobsguides.com/classextender-tutorial.html)

ClassExtender Extra

A utility to create extended MODX objects. By default
creates an extended modUser object called extUser and and extended modResource object called extResource.

Edit the schema file to match the extra user or resource fields you want to store in your custom extended user table, then view either the Extend modUser or Extend modResource resources.

The extra user and/or resource fields are in an object called Data (in their own custom table):

$user->getOne('Data');

$resource->getOne('Data'); 

