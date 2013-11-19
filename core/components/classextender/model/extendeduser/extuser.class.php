<?php
class extUser extends modUser {
    function __construct(xPDO & $xpdo) {
        parent::__construct($xpdo);
        $this->set('class_key', 'extUser');
    }
}