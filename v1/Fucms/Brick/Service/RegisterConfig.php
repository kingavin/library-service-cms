<?php
namespace Fucms\Brick\Service;

use Zend\Mvc\MvcEvent;
use Fucms\Brick\Register;

class RegisterConfig
{
//	private $_registed = false;
	
	protected $_sm = null;
	
	public function __construct($sm)
	{
		$this->_sm = $sm;
	}
	
	public function configRegister($register)
	{
		$lf = $this->_sm->get('Fucms\Layout\Front');
    	
    	$layoutDoc = $lf->getLayoutDoc();
		
		
		$mongoFactory = $this->_sm->get('Core\Mongo\Factory');
		$co = $mongoFactory->_m('Brick');
		//$lf = $sm->get('Fucms\Layout\Front');
		
		//$lf->setRoute($route);
		
		//$layoutRow = $lf->getLayoutRow();
		
		
		//$routeName = $route->getMatchedRouteName();
		
//		if($this->_registed != true) {
//			$this->_registed = true;
			
			$layoutId = $layoutDoc->getId();
			
			
			//echo $layoutId;
			
			
			if($layoutDoc->isDisplayHead == 1) {
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
			
			foreach($brickDocs as $brick) {
				$register->registerBrick($brick);
			}
//		}
	}
}