<?php
class Class_Plugin_BrickRegister extends Zend_Controller_Plugin_Abstract
{
    private $_registed = false;
    
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
		$bricks = $request->getParam('bricks');
		
		if($this->_registed != true && $bricks != 'disabled') {
            $type = $request->getModuleName();
            $controllerName = $this->getRequest()->getControllerName();
			$actionName = $this->getRequest()->getActionName();
				
            if($type == 'admin' || $type == 'forbidden' || $type == 'rest') {
            	
            } else {
            	$layoutFront = Class_Layout_Front::getInstance();
            	$layoutRow = $layoutFront->getLayoutRow();
				
                $layoutId = $layoutRow->getId();
	            $brickTb = Class_Base::_tb('Brick');
	            $co =App_Factory::_m('Brick');
	            if($layoutFront->isDisplayHead() == 1) {
//					$selector = $brickTb->select(false)
//						->from(array('b' => 'brick'), array('*', 'isnull' => new Zend_Db_Expr('`b`.`sort` IS NULL')))
//						->where('layoutId = ? or layoutId = 0', $layoutId)
//						->where('active = ?', 1)
//						->order('isnull ASC')
//						->order('sort ASC');
//					$docs = $co->fetchTest();
//					
//					foreach($docs as $d) {
//						Zend_Debug::dump($d);
//					}
					
					$co->addFilter('$or', array(
							array('layoutId' => $layoutId),
							array('layoutId' => 0))
						)
						->addFilter('active', 1)
						->sort('weight');
	            } else {
//	            	$selector = $brickTb->select(false)
//						->from(array('b' => 'brick'), array('*', 'isnull' => new Zend_Db_Expr('`b`.`sort` IS NULL')))
//						->where('layoutId = ?', $layoutId)
//						->where('active = ?', 1)
//						->order('isnull ASC')
//						->order('sort ASC');
					$co->addFilter('layoutId', $layoutId)
						->addFilter('active', 1)
						->sort('weight');
	            }
//	            $brickRowset = $brickTb->fetchAll($selector);
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