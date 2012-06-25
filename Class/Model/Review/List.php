<?php
class Class_Model_Review_List extends Class_Model_Eav_Entity_List_Abstract
{
	public function __construct()
    {
        $args = func_get_args();
        if(count($args) > 0) {
            $this->_init('review_entity', $args[0]);
        } else {
            $this->_init('review_entity');
        }
        $this->setModelName('Review');
    }

	public function getSelectCountSql()
    {
        $this->_renderFilters();
        
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        
        $countSelect->from('', 'COUNT( distinct main_table.entityId)');
        
        return $countSelect;
    }
	public function getBaseTableName()
    {
        return 'review_entity';
    }
}