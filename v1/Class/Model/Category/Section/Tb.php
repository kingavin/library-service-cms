<?php
class Class_Model_Category_Section_Tb extends Zend_Db_Table_Abstract
{
    protected $_name = 'category_section';
    protected $_dependentTables = array('Class_Model_Category_Tb');
}