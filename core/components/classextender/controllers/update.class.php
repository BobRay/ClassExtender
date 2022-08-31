<?php

/**
 * ClassExtender update controller file for ClassExtender extra
 *
 * Copyright 2012-2022 Bob Ray <https://bobsguides.com>
 * Created on 04-10-2014
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

class extResourceUpdateManagerController extends ResourceUpdateManagerController {
    public function getLanguageTopics() {
        return array(
            'resource',
            'classextender:default'
        );
    }
}