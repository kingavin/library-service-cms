<?php
class Class_Mongo_Product_Type_Row
{
	protected $_row;
	
	public function __construct($row)
	{
		$this->_row = $row;
	}
	
	public function appendAttributeList(Zend_Form $form)
	{
		$attrList = $this->_row['attributeList'];
		
		foreach($attrList as $key => $attr) {
			switch($attr['type']) {
				case 'text':
					$element = new Zend_Form_Element_Text($key);
					break;
				case 'select':
					$element = new Zend_Form_Element_Select($key);
					break;
			}
			$form->addElement($element);
		}
		
		return $form;
	}
}