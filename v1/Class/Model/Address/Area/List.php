<?php
class Class_Model_Address_Area_List extends Class_Model_List_Abstract
{
    public function __construct()
    {
        $this->_init('area');
        $this->setModelName('Address_Area');
    }
}