<?php
class Class_Model_GroupV2_Row extends Zend_Db_Table_Row_Abstract implements Class_Link_Interface
{
	protected $_adRowArr = array();
	
	public function appendAdRow(Zend_Db_Table_Row_Abstract $ad)
	{
		$this->_adRowArr[$ad->id] = $ad;
		return $this;
	}
	
	public function getAdRowArr()
	{
		return $this->_adRowArr;
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
    	return $this->sort;
    }
    
    public function getHref()
    {
    	return "";
    }
}