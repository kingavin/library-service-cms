<?php
class Class_Model_Supplierorder_List extends Class_Model_List_Abstract
{
    public function __construct()
    {
        $this->_init('orders_supplier');
        $this->setModelName('Supplierorder');
    }
    public function addSupplierName()
    {
    	$this->getSelect()->joinLeft(
	        array('supplier' => 'supplier'),
	        'supplier.id = main_table.supplierId',
	        array('supplier.name as supplierName')
        );
        return $this;
    }
}