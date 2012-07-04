<?php
class Class_Model_Order extends Class_Model_Abstract
{
    protected $_itemCollection;
    protected $_hasItem;
    protected $_tempTableName;
    
    protected $_itemList;
    
    protected $_cart = null;
    
    public function __construct()
    {
        $this->_init('orders');
        $this->_tempTableName = 'temp_item';
        $this->_hasItem = false;
    }
    
    public function loadItemList()
    {
        $this->_itemList = Class_Core::_list('Order_Item')->loadItemList($this)
            ->getListData();
        return $this;
    }
    
    public function getItemList()
    {
        return $this->_itemList;
    }
    public function getTotalDiscountPrice()
    {
    	$itemList = $this->getItemList();
    	if (!count($itemList) || is_null($itemList)) {
    		$this->loadItemList();
    		$itemList = $this->getItemList();
    	}
    	$itemTotalPrice = 0;
    	foreach($itemList as $item){
    		$itemTotalPrice += $item->getData('itemPrice');
    	}
    	$realPrice = $this->getData('total') - $this->getData('shippingPrice');
    	return number_format($itemTotalPrice - $realPrice, 2, '.', '');
    }
    public function getTotalPoint()
    {
    	$itemList = $this->getItemList();
    	if (!count($itemList) || is_null($itemList)) {
    		$this->loadItemList();
    		$itemList = $this->getItemList();
    	}
    	$itemTotalPoint = 0;
    	foreach($itemList as $item){
    		$itemTotalPoint += $item->getData('itemPoint');
    	}
    	
    	return $itemTotalPoint;
    }
    public function setItemCollection($itemCollection)
    {
        $this->_itemCollection = $itemCollection;
        $this->_hasItem = true;
        return $this;
    }
    
    public function setUserId($uid)
    {
        $this->setData('customerId', $uid);
        return $this;
    }
    
    public function setCustomerEntityId($id)
    {
        $this->setData('customerId', $id);
        return $this;
    }
    
    public function setAddressId($aid)
    {
        $this->setData('addressId', $aid);
        return $this;
    }
    
    public function setTotalPrice($total)
    {
        $this->setData('total', $total);
        return $this;
    }
    
    public function setPaid($flag)
    {
        $this->setData('paid', $flag);
        return $this;
    }
    
    public function updateOrder()
    {
        $resource = $this->_getResource();
        foreach($this->_data as $k => $v) {
            if($resource->$k != $v) {
                $resource->$k = $v;
            }
        }
        
        $db = Zend_Registry::get('dbAdaptor');
        $db->beginTransaction();
        try {
            $this->_insertId = $resource->save();
            $this->_data = $resource->toArray();
            $db->commit();
        } catch(Exception $e) {
            $db->rollback();
            throw $e;
        }
        return $this;
    }
    
    protected function _allowSave()
    {
        return false;
    }
    
    protected function _beforeSave()
    {
        /*
         * hack total hack, prevent beforeSave for the old code.............. 
         */
        if($this->_newFunctionActivatedHack === true) {
            if($this->_cart instanceof Class_Cart) {
                if(!$this->_hasItem) {
                    throw new Exception('0 items in the Order Model');
                }
                if($this->getData('sub_total') === null  && $this->_cart !== null) {
                    $this->setData('sub_total', $this->_cart->getSubTotal());
                }
                if($this->getData('total') === null) {
                    $this->setData('total', $this->getData('sub_total') + $this->getData('shippingPrice'));
                }
            } else {
                $this->setData('sub_total', $this->_cart->getSubtotal());
                $this->setData('total', $this->getData('sub_total') + $this->getData('shippingPrice'));
            }
        } else {
            if(!$this->_hasItem) {
                throw new Exception('0 items in the Order Model');
            }
            $itemPointsRequired = Class_Cart::getItemPoint();
            $customerPoints = Class_Customer::getData('point');
            if($customerPoints + $itemPointsRequired < 0) {
                throw new Exception('Not enough point');
            }
    
    		$this->setData('addressId', Class_Order::getAddressData('addressId'));
            $this->setData('addressConsignee', Class_Order::getAddressData('consignee'));
            $this->setData('addressFullAddress', Class_Order::getAddressData('fullAddress'));
            $this->setData('addressMobile', Class_Order::getAddressData('mobile'));
            $this->setData('addressPhone', Class_Order::getAddressData('phone'));
        }
		
    }
    
