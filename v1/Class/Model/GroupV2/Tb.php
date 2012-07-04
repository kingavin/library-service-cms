<?php
class Class_Model_GroupV2_Tb extends Zend_Db_Table_Abstract
{
    protected $_name = 'group';
    protected $_rowClass = 'Class_Model_GroupV2_Row';
    
    protected $_dependentTables = array('Class_Model_Group_Tb');
    
	public function fetchSelectOption($preArr, $selector = null)
    {
        $items = array();
        
        if($selector == null) {
        	$selector = $this->select()->order('parentId');
        } else {
        	$selector = $selector->order('parentId');
        }
        $data = $this->fetchAll($selector);
        
        $linkController = new Class_Link_Controller($data);
        $options = $linkController->toMultiOptions();
        if(count($preArr) > 0) {
	        foreach($options as $key => $value) {
	        	$preArr[$key] = $value;
	        }
	        $options = $preArr;
        }
        return $options;
    }
}