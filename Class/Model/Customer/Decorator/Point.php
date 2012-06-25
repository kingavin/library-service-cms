<?php
class Class_Model_Customer_Decorator_Point extends Class_Model_Customer
{
    protected $_decoratedCustomer;
    protected $_amount = array();
    
    public function __construct(Class_Model_Customer $decoratedCustomer, Array $amout)
    {
        $this->_decoratedCustomer = $decoratedCustomer;
        $this->_amount = $amout;
    }
    
    public function promote()
    {
        $this->_decoratedCustomer->promote();
        if(count($this->_amount) > 0) {
            $origPoint = $this->_decoratedCustomer->getData('point');
            $this->_decoratedCustomer->setData('point',$this->_amount[0] + $origPoint)->save();
            if(count($this->_amount) > 1) {
                $newAmount = array_shift($this->_amount);
                $refId = intval($this->_decoratedCustomer->getData('parentId'));
                if (!is_null($refId)) {
                    $customer = Class_Core::_('Customer')->setData('entityId', $refId)
                        ->load();
                    $refCustomer = Class_Core::_('Customer_Decorator_Point', $customer, $this->_amount);
                    $refCustomer->promote();
                }
            }
        }
    }
}