<?php
abstract class Class_Brick_Solid_Abstract implements Class_Brick_Interface
{
    protected $_request = null;
    protected $_brick = null;
    protected $_params = null;
    protected $_scriptName = 'view.phtml';
    protected $_disableRender = false;
    protected $_gearLinks = array();
    
    protected $_useTwig = false;
    
    public function __construct($brick, Zend_Controller_Request_Abstract $request)
    {
    	$this->_request = $request;
    	$this->_brick = $brick;
        $this->_params = (object)$brick->params;
        
        $this->_init();
    }
    
    public function _init(){}
    
    public function getExtName()
    {
    	return $this->_brick->extName;
    }
    
	public function getBrickId()
    {
    	return $this->_brick->brickId;
    }
    
    public function getBrickName()
    {
    	return $this->_brick->brickName;
    }
    
    public function getPosition()
    {
    	return $this->_brick->position;
    }
    
    public function getSpriteName()
    {
    	return $this->_brick->spriteName;
    }
    
	public function getParam($key, $defaultValue = NULL)
    {
    	$params = $this->_params;
    	if(isset($params->$key)) {
    		$temp = $params->$key;
    		return $temp;
    	}
    	return $defaultValue;
    }
    
    public function setParam($key, $value)
    {
    	$this->_params->$key = $value;
    	return true;
    }
    
    public function setParams($src, $type = 'array')
    {
    	if(!empty($src)) {
	    	if($type == 'json') {
	    		$src = Zend_Json_Decoder::decode($src);
	    	}
	    	foreach($src as $key => $value) {
	    		if(!empty($value)) {
	    			$this->_params->$key = $value;
	    		}
	    	}
    	}
    }
    
    public function setScriptFile($filename)
    {
    	$this->_scriptName = $filename;
    }
    
    public function path()
    {
        $path = str_replace('_', '/', $this->_brick->extName);
        return '/brick/'.$path;
    }
    
    public function twigPath()
    {
    	//$path = str_replace('_', '/', $this->_brick->extName);
        return '/'.$this->_brick->extName;
    }
    
    public function render($type = null)
    {
    	if($this->_disableRender === true) {
	        return "<div class='no-render'></div>";
    	} else if(is_string($this->_disableRender)) {
    		return "<div class='".$this->_disableRender."' brickId='".$this->_brick->brickId."'>无法找到对应的URL，此模块内容为空</div>";
    	} else {
	    	if($this->_brick->tplName == "") {
	    		return $this->renderZend();
	    	} else {
	    		return $this->renderTwig();
	    	}	    	
    	}
    }
    
    public function renderZend()
    {
    	$this->view = new Class_Brick_Solid_View(
        	array('scriptPath' => CONTAINER_PATH.'/extension'.$this->path()),
        	$this->_params
        );
        $this->view->addHelperPath(CONTAINER_PATH.'/extension/brick/helpers', 'Helper');
    	$this->prepare();
		if($this->_disableRender == 'no-resource') {
			return "<div class='no-resource'>暂无内容</div>";
		} else {
			$this->view->brickName = $this->_brick->brickName;
			$this->view->brickId = $this->_brick->brickId;
			$this->view->displayBrickName = $this->_brick->displayBrickName;
			$this->view->cssSuffix = $this->_brick->cssSuffix == null ? '' : ' '.$this->_brick->cssSuffix;
			$this->view->effect = $this->_brick->effect;
			
			$this->view->setBrickRow($this->_brick);
			$this->view->gearLinks = $this->_gearLinks;
			return $this->view->render($this->_scriptName);
		}
	}

