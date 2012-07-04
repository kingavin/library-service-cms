<?php
class Class_Model_Artical_Tb extends Zend_Db_Table_Abstract
{
    protected $_name = 'artical';
    protected $_classRow = 'Class_Model_Artical_Row';
    
    protected $_referenceMap = array(
    	'Group' => array(
    		'columns' => 'groupId',
    		'refTableClass' => 'Class_Model_Group_Tb',
    		'refColumns' => 'id',
    		'onDelete' => self::CASCADE
    	)
    );
}