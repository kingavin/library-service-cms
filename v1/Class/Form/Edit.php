<?php
class Class_Form_Edit extends Zend_Form
{
	const MAIN = "main";
	const REQUIRED = "required";
	const DEPENDENT = "dependent";
	const OPTIONAL = "optional";
	const PARAM = "param";
	
	protected $_main = null;
	protected $_required = null;
	protected $_dependent = null;
	protected $_optional = null;
	protected $_param = null;
	
	public function setParam(Array $arr)
    {
    	$this->_param = $arr;
    }
	
    public function appendElementToGroup($elName, $groupName)
    {
    	switch($groupName) {
    		case 'main':
    			array_unshift($this->_main, $elName);
    			break;
    		case 'requreid':
    			array_unshift($this->_required, $elName);
    			break;
    		case 'dependent':
    			array_unshift($this->_dependent, $elName);
    			break;
    		case 'optional':
    			array_unshift($this->_optional, $elName);
    			break;
    		case 'param':
    			array_unshift($this->_param, $elName);
    			break;
    		default:
				throw new Exception('group not exist');
    	}
    }
    
    public function addElements($elements, $groupName)
    {
    	parent::addElements($elements);
    	foreach($elements as $el) {
    		$this->appendElementToGroup($el->getName(), $groupName);
    	}
    }
    
    protected function _initDisplayGroup()
    {
    	if(!is_null($this->_main)) {
    		$this->addDisplayGroup($this->_main, 'main',
	            array('legend' => '基本信息', 'class' => 'main-form')
	        );
    	}
    	if(!is_null($this->_required)) {
    		$this->addDisplayGroup($this->_required, 'required',
	            array('legend' => '填写信息', 'class' => 'required-form')
	        );
    	}
    	if(!is_null($this->_dependent)) {
    		$this->addDisplayGroup($this->_dependent, 'dependent',
	            array('legend' => '必填信息', 'class' => 'dependent-form')
	        );
    	}
    	if(!is_null($this->_optional)) {
    		$this->addDisplayGroup($this->_optional, 'optional',
	            array('legend' => '选填信息', 'class' => 'optional-form')
	        );
    	}
    	if(!is_null($this->_param)) {
    		$this->addDisplayGroup($this->_param, 'param',
	            array('legend' => '参数设定', 'class' => 'param-form')
	        );
    	}
    	$this->setDisplayGroupDecorators(array(
            'FormElements',
            array(array('DL' => 'HtmlTag'), array('tag' => 'dl')),
            'Fieldset'
        ));
        $this->setDecorators(array(
            'FormElements',
            'Form'
        ));
        $this->setMethod('post');
    }
    
    public function __toString()
    {
    	$this->_initDisplayGroup();
    	return parent::__toString();
    }
}