<?php
class Class_Mongo_Article_Doc extends App_Mongo_Entity_Doc
{
	protected $_field = array(
		'groupId',
		'label',
		'link',
		'introtext',
		'introicon',
		'fulltext',
		'created',
		'createdBy',
		'createdByAlias',
		'modified',
		'modifiedBy',
		'modifiedByAlias',
		'sort',
		'meatakey',
		'metadesc',
		'hits',
		'featured',
		'reference'
	);
}