<?php
class Class_Model_Lottery_Prize_List extends Class_Model_List_Abstract
{
    public function __construct($fields = null)
    {
        $this->_init('lottery_prize', $fields);
        $this->setModelName('Lottery_Prize');
    }
    
    public function getBaseTableName()
    {
        return 'lottery_prize';
    }
}
?>