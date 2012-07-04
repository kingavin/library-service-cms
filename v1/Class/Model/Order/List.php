<?php
class Class_Model_Order_List extends Class_Model_List_Abstract
{
    public function __construct()
    {
        $this->_init('orders');
        $this->setModelName('Order');
    }
    
    public function joinAddress($field = array())
    {
        if(is_null($field)) {
            $this->getSelect()->joinLeft(
                array('a' => 'address'),
                'a.addressId = main_table.addressId'
            );
        } else if(is_array($field)) {
            $this->getSelect()->joinLeft(
                array('a' => 'address'),
                'a.addressId = main_table.addressId',
                $field
            );
        } else {
            throw new Exception('array required for order list to join address!');
        }
        return $this;
    }
}