	public function renderTwig()
	{
		$this->view = new Class_Brick_Solid_TwigView();
		$this->view->setScriptPath(CONTAINER_PATH.'/extension'.$this->path());
		$this->view->assign($this->_params);
		if(is_dir(TEMPLATE_PATH.$this->twigPath())) {
			$this->view->addScriptPath(TEMPLATE_PATH.$this->twigPath());
		}
		$this->prepare();
		
		if($this->_disableRender === true) {
			return "<div class='no-render'></div>";
		} else if($this->_disableRender == 'no-resource') {
			return "<div class='no-resource'>暂无内容</div>";
		} else {
			$this->view->setBrickId($this->_brick->getId())
				->setExtName($this->_brick->extName)
				->setClassSuffix($this->_brick->cssSuffix);
			
			$this->view->brickName = $this->_brick->brickName;
			$this->view->brickId = $this->_brick->brickId;
			$this->view->displayBrickName = $this->_brick->displayBrickName;
			$this->view->effect = $this->_brick->effect;
			
			$this->_prepareGearLinks();
			$this->view->setGearLinks($this->_gearLinks);
			try {
				return $this->view->render($this->_brick->tplName);
			} catch(Exception $e) {
				return "critical error within brick id: ".$this->_brick->brickId.'!!<br /><a href="#/admin/brick/edit/brick-id/'.$this->_brick->brickId.'">reset parameters</a>';
			}
		}
	}
    
    public function renderAdmin()
    {
    	return "";
    }
    
    public function configTplOptions($form)
    {
    	$tplArray = array();
    	
    	$systemFolder = CONTAINER_PATH.'/extension'.$this->path();
    	$handle = opendir($systemFolder);
    	while($file = readdir($handle)) {
    		if(strpos($file, '.tpl')) {
    			$tplArray[$file] = $file;
    		}
    	}
    	$userFolder = TEMPLATE_PATH.$this->twigPath();
    	$userTplArray = array();
		if(is_dir($userFolder)) {
			$handle = opendir($userFolder);
	    	while($file = readdir($handle)) {
	    		if(strpos($file, '.tpl')) {
	    			$userTplArray[$file] = $file;
	    		}
	    	}
		}
		if(count($userTplArray) > 0) {
			$tplArray = array('system' => $tplArray, 'user' => $userTplArray);
		}
    	
    	$form->tplName->setMultiOptions($tplArray);
    	return $form;
    }
    
    public function getTplArray()
    {
    	$sysTplArray = array();
    	$userTplArray = array();
    	
    	$systemFolder = CONTAINER_PATH.'/extension'.$this->path();
    	$handle = opendir($systemFolder);
    	while($file = readdir($handle)) {
    		if(strpos($file, '.tpl')) {
    			$sysTplArray[$file] = $file;
    		}
    	}
    	$userFolder = TEMPLATE_PATH.$this->twigPath();
    	if(is_dir($userFolder)) {
			$handle = opendir($userFolder);
	    	while($file = readdir($handle)) {
	    		if(strpos($file, '.tpl')) {
	    			$userTplArray[$file] = $file;
	    		}
	    	}
		}
//    	if($flat) {
//    		if(is_dir($userFolder)) {
//				$handle = opendir($userFolder);
//		    	while($file = readdir($handle)) {
//		    		if(strpos($file, '.tpl')) {
//		    			$systemFolder[$file] = $file;
//		    		}
//		    	}
//			}
//    	} else {
//			if(is_dir($userFolder)) {
//				$handle = opendir($userFolder);
//		    	while($file = readdir($handle)) {
//		    		if(strpos($file, '.tpl')) {
//		    			$userTplArray[$file] = $file;
//		    		}
//		    	}
//			}
//    	}
//    	if(count($userTplArray) > 0) {
		$tplArray = array('system' => $sysTplArray, 'user' => $userTplArray);
//		}
    	return $tplArray;
    }
    
    public function configParam($form)
    {
    	return $form;
    }
    
    public function getGlobalForm()
    {
    	return new Zend_Form();
    }
    
    /**
     * ready gear links
     * 
     * empty function for inheritance
     */
    protected function _prepareGearLinks()
    {
    	return array();
    }
    
    /**
     * append new gear link
     * 
     * @param string $lable
     * @param string $href
     */
    protected function _addGearLink($lable, $href)
    {
		$link = array('label' => $lable, 'href' => $href);
    	array_push($this->_gearLinks, $link);
    	return $this;
    }
    
    public function getCacheId()
    {
    	return null;
    }
}