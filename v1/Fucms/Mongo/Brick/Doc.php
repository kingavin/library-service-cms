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
		'params',
		'tplName',
		'sort'
	);
	
	public function createSolidBrick($routeMatch)
    {
        $className = $this->extName;
	    
        $folderPath = str_replace('_', '/', $className);
        $fileNameArr = explode('_', $className);
        $fileName = $fileNameArr[count($fileNameArr) - 1];
	    
        if(is_file(BASE_PATH.'/extension/'.$folderPath.'/'.$fileName.'.php')) {
            require_once BASE_PATH.'/extension/'.$folderPath.'/'.$fileName.'.php';
        } else {
            throw new Class_Brick_Exception('Brick file: '.BASE_PATH.'/extension/'.$folderPath.'/'.$fileName.'.php'.' not exist for '.$className);
        }
	    $solidBrick = new $className($this, $routeMatch);
	    return $solidBrick;
    }
}