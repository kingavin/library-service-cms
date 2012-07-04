<?php
class Class_Core
{
    static protected $_singletonList = array();
    
//	static public function getModel($modelName)
//	{
//		if(empty($modelName)) {
//			throw new Exception('could not find model');
//		}
//		$classname = 'Class_Model_'.$modelName;
//		return new $classname;
//	}
//	
//	static public function getCollectionModel($modelName)
//	{
//		if(empty($modelName)) {
//			throw new Exception('could not find model collection');
//		}
//		$classname = 'Class_Model_'.$modelName.'_Collection';
//		return new $classname;
//	}
//	
//	static public function getListModel($modelName)
//	{
//		if(empty($modelName)) {
//			throw new Exception('could not find model list class');
//		}
//		$classname = 'Class_Model_'.$modelName.'_List';
//		return new $classname;
//	}
//	
	/**
	 * Construct a model to read database
	 * @param String $modelName
	 */
    
    static public function _($modelName)
	{
		if(empty($modelName)) {
			throw new Exception('could not find model, no model name given!');
		}
		$className = 'Class_Model_'.$modelName;
		$args = func_get_args();
		array_shift($args);
		
		if(count($args) > 0) {
		    $reflection = new ReflectionClass($className);
		    return $reflection->newInstanceArgs($args); 
		} else {
		    return new $className;
		}
	}
	
	/**
	 * Construct a table model to read database
	 * @param String $modelName
	 * @throws Exception
	 * @return Zend_Db_Table_Abstract
	 */
	static public function _tb($modelName)
	{
	    if(empty($modelName)) {
			throw new Exception('could not find model list class');
		}
		$className = 'Class_Model_'.$modelName.'_Tb';
	    $args = func_get_args();
		array_shift($args);
		
		if(count($args) > 0) {
		    $reflection = new ReflectionClass($className);
		    return $reflection->newInstanceArgs($args);
		} else {
		    return new $className;
		}
	}
	
	/*
	 * @param  string $modelName
     * @return Class_Model_List_Abstract
	 */
	static public function _list($modelName)
	{
	    if(empty($modelName)) {
			throw new Exception('could not find model list class');
		}
		$className = 'Class_Model_'.$modelName.'_List';
	    $args = func_get_args();
		array_shift($args);
		
		if(count($args) > 0) {
		    $reflection = new ReflectionClass($className);
		    return $reflection->newInstanceArgs($args);
		} else {
		    return new $className;
		}
	}
	
	static public function _query($select)
	{
        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;
	}
	
	static public function _singleton($modelName)
	{
	    if(empty($modelName)) {
			throw new Exception('could not find model, no model name given!');
		}
		if(!array_key_exists($modelName, self::$_singletonList)) {
		    $className = 'Class_Model_'.$modelName;
		    
		    $args = func_get_args();
		    array_shift($args);
		    
    		if(count($args) > 0) {
    		    $reflection = new ReflectionClass($className);
    		    $obj = $reflection->newInstanceArgs($args); 
    		} else {
    		    $obj = new $className;
    		}
		    self::$_singletonList[$modelName] = $obj;
		}
		return self::$_singletonList[$modelName];
	}
	
//    static function layGraphic($fileName, $type)
//    {
//        if(strpos($fileName, '.jpg') == 0) {
//            if($type == 'thumb') {
//                $fileName = $fileName.'_S.jpg';
//            } else if($type == 'view') {
//                $fileName = $fileName.'.jpg';
//            }
//        }
//        if(is_file(HTML_PATH.'/graphics'.$fileName)) {
//            return '/graphics'.$fileName;
//        } else {
//            if($type == 'thumb') {
//                return '/images/graphic_missing_thumb.jpg';
//            } else if($type == 'view') {
//                return '/images/graphic_missing.jpg';
//            } else if($type == 'preview') {
//                return '/images/graphic_missing_preview.jpg';
//            }
//        }
//    }
    
    static function fireLog($message, $type = null)
    {
        $loger = self::_getLogger('firebug');
        switch($type) {
            case 'info':
                $loger->info($message);
                break;
            default:
                $loger->info($message);
                break;
        }
    }
    
    static function log($message, $type = null)
    {
        switch($type) {
            case 'info':
                $loger = self::_getLogger('file');
                $loger->info($message);
                break;
            case 'payment':
//                $loger = self::_getLogger('file', 'payment.log');
//                $loger->crit($message);
                break;
            case 'error':
                $loger = self::_getLogger('file', 'error.log');
                $loger->crit($message);
                break;
            default:
                $loger = self::_getLogger('file');
                $loger->info($message);
                break;
        }
    }
    
    protected static function _getLogger($loggerType, $filename = null)
    {
        $filename = is_null($filename) ? 'log' : $filename;
        switch($loggerType) {
            case 'firebug':
                return new Zend_Log(new Zend_Log_Writer_Firebug());
                break;
            case 'file':
                return new Zend_Log(new Zend_Log_Writer_Stream(APP_PATH.'/'.$filename));
                break;
            default:
                throw new Exception('logger type is not defined!');
        }
    }
    
    public static function siteInfo($field)
    {
        $siteObj = Class_TableFactory::getRow('site', array('name' => 'site'), 1);
        $row = $siteObj->toArray();
        if(array_key_exists($field, $row)) {
            return $row[$field];
        }
        return null;
    }
    
    public static function _cache(Array $reset = array())
    {
        $frontendName = 'Core';
        $backendName = 'File';
        $frontendOptions = array('lifetime' => 10800, 'automatic_serialization' =>  true);
        $backendOptions = array('cache_dir' => CACHE_PATH, 'hashed_directory_level' => 1);
        
        foreach($reset as $key => $value) {
            switch($key) {
                case 'frontendName':
                    $frontendName = $value;
                    break;
                case 'backendName':
                    $backendName = $value;
                    break;
                case 'caching':
                    $frontendOptions['caching'] = $value;
                    break;
                case 'lifetime':
                    $frontendOptions['lifetime'] = $value;
                    break;
            }
        }
        
        $cache = Zend_Cache::factory(
             $frontendName,
             $backendName,
             $frontendOptions,
             $backendOptions
        );
        return $cache;
    }
}