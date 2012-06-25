<?php
class Class_Model_Address extends Class_Model_Abstract
{
    public function __construct()
    {
        $this->_init('address');
    }
    
    public function getProvinceCollectionData()
    {
        $provinceCollection = new Class_Model_Address_Area_Collection();
        $provinceData = $provinceCollection->addFilter('parentId', 0)
            ->load()
            ->getNVPData('id', 'name');
        return $provinceData;
    }
    
    public function getCityCollectionData($provinceId)
    {
        $cityCollection = new Class_Model_Address_Area_Collection();
        $cityCollectionData = $cityCollection->addFilter('parentId', $provinceId)
            ->load()
            ->getNVPData('id', 'name');
        return $cityCollectionData;
    }
    
    /**
     * Get all addresses of this user
     */
    public function getAddresses($uid)
    {
        $addressTable = Class_TableFactory::getTable('address', array('name'=>'address'));
        $addresses = $addressTable->fetchAll($addressTable->select()->where(' active=1 and customerId=?', $uid));
        return $addresses->toArray();
    }    
    
    /**
     * Get one address 
     */
    public function getAddress()
    {
        
        
    }
    
    public function loadAreaName()
    {
        $areaNames = Class_Core::_list('Address_Area')
            ->addFieldToFilter('id', array('in' => array($this->getData('provinceUnitId'), $this->getData('cityUnitId'))))
            ->load()
            ->getNVPData('id', 'name');
        if(array_key_exists($this->getData('provinceUnitId'), $areaNames)) {
            $this->setData('provinceName', $areaNames[$this->getData('provinceUnitId')]);
        }
        if(array_key_exists($this->getData('cityUnitId'), $areaNames)) {
            $this->setData('cityName', $areaNames[$this->getData('cityUnitId')]);
        }
        return $this;
    }
    
    public function loadShipping()
    {
        $area = Class_Core::_('Address_Area')->setData('id', $this->getData('cityUnitId'))
            ->load();
        $this->setData('paymentUponArrival', $area->getData('paymentUponArrival'));
        $this->setData('baseShippingPrice', $area->getData('baseShippingPrice'));
    }
    
    public function getShippingDetail()
    {
        $area = Class_Core::_('Address_Area')->setData('id', $this->getData('cityUnitId'))
            ->load();
        $pua = false;
        if($area->getData('paymentUponArrival') == 1) {
            $pua = true;
        } else {
            $pua = false;
        }
        return array('pua' => $pua, 'baseShippingPrice' => $area->getData('baseShippingPrice'));
    }
}