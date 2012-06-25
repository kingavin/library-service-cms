<?php
class Class_Model_Supplierorder extends Class_Model_Abstract
{
	protected $_itemList;
	
	public function __construct()
	{
		$this->_init('orders_supplier');
	}
	public function loadItemList($loadManu = false)
    {
        $this->_itemList = Class_Core::_list('Supplierorder_Item')
        	->loadItemList($this)
            ->getListData();
        if($loadManu){
        	self::loadManufacturerForItemList();
        }
        return $this;
    }
    
    public function getItemList()
    {
        return $this->_itemList;
    }
    private function loadManufacturerForItemList(){
    	foreach($this->_itemList as &$item){
	    	$product = Class_Core::_('Product')
				->setData('entityId',$item->getData('productId'))
				->load();
			$attr = Class_Core::_('Eav_Attribute')
				->setData('code','manufacturer')
				->load();
	    	if (!is_null($attr->getData('id'))) {
				$attr->loadValuesForEntity($product)->loadOptions();
			}
			$key = $attr->getData('code');
	        $value = $attr->getSelectedOption();
	        $row = Class_TableFactory::getRow('manufacturer_entity',array('name'=>'manufacturer_entity'),$value);
	        $item->setData($key, $row->name);
		}
		unset($item);
    }
    public function updateProductStock()
    {
    	if(is_null($this->_itemList)){
    		self::loadItemList();
    	}
    	foreach($this->_itemList as $item){
	    	$product = Class_Core::_('Product')
				->setData('entityId',$item->getData('productId'))
				->load();
			$product->updateStock($item->getData('qty'));
		}
		return true;
    }
}