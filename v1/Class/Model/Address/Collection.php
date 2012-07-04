<?php 
class Class_Model_Address_Collection extends Class_Model_Collection_Abstract
{
    public function __construct()
    {
        $this->_init();
        $this->setResourceName('address');
        $this->getSelect()->from(array('main_table' => 'address'));
    }
}