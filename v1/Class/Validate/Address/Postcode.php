<?php
require_once 'Zend/Validate/Abstract.php';

class Class_Validate_Address_Postcode extends Zend_Validate_Abstract
{
    const INVALID = 'requiredForNewAddress';
    
    protected $_messageTemplates = array(
        self::INVALID => "正确邮编-6个数字"
    );
    
    public function isValid($value, $context = null)
    {
        if($context['addressId'] == 0 && !preg_match('/^[0-9]{6}$/', $value)) {
            $this->_error(self::INVALID);
            return false;
        }
        return true;
    }
}
