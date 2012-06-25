<?php
class Class_Model_Category_Tb extends Zend_Db_Table_Abstract
{
    protected $_name = 'category';
    protected $_rowClass = 'Class_Model_Category_Row';
    
    protected $_referenceMap = array(
    	'Section' => array(
    		'columns' => 'sectionId',
    		'refTableClass' => 'Class_Model_Category_Section_Tb',
    		'refColumns' => 'id',
    		'onDelete' => self::CASCADE
    	)
    );
}