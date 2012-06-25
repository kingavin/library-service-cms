<?php
class Class_Model_Supplier_List extends Class_Model_List_Abstract
{
    public function __construct()
    {
        $this->_init('supplier');
        $this->setModelName('Supplier');
    }
}