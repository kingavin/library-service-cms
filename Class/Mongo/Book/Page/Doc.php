<?php
class Class_Mongo_Book_Page_Doc extends App_Mongo_Tree_Leaf_Doc
{
	protected $_field = array(
		'bookId',
		'label',
		'fulltext',
		'link',
		'updated'
	);
}