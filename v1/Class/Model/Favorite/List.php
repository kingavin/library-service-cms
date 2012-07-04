<?php
class Class_Model_Favorite_List extends Class_Model_List_Abstract
{
    public function __construct()
    {
        $this->_init('customer_entity_favorite');
        $this->setModelName('favorite');
        $this->getSelect()
        	->where('main_table.active = 1');	
    }
	public function getBaseTableName()
    {
        return 'customer_entity_favorite';
    }
    
    public function addUserFilter($uid)
    {
        $this->getSelect()
            ->where('main_table.entityId = ?', $this->quote($uid, 'INTEGER'));
        return $this;
    }
    
    public function addProductTable($fields = NULL)
    {
        $this->getSelect()->joinLeft(
            array('pe' => 'product_entity'),
            'pe.entityId = main_table.productId',
            $fields
        )->where('pe.active = 1');
        return $this;
    }
    
    public function addProductGraphicTable($fields = NULL)
    {
        $this->getSelect()->joinLeft(
            array('peg' => 'product_entity_graphic'),
            "peg.entityId = main_table.productId and peg.type = 'thumb'",
            $fields
        );
        return $this;
    }
}