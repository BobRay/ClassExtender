<?php
/**
 * UserSearchForm snippet for ClassExtender extra
 *
 * Copyright 2013 by Bob Ray <http://bobsguides.com>
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
 * Show user information by selected category
 *
 * Variables
 * ---------
 *
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package classextender
 **/

$formTpl = $modx->getOption('extFormTpl', $scriptProperties, 'ExtUserSearchFormTpl');
$categoryList = array(
    /* 'Other', */
    'Please Select a Category',
    'Bakery/Pastry Chef',
    'Beverage',
    'Candy Making',
    'Catering',
    'Chef',
    'Consulting',
    'Editor/Writer',
    'Education/Cooking School',
    'Food Artisan/Farmer/Producer',
    'Food Stylist',
    'Graphic Design',
    'Grocery/Retail',
    'Marketing',
    'Media/TV/Radio/Internet',
    'Nutrition Services',
    'Personal Chef',
    'Product Development',
    'Public Relations/Event Planner',
    'Recipe Tester/Developer',
    'Restaurant Owner',
    'Retired',
    'Sales Representative',
    'Student',
    'Wholesale Products',
);

$categories = '';
foreach ($categoryList as $category) {
    $categories .= "\n  " . '<option value="' . $category . '">' . $category . '</option >';
}

$output = $modx->getChunk($formTpl, array('categories' => $categories));

$fields = array();

if (isset($_POST['submit-var']) && isset($_POST['ext-category'])) {
    if ($_POST['submit-var'] == 'etaoinshrdlu' && !empty($_POST['ext-category'])) {
        if ($_POST['ext-category'] !== 'Please Select a Category') {
            $category = $_POST['ext-category'];
            $modx->sanitize($category);
            $fields = array_merge($scriptProperties, array('category' => $category));
            $output .= $modx->runSnippet('GetExtUsers', $fields);
            // $output .= "<p>Selected: " . $category . '</p>';
        }
    }
}


return $output;