<?php
require_once 'Zend/Validate/Abstract.php';

class Class_Validate_EmailSimple extends Zend_Validate_Abstract
{
    const INVALID = 'emailAddressInvalid';
    
    protected $_messageTemplates = array(
        self::INVALID => "您填写的用户名，可能不是有效的Email地址，请检查后再次提交。"
    );
    
    public function isValid($value)
    {
        $valueString = (string) $value;
        $matches     = array();

        $this->_setValue($valueString);

        if ((strpos($valueString, '..') !== false) or
            (!preg_match('/^([.0-9a-z_-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i', $valueString, $matches))) {
            $this->_error(self::INVALID);
            return false;
        }
        return true;
    }
}
