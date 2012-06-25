<?php
class Class_Model_Manufacturer_List extends Class_Model_Eav_Entity_List_Abstract
{
    public function __construct($fields = null)
    {
        $this->_init('manufacturer_entity', $fields);
        $this->setModelName('Manufacturer');
    }
    
    public function getBaseTableName()
    {
        return 'manufacturer_entity';
    }
}