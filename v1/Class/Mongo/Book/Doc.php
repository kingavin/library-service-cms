<?php
class Class_Mongo_Book_Doc extends App_Mongo_Tree_Doc
{
	protected $_field = array(
		'label',
		'alias',
		'description'
	);
	
	protected function _getIndex()
	{
		return $this->bookIndex;
	}
	
	protected function _getReadLeafCollection()
	{
		$co = App_Factory::_m('Book_Page')
			->addFilter('bookId', $this->getId());
		return $co;
	}
}