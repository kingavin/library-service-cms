<?php
class Class_Mongo_Attributeset_Doc extends App_Mongo_Db_Document
{
	protected $_zfElements = null;
	
	public function loadZfElements()
	{
		if(is_null($this->attributeList)) {
			$this->_zfElements = array();
			return;
		}
		$attributeArr = $this->attributeList;
		foreach($attributeArr as $name => $attr) {
			$type = $attr['type'];
			if(array_key_exists('label', $attr)) {
				$label = $attr['label'];
			} else {
				$label = "没有标题";
			}
			
			$element = null;
			$frontendModelName = null;
			if($frontendModelName != null) {
				$frontModel = Class_Core::_($frontendModelName, $this, $entity);
				$element = $frontModel->toElement();
			} else {
				$selectedValue = '';
				 
				switch($type) {
					case 'textarea':
						$element = new Zend_Form_Element_Textarea('attribute_'.$name, array(
	                        'label' => $label,
	                        'value' => $selectedValue
						));
						break;
					case 'text':
						$element = new Zend_Form_Element_Text('attribute_'.$name, array(
	                        'label' => $label,
	                        'value' => $selectedValue
						));
						break;
					case 'select':
						$element = new Zend_Form_Element_Select('attribute_'.$name, array(
	                        'label' => $label,
	                        'value' => $selectedValue
						));
						break;
					default:
						$element = null;
						break;
				}
				$this->_zfElements[] = $element;

			}
		}
	}
	
	public function getZfElements()
	{
		if($this->_zfElements == null) {
			$this->loadZfElements();
		}
		
        return $this->_zfElements;
	}
}