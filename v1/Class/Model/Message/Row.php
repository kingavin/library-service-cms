<?php
class Class_Model_Message_Row extends Class_Model_Eav_Entity_Row_Abstract
{
	protected $_ownerLabel = '发布人';
	protected $_ownerValidator = null;
	
	public function setOwnerLabel($label)
	{
		if(!empty($label)) {
			$this->_ownerLabel = $label;
		}
		return $this;
	}
	
	public function setOwnerValidator($validator)
	{
		$this->_ownerValidator = $validator;
		return $this;
	}
	
	public function getEntityType()
	{
		return 'message';
	}
	
	public function getOriginalForm($backendForm)
	{
		$form = new Zend_Form();
		$form->addElement('text', 'owner', array(
			'label' => $this->_ownerLabel,
			'required' => true
		));
		if(!is_null($this->id)) {
			$form->populate($this->toArray());
		}
		switch($this->_ownerValidator) {
			case 'email':
				break;
		}
		if(!$backendForm) {
			$form->addElement('submit', 'submit', array(
				'label' => '确认',
				'order' => '9999'
			));
		}
		return $form;
	}
	
	public function getValueTable()
	{
		$tb = new Zend_Db_Table('message_entity_value');
		return $tb;
	}
}