<?php
class Class_Plugin_ResourceAcl extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    	$allowed = true;
    	
    	$csu = Class_Session_User::getInstance();
    	if(!$csu->isLogin()) {
			$clf = Class_Layout_Front::getInstance();
			$resourceType = $clf->getType();
			if($resourceType == 'product' || $resourceType == 'article') {
				//read settings from group
			}
			if($resourceType == 'product-list' || $resourceType == 'list') {
				$re = $clf->getResource();
				$allowed = $re->isAllowed;
			}
    	}
    	
    	if(!$allowed) {
    		header("Location: /user/login");
    		exit();
    	}
    }
}