<?php 
class Class_Model_Lottery_Click extends Class_Model_Abstract
{
	public function __construct()
	{
		$this->_init('lottery_click');
	}
	
	protected function _afterSave()
	{
	    if($this->getData('isSuccessful') == 1) {
	        $prize = Class_Core::_('Lottery_Prize')->create();
	        $prize->setData(
	            array(
	                'customerId' => $this->getData('customerId'),
	                'lotteryId' => $this->getData('lotteryId'),
	                'clickId' => $this->getData('clickId')
	            )
	        )->save(false);
	    }
	}
}