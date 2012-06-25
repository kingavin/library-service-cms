<?php
class Class_Model_Product_Row extends Class_Model_Eav_Entity_Row_Abstract
{
	public $groupLinks = array();
	
	public function getEntityType()
	{
		return 'product';
	}
	
	public function getOriginalForm($backendForm)
	{
		require APP_PATH.'/admin/forms/Product/Edit.php';
		$form = new Form_Product_Edit();
		
		return $form;
	}
	
	public function getValueTable()
	{
		$tb = new Zend_Db_Table('product_entity_value');
		return $tb;
	}
	
//	public function getGroupLinks()
//	{
//		$tb = new Zend_Db_Table('product_group_link');
//		$rowset = $tb->fetchAll($tb->select()->where('productId = ?', $this->id));
//		$arr = Class_Func::buildArr($rowset, 'groupId', 'groupId');
//		return $arr;
//	}
//	
//	protected function _postInsert()
//	{
//		parent::_postInsert();
//		
//		$tb = new Zend_Db_Table('product_group_link');
//		foreach($this->groupLinks as $gl) {
//			$row = $tb->createRow();
//			$row->productId = $this->id;
//			$row->groupId = $gl;
//			$row->save();
//		}
//	}
//	
//	protected function _postUpdate()
//	{
//		parent::_postUpdate();
//		
//		$tb = new Zend_Db_Table('product_group_link');
//		$tb->delete('productId = '.$this->id);
//		foreach($this->groupLinks as $gl) {
//			$row = $tb->createRow();
//			$row->productId = $this->id;
//			$row->groupId = $gl;
//			$row->save();
//		}
//	}
}