<?php
namespace Fucms\Layout;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Front implements ServiceLocatorAwareInterface
{
	public $sm = null;
	
	protected $_routeMatch = null;
	
	protected $_layoutRow = null;
	
	public function setRouteMatch($routeMatch)
	{
		$this->_routeMatch = $routeMatch;
	}
	
	public function isDisplayHead()
	{
		$layoutRow = $this->getLayoutRow();
		if(isset($layoutRow->displayHead)) {
			return $layoutRow->displayHead;
		}
		return 1;
	}
	
	public function getLayoutDoc()
	{
		if($this->_layoutRow == null) {
			$routeName = $this->_routeMatch->getMatchedRouteName();
			$resouceId = $this->_routeMatch->getParam('id');
			$layoutAlias = $this->_routeMatch->getParam('layoutAlias');
		
			$mongoFactory = $this->sm->get('Core\Mongo\Factory');
			
//			$layoutTable = Class_Base::_('Layout');
			$layoutCo = $mongoFactory->_m('Layout');
			$layoutRow = null;
			
			
//			$selector = $layoutTable->select();
			$layoutDoc = null;
			switch($routeName) {
				case 'application':
					$layoutDoc = $layoutCo->addFilter('controllerName', 'index')
						->fetchOne();
						
					if(is_null($layoutDoc) && in_array($layoutAlias, array('index','article','list','product','product-list'))) {
						$layoutDoc = $layoutCo->create();
						$layoutDoc->controllerName = $layoutAlias;
						$layoutDoc->layoutAlias = $layoutAlias;
						$layoutDoc->moduleName = 'default';
						$layoutDoc->isDisplayHead = 1;
						$layoutDoc->default = 1;
						$layoutDoc->type = $layoutAlias;
						$layoutDoc->save();
					}
					break;
				case 'user':
					$layoutDoc = $layoutCo->addFilter('moduleName', 'user')
						->fetchOne();
					if(is_null($layoutDoc)) {
						$layoutDoc = $layoutCo->create();
						$layoutDoc->moduleName = 'user';
						$layoutDoc->controllerName = 'index';
						$layoutDoc->isDisplayHead = 1;
						$layoutDoc->default = 1;
						$layoutDoc->type = 'user';
						$layoutDoc->save();
					}
					break;
				case 'shop':
					$layoutDoc = $layoutCo->addFilter('moduleName', 'shop')
						->addFilter('controllerName', $controllerName)
						->fetchOne();
					if(is_null($layoutDoc)) {
						if(in_array($controllerName, array('index', 'order', 'payment-gateway'))) {
							$layoutDoc = $layoutCo->create();
							$layoutDoc->moduleName = 'user';
							$layoutDoc->controllerName = $controllerName;
							$layoutDoc->isDisplayHead = 1;
							$layoutDoc->default = 1;
							$layoutDoc->type = 'user';
							$layoutDoc->save();
						}
					}
					break;
			}
			
			if(is_null($layoutDoc)) {
				throw new Exception("layout settings not found with given layoutName ".$controllerName);
			}
			$this->_layoutRow = $layoutDoc;
		}
		return $this->_layoutRow;
	}
	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->sm = $serviceLocator;
	}
	
	public function getServiceLocator()
	{
		return $this->sm;
	}
}