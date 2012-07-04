<?php
class Class_Model_Extension_Row extends Zend_Db_Table_Row_Abstract
{
	public function createSolidBrick(Zend_Controller_Request_Abstract $request, $globalParams = '{}')
    {
        $className = $this->name;
	    
        $folderPath = str_replace('_', '/', $className);
        $fileNameArr = explode('_', $className);
        $fileName = $fileNameArr[count($fileNameArr) - 1];
	    
        if(is_file(CONTAINER_PATH.'/extension/brick/'.$folderPath.'/'.$fileName.'.php')) {
            require_once CONTAINER_PATH.'/extension/brick/'.$folderPath.'/'.$fileName.'.php';
        } else {
            throw new Class_Brick_Exception('Brick file: '.CONTAINER_PATH.'/extension/brick/'.$folderPath.'/'.$fileName.'.php'.' not exist for '.$className);
        }
	    $solidBrick = new $className($this, $request, $globalParams);
	    return $solidBrick;
    }
}