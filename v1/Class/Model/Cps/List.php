<?php
class Class_Model_Cps_List extends Class_Model_List_Abstract
{
    public function __construct()
    {
        $this->_init('cps');
        $this->setModelName('Cps');
    }
    public function getTotalCpsPay($rate){
    	$cpsList = $this->getListData();
    	$total = 0;
    	if(count($cpsList)>0){
	    	foreach($cpsList as $cps){
	    		$orderId = $cps->getData('orderId');
	    		if($orderId >0){
		    		$order = Class_Core::_('order')
		    			->setData('orderId',$orderId)
		    			->load();
		    		$orderFei = $order->getData('sub_total');
		    		$total += $orderFei*$rate;
	    		}
	    	}
    	}
    	return $total;
    }
}