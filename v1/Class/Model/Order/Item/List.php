<?php
class Class_Model_Order_Item_List extends Class_Model_List_Abstract
{
    public function __construct()
    {
        $this->_init('orders_item');
        $this->setModelName('Order_Item');
    }
    
    public function joinProductEntity(Array $fields = NULL)
    {
        if (is_null($fields)) {
            $this->getSelect()->joinLeft(
                array('pe' => 'product_entity'),
            	'pe.entityId = main_table.productId'
            );
        } else {
            $this->getSelect()->joinLeft(
                array('pe' => 'product_entity'),
            	'pe.entityId = main_table.productId',
                $fields
            );
        }
        return $this;
    }
    
    public function joinProductGraphic()
    {
        $this->getSelect()->joinLeft(
            array('peg' => 'product_entity_graphic'),
            "main_table.productId = peg.entityId and peg.type = 'thumb'",
            array('value as thumbPath', 'alt')
        );
        return $this;
    }
    
    public function loadItemList(Class_Model_Order $order, $printSql = false, $logSql = false)
    {
        $this->getSelect()->joinLeft(
            array('pe' => 'product_entity'),
        	'pe.entityId = main_table.productId'
        )->where(
        	'main_table.orderId = ?', $this->quote($order->getData('id'), 'INTEGER')
        );
        
        return $this->load($printSql, $logSql);
    }
}