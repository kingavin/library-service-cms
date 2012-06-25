<?php
class Class_Model_Supplierorder_Item_List extends Class_Model_List_Abstract
{
    public function __construct()
    {
        $this->_init('orders_supplier_item');
        $this->setModelName('Supplierorder_Item');
    }
	public function loadItemList(Class_Model_Supplierorder $order, $printSql = false, $logSql = false)
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