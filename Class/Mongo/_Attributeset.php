<?php
class Class_Mongo_Attributeset
{
	protected $_row;
	
	protected $_zfElements;
	
	public function __construct($row)
	{
		$this->_row = $row;
	}
	
	public function getZfElements()
	{
		if($this->_zfElements == null) {
			foreach($this->_row['attributeList'] as $key => $attr) {
				$name = $key;
        		$type = $attr['type'];
        		
        		$element = null;
		        $frontendModelName = null;
		        if($frontendModelName != null) {
		            $frontModel = Class_Core::_($frontendModelName, $this, $entity);
		            $element = $frontModel->toElement();
		        } else {
		        	$selectedValue = 'abc';
		        	
			        switch($type) {
		                case 'textarea':
		                    $element = new Zend_Form_Element_Textarea('attribute_'.$key, array(
		                        'label' => $name,
		                        'value' => $selectedValue
		                    ));
		                    break;
		                case 'text':
		                    $element = new Zend_Form_Element_Text('attribute_'.$key, array(
		                        'label' => $name,
		                        'value' => $selectedValue
		                    ));
		                    break;
		                case 'select':
		                    $element = new Zend_Form_Element_Select('attribute_'.$key, array(
		                        'label' => $name,
		                        'value' => $selectedValue
		                    ));
		                    break;
		                default:
		                    $element = null;
		                    break;
		            }
		        	
//		            if($this->getData('isRequired') == 1) {
//			            $element->setRequired(true);
//			        }
//			        if($this->getData('validator') != null) {
//			            $element->addValidator($this->getData('validator'));
//			        }
//			        if($this->getData('description') != null) {
//			            $element->setDescription($this->getData('description'));
//			        }
			        $this->_zfElements[$key] = $element;
		            
		        }
			}
		}
		
        return $this->_zfElements;
	}
	
	public function appendOptions(Zend_Form $form)
	{
		$attrList = $this->_row['attributeList'];
		
		foreach($attrList as $key => $attr) {
			switch($attr['type']) {
				case 'text':
					$element = new Zend_Form_Element_Text("attribute_$key");
					break;
				case 'select':
					$element = new Zend_Form_Element_Select("attribute_$key");
					break;
			}
			$form->addElement($element);
		}
		
		return $form;
	}
}