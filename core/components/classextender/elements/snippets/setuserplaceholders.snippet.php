<?php
/**
 * SetUserPlaceholders snippet for ClassExtender extra
 *
 * Copyright 2013 by Bob Ray <http://bobsguides.com>
 * Created on 04-13-2014
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
 * Set placeholders for extra user fields
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package classextender
 **/

$modx->lexicon->load('classextender:default');

$sp = $scriptProperties;
$userId = $modx->getOption('userId', $sp, null);
$prefix = $modx->getOption('prefix', $sp, '');

if ($userId != null) {
    $data = $modx->getObject('userData', $userId);
    if (! $user) {
        return $modx->lexicon('ce.user_not_found');
    }
} else {
    $user = $modx->user;
    $data = $modx->getObject('userData', array('userdata_id' => $user->get('id')));
}

if ($data) {
    $modx->toPlaceholders($data, $prefix);
}