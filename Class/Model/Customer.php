<?php
class Class_Model_Customer extends Class_Model_Eav_Entity_Abstract
{
    public function __construct()
    {
        $this->_init('customer_entity');
        $this->setData('entityTypeId', 4);
    }
}