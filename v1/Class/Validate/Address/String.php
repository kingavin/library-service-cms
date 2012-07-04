<?php
require_once 'Zend/Validate/Abstract.php';

class Class_Validate_Address_String extends Zend_Validate_Abstract
{
    const INVALID = 'requiredForNewAddress';
    
    protected $_messageTemplates = array(
        self::INVALID => "请正确填写此项目"
    );
    
    public function isValid($value, $context = null)
    {
        if($context['addressId'] == 0 && (empty($value) || $value === 0)) {
            $this->_error(self::INVALID);
            return false;
        }
        return true;
    }
}
