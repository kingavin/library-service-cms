<?php
class Class_Brick_Solid_View extends Zend_View_Abstract
{
	protected $_params;
	protected $_globalParams;
	
	protected $_brick;
	
	public function __construct($config = array(), $params = NULL, $globalParams = NULL)
	{
		$this->_params = $params;
		$this->_globalParams = $globalParams;
		parent::__construct($config);
	}
	
	public function getParam($key, $defaultValue = NULL)
    {
    	$params = $this->_params;
    	if(isset($params->$key)) {
    		$temp = $params->$key;
    		if($params->$key == 'global' && isset($this->_globalParams->$key)) {
    			$temp = $this->_globalParams->$key;
    		}
    		return $temp;
    	}
    	return $defaultValue;
    }
	
    protected function _run()
    {
        include func_get_arg(0);
    }
    
    public function setBrickRow($brick)
    {
    	$this->_brick = $brick;
    }
    
    public function __get($key)
    {
    	//make a parent call to throw exceptions
    	$parentCall = parent::__get($key);
    	
    	if(isset($this->_brick->$key)) {
    		return $this->_brick->$key;
    	}
    }
}