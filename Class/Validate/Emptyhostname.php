<?php
class Class_Validate_Emptyhostname extends Zend_Validate_Hostname
{
    public function isValid($value)
    {
        return true;
    }
}