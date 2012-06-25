<?php
class Class_Model_Tag_Collection extends Class_Model_Collection_Abstract
{
    public function __construct()
    {
        $this->_init();
        $this->setResourceName('tag');
        $this->getSelect()->from(array('main_table' => 'tag'));
    }
}