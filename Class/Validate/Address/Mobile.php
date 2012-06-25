<?php
require_once 'Zend/Validate/Abstract.php';

class Class_Validate_Address_Mobile extends Zend_Validate_Abstract
{
    const INVALID = 'requiredForNewAddress';
    
    protected $_messageTemplates = array(
        self::INVALID => "正确手机号码-11位数字"
    );
    
    public function isValid($value, $context = null)
    {
        if($context['addressId'] == 0 && !preg_match('/^[0-9]{11}$/', $value)) {
            $this->_error(self::INVALID);
            return false;
        }
        return true;
    }
}
