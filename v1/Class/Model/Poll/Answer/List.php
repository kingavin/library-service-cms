<?php
class Class_Model_Poll_Answer_List extends Class_Model_List_Abstract
{

    public function __construct()
    {
        $this->_init('poll_answer');
        $this->setModelName('Poll_Answer');
    }
}
?>