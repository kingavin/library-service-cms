<?php 
class Class_Model_ClientManager_Role extends Zend_Db_Table
{
	public function getRules()
	{
	    return $this->_rules;
	}
	
	protected function _afterLoad()
	{
	    $ruleTable = new Zend_Db_Table('admin_rule');
	    $ruleSet = $ruleTable->fetchAll('roleId = '.$this->getData('roleId'));
	    $resource = array();
	    foreach($ruleSet as $rule) {
	        $resource[] = $rule->resource;
	    }
	    $this->rules = $resource;
	    $this->setData('resource', $resource);
	}
	
	protected function _afterSave()
	{
	    $ruleTable = new Zend_Db_Table('admin_rule');
	    $ruleTable->delete('roleId = '.$this->getData('roleId'));
	    
	    $resource = $this->getData('resource');
	    if(is_null($resource)) {
	        $resource = array();
	    }
	    
	    foreach($resource as $r) {
	        $ruleTable->insert(array(
	            'roleId' => $this->getData('roleId'),
	            'resource' => $r
	        ));
	    }
	}
}