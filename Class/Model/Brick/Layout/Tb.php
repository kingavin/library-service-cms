<?php
class Class_Model_Brick_Layout_Tb extends Zend_Db_Table_Abstract
{
    protected $_name = 'brick_layout';
    
    protected $_referenceMap = array(
    	'Brick' => array(
    		'columns' => 'brickId',
    		'refTableClass' => 'Class_Model_Eo_Brick_Tb',
    		'refColumns' => 'brickId',
    		'onDelete' => self::CASCADE
    	)
    );
}