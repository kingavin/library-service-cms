<?php 
class Class_Mongo_Group_Doc extends App_Mongo_Tree_Doc
{
	protected $_field = array(
		'label',
		'description',
		'type'
	);
	
	protected function _getIndex()
	{
		return $this->groupIndex;
	}
	
	protected function _getReadLeafCollection()
	{
		$co = App_Factory::_m('Group_Item')
			->addFilter('groupType', $this->type);
		return $co;
	}
}