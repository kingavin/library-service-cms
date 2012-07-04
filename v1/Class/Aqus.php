<?php
class Class_Aqus
{
	/**
	 * Construct a table model to read database
	 * @param String $modelName
	 * @return Zend_Db_Table_Abstract
	 */
    
    static public function _($modelName)
	{
	    if(empty($modelName)) {
			throw new Exception('could not find model list class');
		}
		$className = 'Class_Aqus_Model_'.$modelName.'_Tb';
	    $args = func_get_args();
		array_shift($args);
		
		if(count($args) > 0) {
		    $reflection = new ReflectionClass($className);
		    return $reflection->newInstanceArgs($args);
		} else {
		    return new $className;
		}
	}
}