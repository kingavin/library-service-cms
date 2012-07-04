<?php
class Class_Model_Poll_Vote_List extends Class_Model_List_Abstract
{

    public function __construct()
    {
        $this->_init('poll_vote');
        $this->setModelName('Poll_Vote');
    }
}
?>