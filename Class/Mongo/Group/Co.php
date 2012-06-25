<?php
class Class_Mongo_Group_Co extends App_Mongo_Db_Collection
{
	protected $_name = 'group';
	protected $_documentClass = 'Class_Mongo_Group_Doc';
	
	public function getByType($type, $returnAsOption = false)
	{
		$groupType = null;
		switch($type) {
			case 'article':
				$groupType = $type;
				break;
			case 'product':
				$groupType = $type;
				break;
			default:
				throw new Exception('group type "'.$type.'" not defined');
				return null;
		}
		
		$doc = $this->addFilter('type', $groupType)
			->fetchOne();
		if($returnAsOption) {
			$arr = array();
			foreach($doc->nodes as $node) {
				$this->_buildArray($node, $arr, '');
			}
			return $arr;
		} else {
			return $doc;
		}
	}
	
	protected function _buildArray($obj, &$arr, $prefix)
	{
		$arr[$obj['jId']] = $prefix.$obj['label'];
		if(isset($obj->nodes)) {
			foreach($obj->nodes as $node) {
				$this->_buildArray($node, $arr, $prefix.'--');
			}
		}
		return ;
	}
}