<?php
class Class_Model_Category_Row extends Zend_Db_Table_Row implements Class_Link_Interface
{
    public function getId()
    {
    	return $this->id;
    }
    
    public function getParentId()
    {
    	return $this->parentId;
    }
    
    public function getOrder()
    {
    	return $this->order;
    }
    
    public function getHref()
    {
    	return $this->link;
    }
}