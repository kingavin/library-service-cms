<?php
class Class_Mongo_Group_Co extends App_Mongo_Db_Collection
{
	protected $_name = 'group';
	protected $_documentClass = 'Class_Mongo_Group_Doc';
	
	static protected $_articleDoc = null;
	static protected $_productDoc = null;
	
	public function findArticleGroup()
	{
		if(is_null(self::$_articleDoc)) {
			self::$_articleDoc = $this->addFilter('type', 'article')
				->fetchOne();
		}
		return self::$_articleDoc;
	}
	
	public function findProductGroup()
	{
		if(is_null(self::$_productDoc)) {
			self::$_productDoc = $this->addFilter('type', 'product')
				->fetchOne();
		}
		return self::$_productDoc;
	}
}