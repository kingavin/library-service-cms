<?php
class Class_Model_Site extends Zend_Db_Table_Row_Abstract
{	
	public function _insert()
	{
		if(is_numeric($this->validToDate)) {
			if($this->validToDate < 365) {
				$this->isTrialAccount = 1;
			}
			$time = time() + $this->validToDate*24*3600;
			$this->validToDate = date('Y-m-d H:m:s', $time);
		}
	}
	
	public function _postInsert()
	{
	    $adminRowId = Class_Base::_('Admin')->insert(array(
	        'loginName' => 'root',
	        'password' => md5('123456'.MD5_SALT),
	        'roleId' => 0
	    ));
	}
	
	public function _update()
	{
		if(is_numeric($this->validToDate)) {
			$this->isTrialAccount = 0;
			$time = strtotime($this->_cleanData[validToDate]) + $this->validToDate*24*3600;
			$this->validToDate = date('Y-m-d H:m:s', $time);
		}
	}
}