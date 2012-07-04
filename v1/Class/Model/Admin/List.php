<?php
class Class_Model_Admin_List extends Class_Model_List_Abstract
{
    public function __construct()
    {
        $this->_init('admin');
        $this->setModelName('Admin');
    }
    public function joinRole()
    {
    	$this->getSelect()
    		->joinLeft(
    			array('r'=>'admin_role'),
    			'r.roleId = main_table.roleId',
    			'r.name as role'
    		);
    	return $this;
    }
}