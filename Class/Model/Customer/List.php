<?php
class Class_Model_Customer_List extends Class_Model_List_Abstract
{
    public function __construct()
    {
        $this->_init('customer_entity');
        $this->setModelName('Customer');
    }
}