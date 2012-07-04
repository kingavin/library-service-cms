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
			$aDoc = $this->addFilter('type', 'article')
				->fetchOne();
			if(is_null($aDoc)) {
				$aDoc = $this->create();
				$aDoc->type = 'article';
				$aDoc->save();
			}
			self::$_articleDoc = $aDoc;
		}
		return self::$_articleDoc;
	}
	
	public function findProductGroup()
	{
		if(is_null(self::$_productDoc)) {
			$pDoc = $this->addFilter('type', 'product')
				->fetchOne();
			if(is_null($pDoc)) {
				$pDoc = $this->create();
				$pDoc->type = 'product';
				$pDoc->groupIndex = array();
				$pDoc->save();
			}
			self::$_productDoc = $pDoc;
		}
		return self::$_productDoc;
	}
}