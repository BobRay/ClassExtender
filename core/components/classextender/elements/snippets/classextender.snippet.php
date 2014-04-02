<?php
/**
 * ClassExtender snippet for ClassExtender extra
 *
 * Copyright 2013 by Bob Ray <http://bobsguides.com>
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
 * Extend a MODX class
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package classextender
 **/

if (!defined('MODX_CORE_PATH')) {
    $path1 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/_build/build.config.php';
    if (file_exists($path1)) {
        include $path1;
    } else {
        $path2 = dirname(dirname(dirname(__FILE__))) . '/_build/build.config.php';
        if (file_exists($path2)) {
            include($path2);
        }
    }
    if (!defined('MODX_CORE_PATH')) {
        session_write_close();
        die('[ClassExtender] Could not find build.config.php');
    }
    require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
    $modx = new modX();
    /* Initialize and set up logging */
    $modx->initialize('mgr');
    $modx->getService('error', 'error.modError', '', '');
    $modx->setLogLevel(xPDO::LOG_LEVEL_INFO);
    $modx->setLogTarget(XPDO_CLI_MODE
        ? 'ECHO'
        : 'HTML');

    /* This section will only run when operating outside of MODX */
    if (php_sapi_name() == 'cli') {
        $cliMode = true;
        /* Set $modx->user and $modx->resource to avoid
         * other people's plugins from crashing us */
        $modx->getRequest();
        $homeId = $modx->getOption('site_start');
        $homeResource = $modx->getObject('modResource', $homeId);

        if ($homeResource instanceof modResource) {
            $modx->resource = $homeResource;
        } else {
            echo "\nNo Resource\n";
        }
    }
}

if (!$modx->user->hasSessionContext('mgr')) {
    die ('Unauthorized Access');
}

require_once $modx->getOption('ce.core_path', NULL, $modx->getOption('core_path') . 'components/classextender/') . 'model/classextender/classextender.class.php';
// include 'classextender.class.php';

$modx->lexicon->load('classextender:default');
$props =& $scriptProperties;
$ce = new ClassExtender($modx, $props);
if (! $ce instanceof ClassExtender) {
    die ('Could not instantiate ClassExtender');

}
$ce->init();
$output .= $ce->displayForm();

if ($ce->hasError()) {
    $output .= print_r($ce->getErrors(), true);
} else {
    $output .= $ce->getOutput();
}
if (isset($_POST['submitVar']) && $_POST['submitVar'] == 'submitVar') {
    $ce->process();
}

if ($ce->hasError()) {
    $errors = $ce->getErrors();
    foreach($errors as $error) {
        $output.= "\n" . '<p class="ce_error">' . $error . '</p>';
    }

} else {
    $output.= $ce->getOutput();
}

return $output;
/*$ce->generateClassFiles();
$ce->createTables();
$ce->add_extension_package();*/


// $modx->removeExtensionPackage('packageName');