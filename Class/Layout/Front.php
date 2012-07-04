<?php
class Class_Layout_Front
{
	protected static $_instance = null;
	
	protected $_controllerName = null;
	
	protected $_actionName = null;
	
	protected $_pageAlias = null;
	
	protected $_layoutRow = null;
	
	protected $_request = null;
	
	/**
	 * 
	 * load a resource type depending on type of current layout
	 * @var Zend_Db_Table_Row
	 */
	protected $_resource = null;
	
	protected function __construct()
	{
		$this->_request = Zend_Controller_Front::getInstance()->getRequest();
		
		$this->setControllerName($this->_request->getControllerName())
			->setActionName($this->_request->getActionName());
	}
	
	private function __clone(){}
	
	/**
	 * @return Class_Layout_Front
	 * Enter description here ...
	 */
	public static function getInstance()
	{
		if (null === self::$_instance) {
            self::$_instance = new self();
        }
		return self::$_instance;
	}
	
	public function setControllerName($controllerName)
	{
		if(strpos($controllerName, '.shtml') > 0) {
			$this->_controllerName = substr($controllerName, 0, -6);
		} else {
			$this->_controllerName = $controllerName;
		}
		if(is_null($this->_pageAlias)) {
			$this->_pageAlias = $this->getCurrentControllerName();
		}
		return $this;
	}
	
	public function setActionName($actionName)
	{
		if(strpos($actionName, '.shtml') > 0) {
			$this->_actionName = substr($actionName, 0, -6);
		} else {
			$this->_actionName = $actionName;
		}
		$this->_pageAlias = $this->getCurrentActionName();
		return $this;
	}
	
	public function getCurrentControllerName()
	{
		return $this->_controllerName;
	}
	
	public function getCurrentActionName()
	{
		return $this->_actionName;
	}
	
	public function getCurrentModuleName()
	{
		return $this->_request->getModuleName();
	}
	
	public function setPageAlias($pageAlias)
	{
		$this->_pageAlias = $pageAlias;
		return $this;
	}
	
	public function getPageAlias()
	{
		if(!empty($this->_actionName)) {
			return '/'.$this->getCurrentControllerName().'/'.$this->getCurrentActionName().'.shtml';
		} else if(!empty($this->_controllerName)) {
			return '/'.$this->getCurrentControllerName().'.shtml';
		} else {
			return null;
		}
	}
	
	public function getLayoutRow()
	{
		$moduleName = $this->_request->getModuleName();
		if($moduleName == 'admin' || $moduleName == 'rest') {
			return null;
		}
		if($this->_layoutRow == null) {
//			$layoutTable = Class_Base::_('Layout');
			$layoutCo = App_Factory::_m('Layout');
			$layoutRow = null;
			
//			if(empty($this->_actionName)) {
//				//get generic layout
//				$selector = $layoutTable->select()->where('controllerName = ?', $this->getCurrentControllerName());
//				$layoutRow = $layoutTable->fetchRow($selector);
//			} else {
//				//try to get specific layout for action
//				$selector = $layoutTable->select()->where('controllerName = ?', $this->getCurrentControllerName())
//					->where('actionName = ?', $this->getCurrentActionName());
//				$layoutRow = $layoutTable->fetchRow($selector);
//				
//				if(is_null($layoutRow)) {
//					//get generic layout if specific layout not found for action
//					$selector = $layoutTable->select()->where('controllerName = ?', $this->getCurrentControllerName());
//					$layoutRow = $layoutTable->fetchRow($selector);
//				}
//			}
			
			
			$controllerName = $this->getCurrentControllerName();
//			$selector = $layoutTable->select();

			switch($moduleName) {
				case 'default':
				case '':
//					$selector = $layoutTable->select()->where('controllerName = ?', $this->getCurrentControllerName())
//						->where('moduleName = ?', 'default');
					$layoutDoc = $layoutCo->addFilter('controllerName', $this->getCurrentControllerName())
						->fetchOne();
						
					if(is_null($layoutDoc) && in_array($controllerName, array('index','article','list','product','product-list'))) {
						$layoutDoc = $layoutCo->create();
						$layoutDoc->controllerName = $this->getCurrentControllerName();
						$layoutDoc->moduleName = 'default';
						$layoutDoc->isDisplayHead = 1;
						$layoutDoc->default = 1;
						$layoutDoc->type = $controllerName;
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
			
//			$layoutRow = $layoutTable->fetchRow($selector);
			$this->_layoutRow = $layoutDoc;
		}
		if(is_null($this->_layoutRow)) {
			throw new Exception("layout settings not found with given layoutName");
		}
		return $this->_layoutRow;
	}
	
	public function getResource()
	{
		$module = $this->_request->getModuleName();
		if(is_null($this->_resource) && $module == 'default') {
			$controllerName = $this->getCurrentControllerName();
			$actionName = $this->getCurrentActionName();
			
			$layoutRow = $this->getLayoutRow();
			if(is_null($layoutRow)) {
				return null;
			}
			if($layoutRow->type == 'frontpage') {
				$this->_resource = 'none';
				return $this->_resource;
			}
			
			$id = $actionName;
			$dbType = 'mysql';
			switch($layoutRow->type) {
				case 'article':
					$dbType = 'mongo';
					$co = App_Factory::_m('Article');
					break;
				case 'list':
					$dbType = 'mongo';
					$co = App_Factory::_m('Group_Item');
					break;
				case 'product':
					$dbType = 'mongo';
					$co = App_Factory::_m('Product');
					break;
				case 'product-list':
					$dbType = 'mongo';
					$co = App_Factory::_m('Group_Item');
					break;
				case 'book':
					$dbType = 'mongo';
					$co = App_Factory::_m('Book');
					break;
			}
			
			if($layoutRow->default == 1) {
				if($dbType == 'mysql') {
					$this->_resource = $tb->find($id)->current();
				} else if($dbType == 'mongo') {
					$this->_resource = $co->find($id);
				}
			} else {
				
				if($layoutRow->type == 'article') {
					$articleDoc = $co->addFilter('link', '/'.$controllerName.'/'.$actionName.'.shtml')
						->fetchOne(); //$tb->select()->where('link = ?', '/'.$controllerName.'/'.$actionName.'.shtml');
					$this->_resource = $articleDoc;
				} else if($layoutRow->type == 'list') {
//					$selector = $tb->select()->where('alias = ?', '/'.$controllerName.'.shtml');
//					if(empty($actionName)) {
//						$page = 1;
//					} else {
//						$page = substr($actionName, 4);
//					}
//					
//					$this->_request->setParam('page', $page);
//					$this->_resource = $tb->fetchRow($selector);
				} else if($layoutRow->type == 'book') {
					$bookDoc = $co->addFilter('alias', $controllerName)
						->fetchOne();
					$this->_resource = $bookDoc;
				}
			}
			
			if($this->_resource == null && $actionName == 'index') {
				$this->_resource = 'none';
			}
		}
		
		return $this->_resource;
	}
	
	public function getType()
	{
		$layoutRow = $this->getLayoutRow();
		return $layoutRow->type;
	}
	
	public function isDisplayHead()
	{
		$layoutRow = $this->getLayoutRow();
		if(isset($layoutRow->displayHead)) {
			return $layoutRow->displayHead;
		}
		return 1;
	}
}