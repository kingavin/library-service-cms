<?php
class Class_Model_Lottery_List extends Class_Model_List_Abstract
{
    public function __construct()
    {
        $this->_init('lottery');
        $this->setModelName('Lottery');
    }
    public function joinProduct(array $field = array())
    {
    	if (empty($field)) {
	    	$this->getSelect()->joinLeft(
	            array('pe' => 'product_entity'),
	            'pe.entityId = main_table.productId'
	        );
    	} else {
    		$this->getSelect()->joinLeft(
	            array('pe' => 'product_entity'),
	            'pe.entityId = main_table.productId',
	            $field
	        );
    	}
    	return $this;
    }
	public function joinThumb()
    {
        $this->getSelect()->joinLeft(
            array('peg' => 'product_entity_graphic'),
            'main_table.productId = peg.entityId',
            array('value', 'alt')
        )->where("peg.type = 'thumb'");
        return $this;
    }
}