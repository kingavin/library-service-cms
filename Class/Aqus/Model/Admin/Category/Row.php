<?php
class Class_Aqus_Model_Admin_Category_Row extends Zend_Db_Table_Row_Abstract implements Class_Link_Interface
{
	public function getControllerName()
	{
		return $this->controllerName;
	}
	
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
		return 0;
	}
    
    public function getHref()
    {
    	return '/admin/'.$this->controllerName.'/'.$this->actionName.'/'.$this->param;
    }
}