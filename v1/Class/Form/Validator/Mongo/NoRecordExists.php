<?php
class Class_Form_Validator_Mongo_NoRecordExists extends Zend_Validate_Abstract
{
	const ERROR_NO_RECORD_FOUND = 'noRecordFound';
    const ERROR_RECORD_FOUND    = 'recordFound';
	
    protected $_messageTemplates = array(
        self::ERROR_NO_RECORD_FOUND => "No record matching '%value%' was found",
        self::ERROR_RECORD_FOUND    => "A record matching '%value%' was found",
    );
	
    protected $_collectionName = null;
    protected $_field = null;
    
    public function __construct($collectionName, $field)
    {
    	$this->_collectionName = $collectionName;
    	$this->_field = $field;
    }
    
    public function isValid($value)
    {
    	$co = App_Factory::_m($this->_collectionName);
    	
        $valid = true;
        $this->_setValue($value);

//        $result = $this->_query($value);
        
        $result = $co->addFilter($this->_field, $value)->fetchOne();
        
        
        if ($result) {
            $valid = false;
            $this->_error(self::ERROR_RECORD_FOUND);
        }

        return $valid;
    }
}
