<?php
class Class_Plugin_LayoutSwitch extends Zend_Controller_Plugin_Abstract
{
	protected $_layout;
	
	public function __construct($layout)
	{
		$this->_layout = $layout;
	}
	
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
		if($request->getParam('local-css-mode') == 'activate') {
			$csa = Class_Session_Admin::getInstance();
			$csa->setSessionData('localCssMode', 'active');
		}
    	if($request->getParam('local-css-mode') == 'deactivate') {
			$csa = Class_Session_Admin::getInstance();
			$csa->setSessionData('localCssMode', null);
		}
		
    	if($request->getModuleName() == 'admin' && ($request->isXmlHttpRequest())) {
    		$this->_layout->setLayout('template-front');
    	}
    	if($request->getModuleName() == 'default' && ($request->isXmlHttpRequest())) {
    		$this->_layout->setLayout('ajax-template');
    	}
    	if($request->getParam('layout') == 'disable') {
    		$this->_layout->disableLayout();
    	}
    }
}