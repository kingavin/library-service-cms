<?php
class Class_Model_Group_Tb extends Zend_Db_Table_Abstract
{
    protected $_name = 'group';
    protected $_rowClass = 'Class_Model_Group_Row';
    
    protected $_dependentTables = array('Class_Model_Group_Tb');
    protected $_referenceMap = array(
    	'Subgroup' => array(
    		'columns' => 'parentId',
    		'refTableClass' => 'Class_Model_Group_Tb',
    		'refColumns' => 'id'
    	)
    );
}