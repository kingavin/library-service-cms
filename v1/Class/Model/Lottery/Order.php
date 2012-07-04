<?php
class Class_Model_Lottery_Order extends Class_Model_Order
{
    protected $_prizeId;
    
    public function setPrizeId($prizeId)
    {
        $this->_prizeId = $prizeId;
        return $this;
    }
    
    protected function _beforeSave()
    {
        $this->setData('type', 'lottery');
    }
    
    protected function _afterSave()
    {
        $select = new Zend_Db_Select();
//        $select->
    }
}