<?php
class Class_Model_Admin_Role_List extends Class_Model_List_Abstract
{
    public function __construct()
    {
        $this->_init('admin_role');
        $this->setModelName('Admin_Role');
    }
}