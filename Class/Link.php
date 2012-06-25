<?php
class Class_Link
{
	protected $_parent = null;
	protected $_children = array();
	protected $_hasChildren = false;
	protected $_data = null;
	
	protected $_id = null;
	protected $_parentId = null;
	protected $_order = 0;
	protected $_href = "";
	
	/**
	 * create a link class with all the original data
	 * 
	 * @param Class_Link_Interface $rowObj
	 * @throws Exception
	 */
	public function __construct($rowObj = null)
	{
		if(is_null($rowObj)) {
			$this->_data = array('label' => 'ROOT');
			$this->_id = 0;
			$this->_parentId = -1;
		} else if(is_string($rowObj)) {
			$this->_data = array('label' => $rowObj);
			$this->_id = 0;
			$this->_parentId = -1;
			$this->_href = '/';
		} else if($rowObj instanceof Class_Link_Interface) {
			$this->_data = $rowObj->toArray();
			$this->_id = $rowObj->getId();
			$this->_parentId = $rowObj->getParentId();
			$this->_order = $rowObj->getOrder();
			$this->_href = $rowObj->getHref();
		} else {
			throw new Exception('Argument 1 passed to Class_Link::__construct() must implement interface Class_Link_Interface');
		}
	}
	
	public function getId()
	{
		return $this->_id;
	}
	
	public function setId($value)
	{
		$this->_id = $value;
		return $this;
	}
	
	public function getParentId()
	{
		return $this->_parentId;
	}
	
	public function setParentId($value)
	{
		$this->_parentId = $value;
		return $this;
	}
	
	public function getOrder()
	{
		return $this->_order;
	}
	
	public function setOrder($value)
	{
		$this->_order = $value;
		return $this;
	}
	
	public function setHref($val)
    {
    	$this->_href = $val;
    	return $this;
    }
    
    public function getHref()
    {
    	return $this->_href;
    }
	
//	public function setFromObject(Class_Link_Interface $source)
//	{
//		$this->id = $source->getId();
//		$this->parentId = $source->getParentId();
//		$this->order = $source->getOrder();
//		$this->label = $source->getLabel();
//		$this->href = $source->getHref();
//		$this->title = $source->getTitle();
////		$this->assemble = $source->getAssemble();
//		return $this;
//	}
//	
//	public function setFromArray($arr)
//	{
//		$this->id = $arr['id'];
//		$this->parentId = $arr['parentId'];
//		$this->order = $arr['order'];
//		$this->label = $arr['label'];
//		$this->href = $arr['href'];
//		$this->title = $arr['title'];
////		$this->assemble = $arr['assemble'];
//		return $this;
//	}
	
    public function getParent()
    {
    	return $this->_parent;
    }
    
	public function setParent(Class_Link $link)
	{
		$this->_parent = $link;
	}
	
	public function appendChild(Class_Link $link)
	{
		if($link->_parentId == $this->_id) {
			$this->_children[] = $link;
			$link->_parent = $this;
			$this->_hasChildren = true;
		}
		return $this;
	}
	
	public function hasChildren()
    {
        return $this->_hasChildren;
    }
	
    public function getChildren()
    {
    	return $this->_children;
    }
    
	public function sortChildren()
    {
        $this->_bubbleSort(&$this->_children);
        return $this;
    }
    
    protected function _bubbleSort(&$array)
    {
		$count = count($array);
		if ($count <= 0)
			return false;
		for($i = 0; $i < $count; $i++) {
			for($j = $count - 1; $j > $i; $j--)	{
				$jKey = intval($array[$j]->_order);
				$jmKey = intval($array[$j-1]->_order);
				if($jKey < $jmKey) {
					$tmp = $array[$j];
					$array[$j] = $array[$j-1];
					$array[$j-1] = $tmp;
				}
			}
		}
    }
    
    public function getTrail()
    {
    	$trail = array();
    	$trail[] = $this;
    	$currentLink = $this->_parent;
    	while($currentLink->_parent != null) {
    		$trail[] = $currentLink;
    		$currentLink = $currentLink->_parent;
    	}
    	krsort($trail);
    	return $trail;
    }
    
	public function render()
    {
    	$renderer = Class_Link_Controller::getRenderer();
    	
        ob_start();
        $renderer->run($this);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
    
    public function getData($key)
    {
    	if($key == 'id' || $key == 'parentId' || $key == 'order') {
			throw new Exception('please use a get method instead of direct call');
		}
		if(!array_key_exists($key, $this->_data)) {
			throw new Exception('required "'.$key.'" is not set in Class_Link data');
		}
		return $this->_data[$key];
    }
    
	public function __set($key, $value)
	{
		if($key == 'id' || $key == 'parentId' || $key == 'order') {
			throw new Exception('please use a set method instead of direct call');
		}
		$this->_data[$key] = $value;
	}
	
	public function __get($key)
	{
		if($key == 'id' || $key == 'parentId' || $key == 'order') {
			throw new Exception('please use a get method instead of direct call');
		}
		if(!array_key_exists($key, $this->_data)) {
			throw new Exception('required key: "'.$key.'" is not set in Class_Link data');
		}
		return $this->_data[$key];
	}
}