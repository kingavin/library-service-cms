<?php
class Class_Model_Category_Collection extends Class_Model_Eav_Entity_Collection_Abstract
{
    public function __construct()
    {
        $this->_init();
        $this->setResourceName('category_entity');
        $this->getSelect()->from(array('main_table' => 'category_entity'));
    }
    
//    public function loadCategories($printQuery = false, $logQuery = false)
//    {
//        $this->load();
//        return $this;
//    }
    
    
}
