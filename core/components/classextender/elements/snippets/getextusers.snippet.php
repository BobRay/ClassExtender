<?php
/**
 * GetExtUsers snippet for ClassExtender extra
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
 * Show user information
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package classextender
 **/

/* Assume these are false until we know otherwise */
$cliMode = false;
$outsideModx = false;

/* Set a user (set to actual username) */
$myUserName = 'JoeBlow';

/* Instantiate MODX if necessary */
if (!isset($modx)) {
    $outsideModx = true;

    /* Set path to MODX core directory */
    if (!defined('MODX_CORE_PATH')) {
        /* be sure this has a trailing slash */
        define('MODX_CORE_PATH', 'c:/xampp/htdocs/addons/core/');
    }
    /* get the MODX class file */
    require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

    /* instantiate the $modx object */
    $modx = new modX();
    if (!$modx) {
        echo 'Could not create MODX class';
    }
    /* initialize MODX and set current context */
    $modx->initialize('web');
    // or $modx->initialize('mgr');

    /* load the error handler */
    $modx->getService('error', 'error.modError', '', '');

    /* Make sure there's a request */
    $modx->getRequest();

    /* Set up logging */
    $modx->setLogLevel(xPDO::LOG_LEVEL_INFO);

    /* Check for CLI mode and set log target */
    if (php_sapi_name() == 'cli') {
        $cliMode = true;
        $modx->setLogTarget('ECHO');
    } else {
        $modx->setLogTarget('HTML');
    }

    /* Set $modx->resource, request, and $modx->user */

    /* Set $modx->resource to home page */
    $homeId = $modx->getOption('site_start');
    $homeResource = $modx->getObject('modResource', $homeId);

    if ($homeResource instanceof modResource) {
        $modx->resource = $homeResource;
    } else {
        die('No Resource');
    }

    /* set $modx->user */
    $myUser = $modx->getObject('modUser', array('username' => $myUserName));
    if ($myUser instanceof modUser) {
        $modx->user = $myUser;
    } else {
        die('No User');
    }
}

/* Use this next section if the ClassExtender package
   is not registered in the extension_packages System
   Setting */

/* $package = 'extendeduser';
$prefix = 'ext_';
$basePath = $modx->getOption('ce.core_path', NULL, $modx->getOption('core_path') . 'components/' . $package . '/');

$modelPath = $basePath . 'model/';

$success = $modx->addPackage($package, $modelPath, $prefix);
if (!$success) {
    return "\naddPackage failed.";
    return;
}*/
/* @var $scriptProperties array */
$scriptProperties = isset($scriptProperties)? $scriptProperties : array();
$sp = $scriptProperties;

$userClass = $modx->getOption('userClass', $sp, 'extUser' );
$category = $modx->getOption('category', $sp, 'All');
$showInactive = $modx->getOption('showInactive', $sp, false);


$where = $modx->getOption('where', $sp, array(
     array(
         'Data.category1:='    => $category,
         'OR:Data.category2:=' => $category,
         'OR:Data.category3:=' => $category,
     ),
));

$outerTpl = $modx->getOption('extUserOuterTpl', $sp, 'extUserOuterTpl');
$innerTpl = $modx->getOption('extUserInnerTpl', $sp, 'extUserInnerTpl');
$rowTpl = $modx->getOption('extUserRowTpl', $sp, 'extUserRowTpl');



$c = $modx->newQuery($userClass);
$c->sortby('Data.lastName', 'ASC');

/* No where clause if 'All' and showInactive */
if ($category == 'All') {
    if (!$showInactive) {
        $c->where(array('active' => true));
    }
} else {
    $c->where($where);
}

$users = $modx->getCollectionGraph($userClass, '{"Profile":{},"Data":{}}', $c);

$count = count($users);

if (! $count) {
    return 'No Users Found in category';

}

/* @var $user modUser */
$i = 0;
$outer = $modx->getChunk($outerTpl);

$output = '';
$innerOutput = '';
foreach ($users as $user) {
    $fields = $user->toArray();
    unset($fields['password'], $fields['cachepwd'], $fields['salt'], $fields['hash_class'] );
    if ($user->Profile) {
        $fields = array_merge($user->Profile->toArray(), $fields);
    }
    if ($user->Data) {
        $fields = array_merge($user->Data->toArray(), $fields);
        $fields['category1'] = !empty($fields['category1_other'])
            ? $fields['category1_other']
            : isset($fields['category1'])? $fields['category1'] : '';
        $fields['category2'] = !empty($fields['category2_other'])
            ? $fields['category2_other']
            : isset($fields['category2'])
                ? $fields['category2']
                : '';
        $fields['category3'] = !empty($fields['category3_other'])
            ? $fields['category3_other']
            : isset($fields['category3'])
                ? $fields['category3']
                : '';

    }
    $inner = $modx->getChunk($innerTpl, $fields);
    $row = $modx->getChunk($rowTpl, $fields);
    $innerOutput .= str_replace('[[+extUserRow]]', $row, $inner);
    $i++;
}

$output = str_replace('[[+extUserInner]]', $innerOutput, $outer);

if (php_sapi_name() == 'cli') {
    echo $output;
} else {
     return $output;
}