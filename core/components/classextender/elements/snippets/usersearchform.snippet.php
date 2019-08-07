<?php
/**
 * UserSearchForm snippet for ClassExtender extra
 *
 * Copyright 2012-2019 Bob Ray <https://bobsguides.com>
 * Created on 01-05-2014
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
 * Show user information based on search
 *
 * Variables
 * ---------
 *
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package classextender
 **/

/* Properties
 *
 * @property &extFormTpl textfield -- Tpl chunk to use for user
 * search form; Default: ExtUserSearchFormTpl.
 */


$formTpl = $modx->getOption('extFormTpl', $scriptProperties, 'ExtUserSearchFormTpl');


$output = $modx->getChunk($formTpl);

$pFirstName = $modx->getOption('user_search_first_name', $_POST, '');
$pLastName = $modx->getOption('user_search_last_name', $_POST, '');

$modx->setPlaceholder('user_search_first_name', $pFirstName);
$modx->setPlaceholder('user_search_last_name', $pLastName);



$fields = array();

if (isset($_POST['submit-var']) && $_POST['submit-var'] == 'etaoinshrdlu') {

    $fields['where'] = '{"firstName:=":"' . $pFirstName . '","OR:lastName:=":"' . $pLastName . '"}';

    $results = $modx->runSnippet('GetExtUsers', $fields);

}

if (! empty ($results) ){
    $modx->SetPlaceholder('user_search.results_heading',
        $modx->lexicon('ce_user_search_results_heading'));
    $modx->setPlaceholder('user_search.results', $results);
}
return $output;