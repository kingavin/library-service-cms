<?php
class Class_Model_Layout_Brick_Tb extends Zend_Db_Table_Abstract
{
    protected $_name = 'layout_brick';
    
    protected $_referenceMap    = array(
        'LayoutBrick' => array(
            'columns' => 'layoutId',
            'refTableClass' => 'Class_Model_Layout_Tb',
            'refColumns' => 'id',
            'onDelete' => self::CASCADE
        ),
        'BrickLayout' => array(
            'columns' => 'brickId',
            'refTableClass' => 'Class_Model_Brick_Tb',
            'refColumns' => 'brickId',
            'onDelete' => self::CASCADE
        )
    );
}