<?php
abstract class Class_Brick_Solid_Package_Abstract implements Class_Brick_Interface
{
    protected $_request = null;
    protected $_brick = null;
    protected $_params = null;
    protected $_solidBrickArr = array();
    
    public function __construct($brick, Zend_Controller_Request_Abstract $request)
    {
    	$this->_request = $request;
    	$this->_brick = $brick;
        $this->_params = json_decode($brick->params);
    }
    
	public function getExtName()
    {
    	return $this->_brick->extName;
    }
    
    public function getPosition()
    {
    	return $this->_brick->position;
    }
    
    public function path()
    {
        $path = str_replace('_', '/', $this->_brick->extName);
        return '/brick/'.$path;
    }
    
    public function _buildBricks($brickIdArr)
    {
    	if(count($brickIdArr) == 0) {
    		$this->_solidBrickArr = array();
    		return false;
    	}
    	$brickTb = Class_Base::_tb('Brick');
		$selector = $brickTb->select()->where('brickId in ('.implode(',', $brickIdArr).')')->order('order');
		$brickRowset = $brickTb->fetchAll($selector);
        foreach($brickRowset as $brick) {
	        $solidBrick = $brick->createSolidBrick($this->_request);
	        $this->_solidBrickArr[] = $solidBrick;
		}
		return true;
    }
    
    public function render()
    {
//    	$brickIdArr = explode(',', $this->_params->brickIdArr);
//    	Zend_Debug::dump($this->_params->brickIdArr);
//    	die();
        $this->_buildBricks($this->_params->brickIdArr);
        $this->view = new Class_Brick_Solid_View(array('scriptPath' => CONTAINER_PATH.'/extension'.$this->path()));
        $this->view->addHelperPath(CONTAINER_PATH.'/extension/brick/helpers', 'Helper');
		
        $this->view->brickName = $this->_brick->brickName;
        $this->view->displayBrickName = $this->_brick->displayBrickName;
        $this->view->cssSuffix = $this->_brick->cssSuffix == null ? '' : '-'.$this->_brick->cssSuffix;
		$this->view->solidBrickArr = $this->_solidBrickArr;
        
		$this->prepare();
		return $this->view->render('view.phtml');
    }
    
	public function renderAdmin()
    {
    	if(is_file(CONTAINER_PATH.'/extension'.$this->path().'/admin.phtml')) {
			$this->view = new Class_Brick_Solid_View(array('scriptPath' => CONTAINER_PATH.'/extension'.$this->path()));
			$this->view->addHelperPath(CONTAINER_PATH.'/extension/brick/helpers', 'Helper');
	        
	        return $this->view->render('admin.phtml');
    	}
    	return "";
    }
    
    public function configParam($form)
    {
    	return $form;
    }
}