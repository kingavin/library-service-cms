<?php
namespace Fucms\Plugin;

use Zend\Mvc\MvcEvent;

class BrickRegister
{
	private $_registed = false;
	
	public function postDispatch(MvcEvent $e)
	{
		echo 'run!!<br />';
		
		$route = $e->getRouteMatch();
		
		$sm = $e->getApplication()->getServiceManager();
		
		$mongoFactory = $sm->get('Core\Mongo\Factory');
		
		$lf = $sm->get('Fucms\Layout\Front');
		
		$lf->setRoute($route);
		
		$layoutRow = $lf->getLayoutRow();
		
		
		$routeName = $route->getMatchedRouteName();
		
		if($this->_registed != true) {
			$this->_registed = true;
			
			$layoutId = $layoutRow->getId();
			$co = $mongoFactory->_m('Brick');
			
			//echo $layoutId;
			
			
			if($lf->isDisplayHead() == 1) {
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
				//$this->registerBrick($brick, $route);
			}
		}
	}
}