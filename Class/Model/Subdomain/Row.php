<?php
class Class_Model_Subdomain_Row extends Zend_Db_Table_Row_Abstract
{
	protected function _postInsert()
	{
		$adminTb = Class_Base::_('Admin');
		$adminRow = $adminTb->createRow()->setFromArray(array(
			'subdomainId' => $this->id,
			'loginName' => 'admin',
			'password' => md5('123456'.MD5_SALT),
			'roleId' => 2
		))->save();
		
		$sectionTb = Class_Base::_('Category_Section');
		$sectionRow = $sectionTb->createRow()->setFromArray(array(
			'subdomainId' => $this->id,
			'name' => '一级目录-'.$this->label,
			'title' => '一级目录-'.$this->label
		))->save();
	}
}