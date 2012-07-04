<?php
class Class_Model_Review extends Class_Model_Eav_Entity_Abstract
{
    protected $_attributeList;
    protected $_orderItem;
    
    public function __construct()
    {
        $this->_init('review_entity');
        $this->setEntityTypeId(6);
        $this->_attributeList = array();
    }
    
    public function setOrderItem(Class_Model_Order_Item $item)
    {
        $this->_orderItem = $item;
        $this->setData('entityAttributeSetId', $item->getData('entityAttributeSetId'));
        return $this;
    }
    
    public function getOrderItem()
    {
        return $this->_orderItem;
    }
    
	public function getAttrList()
    {
        if(empty($this->_attributeList)) {
            $this->loadAttrList();
        }
        return $this->_attributeList;
    }
    
    public function loadAttrList()
    {
        $attrListModel = Class_Core::_list('Eav_Attribute')->addEntityFilter($this, array())
            ->addOrder('main_table.index', Class_Model_List_Abstract::SORT_ORDER_ASC);
    
        $this->_attributeList = $attrListModel->load()
            ->getListData('id');
    	foreach ($this->_attributeList as $attr) {
			if (!is_null($attr)) {
				$attr->loadValuesForEntity($this)->loadOptions();
		    }
		}    
        return $this;
    }
    protected function _beforeSave()
    {
    	$orderItem = $this->getOrderItem();
    	if (!is_null($orderItem)) {
    		$this->setData('productId', $orderItem->getData('productId'))
    			->setData('orderItemId', $orderItem->getData('id'));
    		
    	}
    	
    }
    protected function _afterSave()
    {
        parent::_afterSave();
        $this->getValueCollection('int')->save();
   		//update review statistics
        $reviewStatisticsTable = Class_TableFactory::getTable('review_statistics',array('name'=>'review_statistics'));
        $db = $reviewStatisticsTable->getAdapter();
        $productId = $this->getData('productId');
        
    	$attrValuesArr = $this->getValueCollection('int')->getNewCollectionData();
        foreach ($attrValuesArr as $key=>$arr) {
        	$attrValuesArr[$arr['attributeId']] = $arr;
        	unset($attrValuesArr[$key]);
        }
    	foreach($this->_attributeList as $attr) {
            $inputType = $attr->getData('inputType');
            $set = array(
            	'attributeId' => $attr->getData('id'),
            	'productId' => $productId
            );
            $where = $db->quoteInto('attributeId = ?', $attr->getData('id'))
			       . $db->quoteInto('AND productId = ?',$productId);	
			$row = $reviewStatisticsTable->fetchRow($where, 'id');
            if ($inputType == 'select') {
            	//TODO statistics of another review which hava question and answer
            } else if ($inputType == 'text'){
            	if (is_null($row)) {
            		$set['value'] = $attrValuesArr[$attr->getData('id')]['value'];
            		$reviewStatisticsTable->insert($set);
            	}else  {
            		$row->value = $row->value + $attrValuesArr[$attr->getData('id')]['value'];
            		$row->save();
            	}
            }
        }
        //give point to customer
        $point = 0;
    	$orderItem = $this->getOrderItem();
    	if (!is_null($orderItem)) {
    		$point = $orderItem->getData('itemPoint') ? $orderItem->getData('itemPoint') : $orderItem->getData('itemPrice');
    		$product = Class_Core::_('Product')
    			->setData('entityId', $orderItem->getData('productId'))
    			->load();
    		if ($product->getData('pointSale') == 0) {
    			$point = 2 * $point;
    		}
    	}
         
    	$customerId = Class_Customer::getData('entityId');
    	if (!is_null($customerId)) {
    		$customer = Class_Core::_('Customer')
				->setData('entityId',$customerId)
				->load();
    		$customer->setData('point',$customer->getData('point') + $point)->save(false);
    	}
    }
    
}