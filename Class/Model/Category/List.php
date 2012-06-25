<?php
class Class_Model_Category_List extends Class_Model_Eav_Entity_List_Abstract
{
    public function __construct()
    {
        $this->_init('category_entity');
        $this->setModelName('Category');
    }
    
    public function getBaseTableName()
    {
        return 'category_entity';
    }
}
