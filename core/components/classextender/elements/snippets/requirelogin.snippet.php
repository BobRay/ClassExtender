<?php
/**
 * RequireLogin snippet for ClassExtender extra
 *
 * Copyright 2012-2022 Bob Ray <https://bobsguides.com>
 * Created on 09-17-2022
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
 * Show Login form to not-logged-in users
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package classextender
 **/

$msg='';

$cssFile = $modx->getOption('cssFile', $scriptProperties,
    '', true);

if (!empty($cssFile)) {
    $modx->regClientCSS($cssFile);
}

if ($modx->user->hasSessionContext($modx->context->get('key'))) {
    $chunk = $modx->getOption('loggedInChunk', $scriptProperties, '');
} else {
    $modx->lexicon->load('classextender:default');
    $msg = '<h3>' . $modx->getOption('notLoggedInMsg', $scriptProperties, $modx->lexicon('ce_login_required')) . '</h3>';
    $chunk =  $modx->getOption('notLoggedInChunk', $scriptProperties, '');
}

$finalChunk = !empty($chunk) ? $modx->getChunk($chunk) : '';
return $msg . $finalChunk;