<?php
class Class_Mongo_Brick_Doc extends App_Mongo_Db_Document
{
	protected $_field = array(
		'extName',
		'layoutId',
		'stageId',
		'spriteName',
		'brickName',
		'displayBrickName',
		'cssSuffix',
		'type',
		'active',
		'weight',
		'params',
		'tplName'
	);
	
	public function createSolidBrick(Zend_Controller_Request_Abstract $request, $globalParams = '{}')
    {
        $className = $this->extName;
	    
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