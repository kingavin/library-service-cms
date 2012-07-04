<?php
class Class_Mongo_Layout_Doc extends App_Mongo_Db_Document
{
	protected $_field = array(
		'label',
		'moduleName',
		'controllerName',
		'actionName',
		'default',
		'type',
		'displayHead'
	);
}