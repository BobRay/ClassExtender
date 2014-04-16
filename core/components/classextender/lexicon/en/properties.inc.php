<?php
/**
 * en:properties.inc.php topic lexicon file for ClassExtender extra
 *
 * Copyright 2013 by Bob Ray <http://bobsguides.com>
 * Created on 04-03-2014
 *
 * ClassExtender is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * ClassExtender is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * ClassExtender; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package classextender
 */

/**
 * Description
 * -----------
 * en:properties.inc.php topic lexicon strings
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package classextender
 **/





/* Used in properties.getextusers.snippet.php */
$_lang['ce.show_inactive_users'] = 'Show inactive users in list; default: No';
$_lang['ce.class_for_user_object'] = 'Class for user object';
$_lang['ce.outer_tpl'] = 'Name of outer Tpl chunk to use for user listing; default: extUserOuterTpl';
$_lang['ce.inner_tpl'] = 'Name of inner Tpl chunk to use for user listing; default: extUserInnerTpl.';
$_lang['ce.row_tpl'] = 'Name of row Tpl chunk to use for user listing -- displays individual user data; default: extUserRowTpl';

/* Used in properties.classextender.snippet.php */
$_lang['ce.package_desc'] = 'Name of the package being created (e.g., extendeduser, extendedresource); default: empty';
$_lang['ce.class_desc'] = 'Name of class being created (e.g.,   extUser, extResource); default: empty';
$_lang['ce.parent_object_desc'] = 'Class that the object being created extends (e.g., modUser, modResource); default: empty';
$_lang['ce.table_prefix_desc'] = 'Table prefix for new DB table; default: ext_';
$_lang['ce.table_name_desc'] = 'Name for DB table without the prefix (e.g., user_data, resource_data); default: empty';
$_lang['ce.method_desc'] = 'Method to use for creating class and map files; must be use_table or user_schema; default: user_schema';
$_lang['ce.register_package_desc'] = 'If set (recommended), package will be registered in the extension_packages System Setting and will be available on every page load; default: Yes';
$_lang['ce.update_class_key_desc'] = 'If set, the class_key field for all existing objects will be updated when the class is created -- leave this set to No and use the form to update the class_keys; default: No';