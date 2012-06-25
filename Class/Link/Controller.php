<?php
class Class_Link_Controller
{
	protected static $_renderer = null;
	
	protected $_links = array();
	protected $_head = null;
	
	public static function factory($type)
	{
		
//		if(is_null(self::$_acl)) {
//			$serializedAcl = null;
//			
//			$frontendOptions = array('lifetime' => null);
//			$backendOptions = array('cache_dir' => CACHE_PATH);
//			$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
//			
//			if(!$serializedAcl = $cache->load('serialized_acl_instance')) {
//				$serializedAcl = new self();
//				$serializedAcl->loadRules();
//				$cache->save(serialize($serializedAcl), 'serialized_acl_instance');
//				self::$_acl = $serializedAcl;
//			} else {
//				self::$_acl = unserialize($serializedAcl);
//			}
//		}
//		
//		return self::$_acl;
		
		
		
		
		
		
		$controller = null;
		
		switch($type) {
			case 'article':
				$tb = Class_Base::_('Group');
				$linkRowset = $tb->fetchAll($tb->select()->where('type = ?', 'article'));
				$controller = new self($linkRowset, "首页");
				break;
			case 'product':
				$tb = Class_Base::_('Group');
				$linkRowset = $tb->fetchAll($tb->select()->where('type = ?', 'product'));
				$controller = new self($linkRowset, "首页");
				break;
			case 'file':
				$tb = Class_Base::_('Group');
				$linkRowset = $tb->fetchAll($tb->select()->where('type = ?', 'file'));
				$controller = new self($linkRowset, "图片分组");
				break;
			default:
				throw new Exception('undefined link controller type: '.$type);
				break;
		}
		return $controller;
	}
	
	public function __construct(Zend_Db_Table_Rowset_Abstract $sourceRowset = null, $headSource = null)
	{
		if(!is_null($sourceRowset)) {
			if(is_null($headSource)) {
				$this->_head = new Class_Link();
			} else {
				$this->_head = new Class_Link($headSource);
			}
			$this->_links[0] = $this->_head;
			foreach($sourceRowset as $source) {
				$link = new Class_Link($source);
				$this->append($link);
			}
			
			$this->_buildConnection($this->_head);
		}
	}
	
	public function create(Zend_Db_Table_Rowset_Abstract $sourceRowset)
	{
		$this->_head = new Class_Link();
		$this->_links[0] = $this->_head;
		foreach($sourceRowset as $source) {
			$link = $source->toLink();
			$this->append($link);
		}
		
		$this->_buildConnection($this->_head);
	}
	
	protected function _buildConnection($parent)
	{
		$count = count($this->_links);
        if($count > 0) {
            $parentId = $parent->getId();
            foreach($this->_links as $k => $v) {
                if($v->getParentId() == $parentId) {
                    $parent->appendChild($v);
                    $this->_buildConnection($v);
                }
            }
        }
        $parent->sortChildren();
        return;
	}
	
	public function createLink(Class_Link_Interface $source = null)
	{
		$link = new Class_Link();
		if(!is_null($source)) {
			$link->setFromObject($source);
		}
		return $link;
	}
	
	public function append(Class_Link $link)
	{
		$id = $link->getId();
		$this->_links[$id] = $link;
		return $this;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * 
	 * @return Class_Link
	 */
	public function getLinkHead()
	{
		return $this->_head;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param Number $id
	 * 
	 * @return Class_Link
	 */
	public function getLink($id)
	{
		if(array_key_exists($id, $this->_links)) {
			return $this->_links[$id];
		}
		return null;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * 
	 * @return ArrayObject
	 */
	public function toMultiOptions()
	{
		$arr = array();
		foreach($this->_head->getChildren() as $cLink) {
			$this->_getChildrenAsMultiOptions($cLink, $arr, '');
		}
		return $arr;
	}
	
	public function getTrail($id)
    {
    	$link = $this->getLink($id);
    	if($link == null) {
    		return array();
    	} else {
	    	$trail = array();
	    	$trail[] = $link;
	    	$currentLink = $link->getParent();
	    	while($currentLink->getParent() != null) {
	    		$trail[] = $currentLink;
	    		$currentLink = $currentLink->getParent();
	    	}
	    	$trail[] = $currentLink;
	    	krsort($trail);
	    	return $trail;
    	}
    }
	
	protected function _getChildrenAsMultiOptions(Class_Link $link, &$arr, $prefix)
	{
		$arr[$link->getId()] = $prefix.$link->label;
		if($link->hasChildren()) {
			foreach($link->getChildren() as $cLink) {
				$this->_getChildrenAsMultiOptions($cLink, $arr, $prefix.'--');
			}
		}
		return ;
	}
	
	public static function getRenderer()
	{
		return self::$_renderer;
	}
	
	public static function setRenderer(Class_Link_Renderer_Abstract $renderer)
	{
		self::$_renderer = $renderer;
	}
}