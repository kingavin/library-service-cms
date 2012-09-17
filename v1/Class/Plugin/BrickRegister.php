<?php
class Class_Plugin_BrickRegister extends Zend_Controller_Plugin_Abstract
{
    private $_registed = false;
    
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
		$bricks = $request->getParam('bricks');
		$type = $request->getModuleName();
		
		switch($type) {
			case 'default':
			case "":
				$locale = Zend_Registry::get('Locale');
				$translate = new Zend_Translate(
					array('adapter' => 'gettext', 'content' => CONTAINER_PATH.'/languages/default/zh_CN.mo', 'locale' => 'zh_CN')
				);
				$translate->addTranslation(
					array('content' => CONTAINER_PATH.'/languages/default/en_US.mo', 'locale' => 'en_US')
				);
				$translate->setLocale($locale);
				Zend_Registry::set('Zend_Translate', $translate);
				break;
			case 'user':
				$locale = Zend_Registry::get('Locale');
				$translate = new Zend_Translate(
					array('adapter' => 'gettext', 'content' => CONTAINER_PATH.'/languages/user/zh_CN.mo', 'locale' => 'zh_CN')
				);
				$translate->addTranslation(
					array('content' => CONTAINER_PATH.'/languages/user/en_US.mo', 'locale' => 'en_US')
				);
				$translate->setLocale($locale);
				Zend_Registry::set('Zend_Translate', $translate);
				break;
		}
		
		
		if($this->_registed != true && $bricks != 'disabled') {
            $controllerName = $this->getRequest()->getControllerName();
			$actionName = $this->getRequest()->getActionName();
				
            if($type == 'admin' || $type == 'forbidden' || $type == 'rest') {
            	
            } else {
            	$layoutFront = Class_Layout_Front::getInstance();
            	$layoutRow = $layoutFront->getLayoutRow();
				
                $layoutId = $layoutRow->getId();
	            $co =App_Factory::_m('Brick');
	            if($layoutFront->isDisplayHead() == 1) {
					$co->addFilter('$or', array(
							array('layoutId' => $layoutId),
							array('layoutId' => 0))
						)
						->addFilter('active', 1)
						->sort('sort');
	            } else {
					$co->addFilter('layoutId', $layoutId)
						->addFilter('active', 1)
						->sort('sort');
	            }
				$brickDocs = $co->fetchDoc();
				
	            $cbc = Class_Brick_Controller::getInstance();
	            foreach($brickDocs as $brick) {
	                $cbc->registerBrick($brick, $request);
	            }
	            
	            if($request->getModuleName() == 'default' || $request->getModuleName() == '') {
	            	$request->setControllerName('index');
            		$request->setActionName('index');
	            }
            }
            
            $this->_registed = true;
        }
    }
}