    protected function _afterSave()
    {
        //total hack hack hack!  i hate it very much...
        if($this->_cart instanceof Class_Cart_Abstract) {
            $orderId = $this->getData('id');
            foreach($this->_cart->getItems() as $item) {
                $item->moveStock($orderId);
            }
           
            //add by tery 2010-1-19 for coupon
            $client = new Zend_Session_Namespace('client');
            if(isset($client->coupon)){
            	unset($client->counpon);
            }
            //end add by tery 2010-1-19 for coupon
            
            if($this->_newFunctionActivatedHack === true) {
                $db = Zend_Registry::get('dbAdaptor');
                $newOrderId = date('ymd').$orderId;
                $set = array('orderId'=>$newOrderId);
        		$where = $db->quoteInto('id = ?', $orderId);
        		$db->update('orders',$set,$where);
            }
        } else {
        
            $orderId = $this->getData('id');
            $productModelList = Class_Core::_list('Product')
                ->addFieldToFilter('entityId', array('in'=>array_keys($this->_itemCollection)))
                ->load()
                ->getListData();
            $productArr = array();
            if(!is_null($this->_cart)) {
                $productArr = $this->_cart->getProductArr();
            }
            foreach($productModelList as $pModel) {
    //            $pModel = Class_Model_Product_Factory::_obj($pModel);
                if(array_key_exists($pModel->getEntityId(), $productArr)) {
                    if($productArr[$pModel->getEntityId()]['price'] !== null) {
                        $pModel->setData('price', $productArr[$pModel->getEntityId()]['price']);
                    }
                }
                $pModel->moveStock($orderId, $this->_itemCollection[$pModel->getEntityId()], $pModel->getData('price'));
            }
            //add by tery 2010-1-19 for coupon
            $client = new Zend_Session_Namespace('client');
            if(isset($client->coupon)){
            	unset($client->counpon);
            }
            //end add by tery 2010-1-19 for coupon
            
            if($this->_newFunctionActivatedHack === true) {
                $db = Zend_Registry::get('dbAdaptor');
                $newOrderId = date('ymd').$orderId;
                $set = array('orderId'=>$newOrderId);
        		$where = $db->quoteInto('id = ?', $orderId);
        		$db->update('orders',$set,$where);
            }
        }
//		if(Class_Customer::getData('entityId') != 0){
//			$itemPointsRequired = Class_Cart::getItemPoint();
//			$customerPoints = Class_Customer::getData('point');
//			$newPoint = $customerPoints + $itemPointsRequired;
//			$customerRow = Class_TableFactory::getRow('customer_entity', array('name' => 'customer_entity'), Class_Customer::getData('entityId'));
//			$customerRow->point = $newPoint;
//			$customerRow->save();
//		}
		//add by tery 2010-1-11
		
//		if(Class_Customer::getData('entityId') == 0){
//			$cellphone = Class_Order::getAddressData('mobile');
//			$customer = Class_Core::_('Customer')->setData('cellphone', $cellphone)
//				->load();
//				
//			if(!is_null($customer->getData('entityId'))){
//				$customerId = $customer->getData('entityId');
//			}
//		}else{
//			$customerId = Class_Customer::getData('entityId');
//		}
//		if(!is_null($customerId) && $customerId != 0){
//			$address = Class_Core::_('Address')->create()
//				->setData('customerId',$customerId)
//				->setData('consignee',Class_Order::getAddressData('consignee'))
//				->setData('mobile',Class_Order::getAddressData('mobile'))
//				->setData('phone',Class_Order::getAddressData('phone'))
//				->setData('postcode',Class_Order::getAddressData('postcode'))
//				->setData('provinceUnitId',Class_Order::getAddressData('provinceUnitId'))
//				->setData('cityUnitId',Class_Order::getAddressData('cityUnitId'))
//				->setData('addressDetail',Class_Order::getAddressData('addressDetail'))
//				->save();
//		}
		//end add
		
    }
    
