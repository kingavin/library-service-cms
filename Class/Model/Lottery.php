<?php
class Class_Model_Lottery extends Class_Model_Abstract
{
	public function __construct()
	{
		$this->_init('lottery');
	}
	public function isPrize($total=0)
	{
		$probability = $this->getData('probability');
		if (is_null($probability) || $total == 0) {
			return false;
		}
		if ($probability > $total) {
			return false;
		}
		$randArr = array();
		for ($i=0;$i<$probability;$i++) {
			$randArr[] = mt_rand(0, $total);
		}
		$rn = mt_rand(0, $total);
		$this->setData('diceResult', $rn);
		return in_array($rn, $randArr);
	}
}