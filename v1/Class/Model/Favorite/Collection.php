<?php
class Class_Model_Favorite_Collection extends Class_Model_Collection_Abstract
{
    public function __construct()
    {
        $this->_init();
        $this->setResourceName('user_favorite');
        $this->getSelect()
            ->from(array('main_table' => 'user_favorite'))
            ->where('main_table.active = 1');
    }
    
    public function addUserFilter($uid)
    {
        $this->getSelect()
            ->where('main_table.user_id = ?', $this->quote($uid, 'INTEGER'));
        return $this;
    }
    
    public function addProductTable($fields = NULL)
    {
        $this->getSelect()->joinLeft(
            array('pe' => 'product_entity'),
            'pe.entityId = main_table.product_id',
            $fields
        )->where('pe.active = 1');
        return $this;
    }
    
    public function addProductGraphicTable($fields = NULL)
    {
        $this->getSelect()->joinLeft(
            array('peg' => 'product_entity_graphic'),
            "peg.entityId = main_table.product_id and peg.type = 'thumb'",
            $fields
        );
        return $this;
    }
}