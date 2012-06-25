<?php
class Class_Model_Group_Row extends Zend_Db_Table_Row_Abstract implements Class_Link_Interface
{
	public function toArray()
	{
		return parent::toArray();
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
		$href = "";
		if(!empty($this->alias)) {
			$href = $this->alias;
		} else {
			if($this->type == 'article') {
				$href = '/list-'.$this->id.'/page1.shtml';
			} else if($this->type == 'product') {
				$href = '/product-list-'.$this->id.'/page1.shtml';
			}
		}
		return $href;
	}
}