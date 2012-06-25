<?php
class Class_Brick_Controller
{
	static private $_instance;
	
    protected $_solidBrickList = array();
    protected $_brickNameList = array();
    protected $_extensionParams = array();
    protected $_jsList = array();
    protected $_cssList = array();
    protected $_cache = null;
    
    private function __construct()
    {
    	$frontendOptions = array(
	       'lifetime' => 7200
	    );
	    $backendOptions = array(
	        'cache_dir' => CACHE_PATH
	    );
	    $this->_cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
    }
    
    private function __clone() {}
    
    /**
     * @return Class_Brick_Controller
     * Enter description here ...
     */
    static public function getInstance()
    {
    	if(!self::$_instance) {
    		self::$_instance = new self();
    	}
    	return self::$_instance;
    }
    
    public function createSolidBrick($brick, Zend_Controller_Request_Abstract $request)
    {
    	if($brick instanceof Class_Model_Brick_Row) {
    		
    	} else if(is_string($brick)) {
    		$co = App_Factory::_m('Brick');
			$brickRow = $co->create();
			$brickRow->setFromArray(array('extName' => $brick));
			$solidBrick = $brickRow->createSolidBrick($request);
		    return $solidBrick;
    	}
    }
    
    public function registerBrick(
        $brick,
        Zend_Controller_Request_Abstract $request)
    {
        $solidBrick = $brick->createSolidBrick($request);
		$this->_solidBrickList[] = $solidBrick;
        return true;
    }
    
    public function getBrickList($spriteName = null)
    {
    	if(is_null($spriteName)) {
        	return $this->_solidBrickList;
    	} else {
    		$solidBrickList = $this->_solidBrickList;
	    	$returnBricks = array();
			foreach($solidBrickList as $solidBrick) {
				if($solidBrick->getSpriteName() == $spriteName) {
					$returnBricks[] = $solidBrick;
				}
			}
			return $returnBricks;
    	}
    }
    
    public function getJsList()
    {
        return $this->_jsList;
    }
    
    public function getCssList()
    {
        return $this->_cssList;
    }
    
    public function renderBrick($brickId)
    {
    	$solidBrickList = $this->_solidBrickList;
    	
    	$brickHTML = "没有找到对应brick-id:".$brickId."的内容";
    	foreach($solidBrickList as $solidBrick) {
    		if($solidBrick->getBrickId() == $brickId) {
    			$brickHTML = $solidBrick->render();
    			break;
    		}
    	}
    	return $brickHTML;
    }
    
	public function render($position)
	{
	    if(!is_null($position)) {
	        if(array_key_exists($position, $this->_solidBrickList)) {
    	        $solidBrickList = $this->_solidBrickList[$position];
    	        $HTML = "";
    	        foreach($solidBrickList as $solidBrick) {
    	            $HTML.= $solidBrick->render();
    	        }
    	        return $HTML;
	        }
	    } else {
	        throw new Class_Brick_Exception('position required for brick rendering');
	    }
	}
	
	public function renderAll()
	{
		$solidBrickList = $this->_solidBrickList;
		
		$HTML_ARR = array();
		foreach($solidBrickList as $solidBrick) {
			if(array_key_exists($solidBrick->getSpriteName(), $HTML_ARR)) {
				$BrickHTML = $HTML_ARR[$solidBrick->getSpriteName()];
			} else {
				$BrickHTML = "";
			}
			/**
			 * @todo redesign the cache mech
			 */
//			if($solidBrick->getCacheId() !== null && !Class_Session_Admin::isLogin()) {
//				$cacheId = $solidBrick->getCacheId();
//				if(!$this->_cache->test($cacheId)) {
//					$BrickHTML.= $solidBrick->render();
//					$this->_cache->save($BrickHTML, $cacheId, array('brick'));
//				} else {
//					$BrickHTML.= $this->_cache->load($cacheId);
//				}
//			} else {
				$BrickHTML.= $solidBrick->render();
//			}
			$HTML_ARR[$solidBrick->getSpriteName()] = $BrickHTML;
		}
		return $HTML_ARR;
	}
	
	public function renderPosition()
	{
		$tb = new Zend_Db_Table('layout_stage');
		$spriteRowset = $tb->fetchAll($tb->select()->where('layoutId = 1')->order('sort ASC'));
		$HTML_ARR = array();
		
		foreach($spriteRowset as $row) {
			$HTML = "";
			$spriteName = $row->spriteName;
			if(array_key_exists($spriteName, $this->_solidBrickList)) {
				$HTML.= $this->_render($spriteName);
			}
			$HTML_ARR[$spriteName] = $HTML;
		}
		return $HTML_ARR;
	}
	
	protected function _render($spriteName)
	{
		if(is_null($spriteName)) {
			throw new Class_Brick_Exception('position required for brick rendering');
		}
		$solidBrickList = $this->_solidBrickList[$spriteName];
		$HTML = "";
		foreach($solidBrickList as $solidBrick) {
			$BrickHTML = "";
			if($solidBrick->getCacheId() !== null && !Class_Session_Admin::isLogin()) {
				$cacheId = $solidBrick->getCacheId();
				if(!$this->_cache->test($cacheId)) {
					$BrickHTML = $solidBrick->render();
					$this->_cache->save($BrickHTML, $cacheId, array('brick'));
				} else {
					$BrickHTML = $this->_cache->load($cacheId);
				}
			} else {
				$BrickHTML = $solidBrick->render();
			}
			$HTML.= $BrickHTML;
		}
		return $HTML;
	}
}