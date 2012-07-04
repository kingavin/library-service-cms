<?php
class Class_Acl extends Zend_Acl
{
	protected static $_acl = null;
	
	protected function __construct() {}
	
	protected function __clone() {}
	
	public function loadRules()
	{
		$db = Zend_Registry::get('siteDb');
		$resTb = new Zend_Db_Table(array('db' => $db, 'name' => 'resource'));
		$resRowset = $resTb->fetchAll();
		
		foreach($resRowset as $resRow) {
			$this->addResource($resRow->controllerName);
		}
		
		$this->addRole(new Zend_Acl_Role('nobody'));
		$this->deny('nobody', null);
		$this->allow('nobody', 'index', 'login');
		$this->allow('nobody', 'index', 'designer-login');

		$this->addRole(new Zend_Acl_Role(0));
		$this->allow(0, null);

		$this->allow(null, 'index', 'logout');

		$groupTb = new Zend_Db_Table('admin_group');
		$groupRowset = $groupTb->fetchAll();
		foreach($groupRowset as $group) {
			$this->addRole($group->id);
		}
		$allowedResTb = new Zend_Db_Table('admin_rule');
		$allowedResRowsets = $allowedResTb->fetchAll();
		foreach($allowedResRowsets as $resRow) {
			$this->allow($resRow->roleId, $resRow->resource);
		}
	}
	
	public static function getInstance()
	{
		if(is_null(self::$_acl)) {
			$serializedAcl = null;
			
			$frontendOptions = array('lifetime' => null);
			$backendOptions = array('cache_dir' => CACHE_PATH);
			$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
			
			if(!$serializedAcl = $cache->load('serialized_acl_instance')) {
				$serializedAcl = new self();
				$serializedAcl->loadRules();
				$cache->save(serialize($serializedAcl), 'serialized_acl_instance');
				self::$_acl = $serializedAcl;
			} else {
				self::$_acl = unserialize($serializedAcl);
			}
		}
		
		return self::$_acl;
	}
}