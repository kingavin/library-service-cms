<?php
class Class_Model_Cms_Menu extends Class_Model_Abstract
{
    public function __construct()
    {
        $this->_init('cms_menu');
    }
    
    public function hasChild()
    {
        return false;
    }
}