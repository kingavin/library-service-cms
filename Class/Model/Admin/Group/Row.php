<?php
class Class_Model_Admin_Group_Row extends Zend_Db_Table_Row_Abstract
{
	protected $_rules;
	
	public function getRules()
	{
		$ruleTable = new Zend_Db_Table('admin_rule');
	    $ruleSet = $ruleTable->fetchAll('roleId = '.$this->id);
	    $resource = array();
	    foreach($ruleSet as $rule) {
	        $resource[] = $rule->resource;
	    }
	    $this->_rules = $resource;
	    return $this->_rules;
	}
}