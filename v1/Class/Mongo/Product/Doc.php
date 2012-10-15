<?php
class Class_Mongo_Product_Doc extends App_Mongo_Entity_Doc
{
	protected $_field = array(
		'attributesetId',
		'fulltext',
		'groupId',
		'introicon',
		'introtext',
		'metakey',
		'label',
		'name',
		'sku',
		'origPrice',
		'price',
		'showWhere',
		'weight',
		'graphics',
		'attachmentFiles'
	);
	
	public function setAttachments($urlArr, $nameArr, $typeArr)
	{
		if(count($urlArr) != count($nameArr) || count($urlArr) != count($typeArr)) {
			throw new Exception('attachment count does not match each other!');
		}
		
		$attachment = array();
		foreach($typeArr as $key => $type) {
			$attachment[] = array('filetype' => $type, 'filename' => $nameArr[$key], 'urlname' => $urlArr[$key]);
		}
		$this->attachment = $attachment;
	}
}