<?php
/**
 * en:properties.inc.php topic lexicon file for ClassExtender extra
 *
 * Copyright 2012-2019 Bob Ray <https://bobsguides.com>
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
$_lang['ce.get_ext_users_where'] = 'JSON string containing query criteria; default: empty';
$_lang['ce.get_ext_users_sortby_desc'] = 'Field to sort by (e.g., username, Profile.fullname, Data.lastname); default: username';
$_lang['ce.get_ext_users_sortdir_desc'] = 'Direction to sort in (ASC, DESC); default: ASC';
$_lang['ce.class_for_user_data_object'] = 'Class for extended data table (not the user object); default: userData';
$_lang['ce.outer_tpl'] = 'Name of outer Tpl chunk to use for user listing; default: extUserOuterTpl';
$_lang['ce.inner_tpl'] = 'Name of inner Tpl chunk to use for user listing; default: extUserInnerTpl.';
$_lang['ce.row_tpl'] = 'Name of row Tpl chunk to use for user listing -- displays individual user data; default: extUserRowTpl';

/* Used in properties.classextender.snippet.php */
$_lang['ce.table_prefix_desc'] = 'Table prefix for new DB table; default: ext_';

/* Used in properties.getextresources.snippet.php */
$_lang['ce.get_ext_resources_inner_tpl_desc'] = 'Name of inner Tpl chunk; default: ExtResourceInnerTpl';
$_lang['ce.get_ext_resources_outer_tpl_desc'] = 'Name of outer Tpl chunk; default: ExtResourceOuterTpl';
$_lang['ce.get_ext_resources_row_tpl_desc'] = 'Name of row Tpl chunk; default: ExtResourceRowTpl';
$_lang['cd.resource_class_desc'] = 'Name of extended resource class; default: extResource';
$_lang['ce.get_ext_resources_sortby_desc'] = 'Field to sort by (e.g., pagetitle, Data.somefield); default: pagetitle';
$_lang['ce.get_ext_resources_sortdir_desc'] = 'Direction to sort in (ASC, DESC); default: ASC';
$_lang['ce.get_ext_resources_where_desc'] = 'JSON string with search criteria; default: empty';

/* Used in properties.setuserplaceholders.snippet.php */
$_lang['ce.user_id_desc'] = 'User ID; default: empty (defaults to current user)';
$_lang['ce.user_prefix_desc'] = 'Prefix for placeholders; default: empty';

/* Used in properties.setresourceplaceholders.snippet.php */
$_lang['ce.resource_id_desc'] = 'ID of resource to set placeholders from; default: empty (defaults to current resource)';
$_lang['ce.resource_prefix_desc'] = 'Prefix for placeholders; default: empty';

/* Used in properties.usersearchform.snippet.php */
$_lang['ce.ext_form_tpl_desc'] = 'Tpl chunk to use for user search form; default: ExtUserSearchFormTpl';