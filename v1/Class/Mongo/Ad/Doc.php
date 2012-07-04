<?php
class Class_Mongo_Ad_Doc extends App_Mongo_Db_Document
{
	protected $_field = array(
		'groupId',
		'label',
		'alias',
		'clicks',
		'url',
		'image',
		'description',
		'created'
	);
}