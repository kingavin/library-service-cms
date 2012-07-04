<?php
class Class_Model_Customer_Decorator_Lottery extends Class_Model_Customer
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
        if(count($this->_amount) > 0) {
            $pointGiven = $this->_amount[0];
            $this->_decoratedCustomer->setData('lotteryPoint', $this->_decoratedCustomer->getData('lotteryPoint') + $pointGiven)
                ->save();
        }
        
        $refId = $this->_decoratedCustomer->getData('parentId');
        array_shift($this->_amount);
        
        if(count($this->_amount) > 0 && !empty($refId)) {
            $customer = Class_Core::_('Customer')->setData('entityId', $refId)
                ->load();
            Class_Core::_('Customer_Decorator_Lottery', $customer, $this->_amount)->promote();
        }
    }
}