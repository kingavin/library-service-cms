<?php 
class Class_Mongo_Navi_Doc extends App_Mongo_Tree_Doc
{
	protected $_field = array(
		'label',
		'description'
	);
	
	protected function _getIndex()
	{
		return $this->naviIndex;
	}
	
	protected function _getReadLeafCollection()
	{
		$co = App_Factory::_m('Navi_Link')
			->addFilter('naviId', $this->getId());
		return $co;
	}
}