    /**
     * if user or admin cancel a order, restore the product stock of this order 
     * if user canel order by themslves, you need check the owner of this order before you call this function
     * 
     */
    public function restoreStock()
    {
        $productList = Class_Core::getListModel('Product')->joinOrderItemFilter($this->getData('id'))
            ->load()
            ->getListData();
        $db = Zend_Registry::get('dbAdaptor');
        $db->beginTransaction();
        try {
            foreach($productList as $p) {
                $p->addStock($p->getData('qty'));
            }
            $db->query("
                update orders 
                set status = 'cancelled'
    			where id=".$this->getData('id')." and status='new'
    		");
            $db->commit();
        } catch(Exception $e) {
            $db->rollback();
            throw $e;
        }
        return $this;
    }
    
    /**
     * deprecated !!
     * Get all orders for the user
     * 
     * @param string $status    three options, 
     * 							'processing' return processing orders,
     * 						   	'cancelled' return cancelled orders,
     * 							'complete' return complete orders
     * @return array $return       user's orders
     */
    public function getOrders($status = '')
    {
        $s = array();
        switch($status)
        {
            case 'cancelled':                
                $s[] = 'cancelled';
                break;
            case 'complete':
                $s[] = 'complete'; 
                break;
            case 'processing':
                $s = array('new','processed','sent','received');    
            default:
                break;            
        }
        
        $str = '';
        if( count($s) > 0 ) 
        {            
            $str = implode("' or orders.status='",$s );
            $str = " AND ( orders.status='".$str."' ) ";          
        }
        $return = array();
        $db = Zend_Registry::get('dbAdaptor');
       
        $return = $db -> fetchAll("
                            select  orders.id as order_id, 
                                        address.addressId as address_id,
                                        orders.created as order_created,
                                        address.created as address_created,
                                        orders.*, 
                                        address.*  
                                    from orders
                                    left join address on address.addressId=orders.addressId
                                    where orders.customerId=" . intval(Class_Customer::getData('entityId')) .$str."
                                    order by orders.created DESC
                                    "
        );    
        return $return;
    }
    
    /**
     * @return Class_Model_Order
     * @param $cart
     */
    
    public function setCart($cart)
    {
        $this->_cart = $cart;
        return $this;
    }
    
    /**
     * 
     * @param Class_Model_Address $address
     * @return Class_Model_Order
     */
    public function setAddress(Class_Model_Address $address)
    {
        $this->_newFunctionActivatedHack = true;
        $address->loadAreaName();
        $this->setData(array(
        	'addressConsignee' => $address->getData('consignee'),
            'addressCityId' => $address->getData('cityUnitId'),
        	'addressProvinceId' => $address->getData('provinceUnitId'),
            'addressFullAddress' => $address->getData('provinceName').$address->getData('cityName').$address->getData('addressDetail'),
            'addressMobile' => $address->getData('mobile'),
            'addressPhone' => $address->getData('phone'),
        	'addressId' => $address->getData('addressId')
        ));
        return $this;
    }
    
    /**
     * @return Class_Model_Order
     * @param Class_Cart $cart
     */
    public function setItemFromCart($cart)
    {
        if($cart instanceof Class_Cart) {
            $productArr = $cart->getProductArr();
            $this->_itemCollection = array();
            foreach($productArr as $entityId => $values) {
                $this->_itemCollection[$entityId] = $values['qty'];
            }
            $this->_hasItem = true;
            return $this;
        } else if($cart instanceof Class_Cart_Abstract){
//            $productArr = $cart->getItems();
//            $this->_itemCollection = array();
//            foreach($productArr as $item) {
//                $entityId = $item->getEntityId();
//                $this->_itemCollection[$entityId] = $item->getQty('qty');
//            }
            $this->_hasItem = true;
            return $this;
        }
    }
    
    /**
     * @return Class_Model_Order
     */
    public function setItem()
    {
        if($this->_cart === null) {
            throw new Exception('cart class required for order');
        }
        $this->setItemFromCart($this->_cart);
        return $this;
    }
}