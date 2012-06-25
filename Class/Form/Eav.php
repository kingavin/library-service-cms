<?php
class Class_Form_Eav extends Zend_Form
{
    protected $_partialDisabled = false;
    protected $_disableArr = array();
    
    public function appendAttributeElement($attributeList)
    {
        foreach($attributeList as $attr) {
            $this->addElement($attr->toElement());
        }
        return $this;
    }
    
    public function setDisableArray(Array $disableArr)
    {
        $this->_setDisableArray($disableArr);
        $this->setPartialDisabled(true);
        $this->_disalbeElements();
        
        return $this;
    }

    public function setPartialDisabled($flag = true)
    {
        $this->_partialDisabled = (bool) $flag;
        $this->_disalbeElements();
    }

    public function getValues($suppressArrayNotation = false)
    {
        $data = parent::getValues($suppressArrayNotation);
        if ($this->_partialDisabled) {
            foreach ($this->_disableArr as $e) {
                if (array_key_exists($e, $data)) {
                    unset($data[$e]);
                }
            }
        }
        return $data;
    }

    protected function _setDisableArray(Array $disableArr)
    {
        $this->_disableArr = $disableArr;
        return $this;
    }

    protected function _disalbeElements()
    {
        if ($this->_partialDisabled) {
            $this->_partialDisabled = true;
            foreach ($this->_disableArr as $e) {
                $this->$e->setRequired(false);
                $this->$e->setAttrib('disabled', 'disabled');
            }
        }
        return $this;
    }
}