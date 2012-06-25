<?php
class Class_Model_Manufacturer extends Class_Model_Eav_Entity_Abstract
{
    public function __construct()
    {
        $this->_init('manufacturer_entity');
        $this->setData(array('entityTypeId' => 5, 'entityAttributeSetId' => 1));
    }
}