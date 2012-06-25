<?php
class Class_Model_Coupon_List extends Class_Model_List_Abstract
{
    public function __construct()
    {
        $this->_init('coupon');
        $this->setModelName('Coupon');
    }
}