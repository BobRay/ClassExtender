<?php
/**
 * en default topic lexicon file for ClassExtender extra
 *
 * Copyright 2012-2023 Bob Ray <https://bobsguides.com>
 * Created on 11-10-2013
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
 * en default topic lexicon strings
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package classextender
 **/



/* Used in classextender.snippet.php */
$_lang['ce.no_class_extender'] = 'Could not instantiate ClassExtender';


/* Used in classextender.class.php */
$_lang['ce.add_package_failed'] = 'addPackage() failed';
$_lang['ce.addpackage_failed'] = 'addPackage() failed';
$_lang['ce.could_not_enable'] = 'Could not enable plugin: ';
$_lang['ce.could_not_find_classes_in_schema_file'] = 'Could not find classes in schema file';
$_lang['ce.could_not_find_schema_chunk'] = 'Could not find schema chunk';
$_lang['ce.could_not_get_package_name_from_schema'] = 'Could not get package name from schema';
$_lang['ce.could_not_open_schema_file'] = 'Could not open schema file';
$_lang['ce.could_not_save_namespace'] = 'Could not save namespace';
$_lang['ce.could_not_update_system_setting'] = 'Could not update System Setting';
$_lang['ce.create_object_container_failed'] = 'createObjectContainer() failed';
$_lang['ce.created_extension_package_object'] = 'Created extension package object';
$_lang['ce.empty_table_prefix'] = 'Table prefix is empty';
$_lang['ce.generating_class_files'] = 'Generating class and map files';
$_lang['ce.getmanager_failed'] = 'getManager() failed';
$_lang['ce.old_class_files_removed'] = 'Old class and map files removed';
$_lang['ce.package_name_is_empty'] = 'Package name is empty';
$_lang['ce.parse_schema_failed'] = 'parse_schema() failed';
$_lang['ce.plugin_enabled'] = 'plugin enabled';
$_lang['ce.saving_schema'] = 'Saving schema';
$_lang['ce.schema_chunk_is_empty'] = 'schemaChunk is empty';
$_lang['ce.schema_parsed'] = 'Schema parsed';
$_lang['ce.table_created'] = 'Table created';

/* Used in getextresources.snippet.php */
$_lang['ce.no_resources_found'] = 'No Resources Found, or no resources have custom fields set';

/* Used in setresourceplaceholders.snippet.php */
$_lang['ce.resource_not_found'] = 'Resource not found, or has no custom fields set';

/* Used in setuserplaceholders.snippet.php */
$_lang['ce.user_not_found'] = 'User not found, or has no custom fields set';

/* Used in getextusers.snippet.php */
$_lang['ce.no_users_found'] = 'No Users Found, or no user has custom fields set';

/* Used in extusersearchformtpl.chunk.html */
$_lang['ce.user_search_first_name_caption'] = 'First Name';
$_lang['ce.user_search_last_name_caption'] = 'Last Name';

/* Used in usersearchform.snippet.php */
$_lang['ce.user_search_results_heading'] = 'Results';

/* Used in upDateProfile.snippet.php */
$_lang['ce.login_required'] = 'Login Required';

/* System Settings */
$_lang['setting_ce_autoload_directories'] = 'Autoload directories';

$_lang['setting_ce_autoload_directories_desc'] = 'Comma-separated list of directories holding class files; default: extendeduser,extendedresource';

/* Used in CreateSchema snippet */
$_lang['ce.could_not_get_manager'] = 'Could not get Manager';
$_lang['ce.could_not_get_generator'] = 'Could not get Generator';
$_lang['ce.schema_written_to_file'] = 'Schema written to file';
$_lang['write_schema_failed'] = 'Error writing schema file - writeSchema() failed';
$_lang['ce.creating_chunk'] = 'Creating Chunk';
$_lang['ce.file_get_contents_failed'] = 'file_get_contents() failed';
$_lang['ce.could_not_save_chunk'] = 'Could not save chunk: ';
$_lang['ce.saved_schema'] = 'Saved schema in chunk: ';