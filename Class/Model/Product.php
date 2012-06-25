<?php
class Class_Model_Product extends Class_Model_Product_Abstract
{
    protected $_attributeList;
    public $_thumb;
    
    public function __construct()
    {
        $this->_init('product_entity');
        $this->setEntityTypeId(1);
        $this->_thumb = array('value' => null, 'alt' => null);
        $this->_attributeList = array();
    }
    
    public function setProductType($typeName)
    {
        if(is_null($this->getData('productType'))) {
            $this->setData('productType', $typeName);
        }
        return $this;
    }

    public function getParentId()
    {
        if($this->getProductType() == 'item') {
            $db = Zend_Registry::get('dbAdaptor');
            $parentEntityId = $db->fetchOne('select entityId
  				from product_group_link
				where entityLinkId = ?', $this->getEntityId());
            return $parentEntityId;
        }
        return $this->getEntityId();
    }
    
    public function getProductType()
    {
        return $this->getData('productType');
    }
    
    public function getConfigurableAttributeCollection()
    {
        if(is_null($this->_configurableAttributeCollection)) {
            $this->_configurableAttributeCollection = new Class_Model_Eav_Attribute_Collection();
            $this->_configurableAttributeCollection->setEntity($this)
                    ->setAttributeSetFilter()
                    ->addFilter('isConfigurable', 1)
                    ->load();
        }
        return $this->_configurableAttributeCollection;
    }
    
    public function joinGraphicThumb()
    {
        //$this->_getResource()->joinLeft();
        echo $this->_getResource();
    }
    
    public function getCurrentStock()
    {
        $stockTable = Class_TableFactory::getTable('product_entity_stock', array('name' => 'product_entity_stock'));
        $row = $stockTable->fetchRow($stockTable->select()->where('entityId = ?', $this->getEntityId()));
        return $row['qty'];
    }
    
    /*
     * depricated replaced by addStock
     */
    public function updateStock($qty)
    {
        $stockTable = Class_TableFactory::getTable('product_entity_stock', array('name' => 'product_entity_stock'));
        $row = $stockTable->fetchRow($stockTable->select()->where('entityId = ?', $this->getEntityId()));
        if(is_null($row)) {
           $row = $stockTable->createRow();
           $row->entityId = $this->getEntityId();
        }
        $row->qty = $row['qty'] + $qty;
        $row->save();
        return $this;
    }
    
    public function addStock($qty)
    {
        $stockUpdater = null;
        switch($this->getProductType()) {
            case 'simple':
                $stockUpdater = Class_Core::getModel('Product_Stock_Updater_Simple');
                break;
            case 'item':
                $stockUpdater = Class_Core::getModel('Product_Stock_Updater_Item');
                break;
            case 'group':
                return $this;
            default:
                throw new Exception('no stock updater for product type: '.$this->getProductType());
        }
        $stockUpdater->addStock($this, $qty);
        return $this;
    }
    
    public function checkStock($qty = 1)
    {
        $checker = null;
        switch($this->getProductType()) {
            case 'simple':
            case 'item':
                $checker = Class_Core::getModel('Product_Stock_Checker_Simple');
                break;
            case 'group':
                $checker = Class_Core::getModel('Product_Stock_Checker_Group');
                break;
            default:
                throw new Exception('no stock checker for ENTITY ID: '.$this->getEntityId().', product type: '.$this->getProductType());
        }
        return $checker->checkStock($this, $qty);
    }
    
    public function moveStock($orderId, $qty, $price, $groupId = 0)
    {
        $mover = null;
        switch($this->getProductType()) {
            case 'simple':
            case 'gift':
                $mover = Class_Core::getModel('Product_Stock_Mover_Simple');
                break;
            case 'item':
                $mover = Class_Core::getModel('Product_Stock_Mover_Item');
                break;
            case 'group':
                $mover = Class_Core::getModel('Product_Stock_Mover_Group');
                break;
            default:
                throw new Exception('no stock mover for product type: '.$this->getProductType());
        }
        return $mover->moveStock($this, $orderId, $qty, $price, $groupId);
    }
    
    public function getGraphic($type)
    {
        $graphicLoader = null;
        switch($this->getProductType()) {
            case 'simple':
            case 'configurable':
                $graphicLoader = Class_Core::getModel('Product_Graphic_Simple');
                break;
            case 'item':
                $graphicLoader = Class_Core::getModel('Product_Graphic_Item');
                break;
            case 'group':
                $graphicLoader = Class_Core::getModel('Product_Graphic_Group');
                break;
            default:
                throw new Exception('no graphic loader for '.$this->getProductType());
        }
        if($type == 'thumb') {
            $this->_thumb = $graphicLoader->getGraphicThumb($this);
        }
    }
    
    public function getAttributeByCode($code)
    {
        $attribute = Class_Core::getModel('Eav_Attribute')->setData('code', $code)
            ->load()
            ->loadOptions();
        if(!is_null($attribute->getData('id'))) {
            $this->_attributeList[$attribute->getData('id')]['attribute'] = $attribute;
        }
    }
    
    public function getAttributeByName($name)
    {
        foreach($this->_attributeList as $a) {
            if(strcmp($a->getData('name'), $name) == 0) {
                return $a;
            }
        }
        
        $attributeListModel = Class_Core::getListModel('Eav_Attribute');
        $attributeListModel->getSelect()->joinLeft(
                array('eea' => 'eav_entity_attribute'),
                'main_table.id = eea.attributeId',
                array()
            )->where(
                'eea.attributeSetId = ?', $attributeListModel->getConnection()->quote($this->getAttributeSetId(), 'INTEGER')
            )->where(
                'main_table.name = ?', $name
            );
//            echo $attributeListModel->getSelect()->__toString();
        $attributeList = $attributeListModel->load()->getListData();
        if(count($attributeList) > 0) {
            $attribute = $attributeList[0];
            $attribute->loadOptions();
            
            $this->_attributeList[$attribute->getData('id')] = $attribute;
            return $attribute;
        }
        return null;
    }
    
    public function getValue($attributeName)
    {
//        echo $attributeName.' is the name<br />';
        $loadedAttribute = $this->getAttributeByName($attributeName);
        if(is_null($loadedAttribute)) {
//            echo 'in if';
            return null;
        } else if(count($loadedAttribute->getSelectedValue()) == 0) {
//            echo 'in else if';
            $attributeId = $loadedAttribute->getData('id');
            $storageType = $loadedAttribute->getData('storageType');
            $value = Class_Core::getListModel('Eav_Value')->setEntity($this)
                ->setValueType($storageType)
                ->setAttributeId($attributeId)
                ->loadValue()
                ->getCollectionData();
            $loadedAttribute->setSelectedValue($value);
            return $loadedAttribute->getSelectedOption();
        } else {
//            echo 'in else';
            return $loadedAttribute->getSelectedOption();
        }
    }
    
    public function getValuesByAttributeModel($attribute)
    {
        $storageType = $attribute->getData('storageType');
        $attributeId = $attribute->getData('id');
        
        
        $value = Class_Core::getListModel('Eav_Value')->setEntity($this)
                ->setValueType($storageType)
                ->setAttributeId($attributeId)
                ->loadValue()
                ->getCollectionData();
        return $value;
    }
    
    public function getGlobalSetting(Array $identity = array())
    {
//        $globleAttr = $this->getGlobalAttribute($identity);
//        if(is_null($globleAttr)) {
//            return null;
//        } else if(count($globleAttr) {
//            $attributeId = $globleAttr->getData('id');
//            $storageType = $globleAttr->getData('storageType');
//            $value = Class_Core::getListModel('Eav_Value')->setEntity($this)
//                ->setValueType($storageType)
//                ->setAttributeId($attributeId)
//                ->loadValue()
//                ->getCollectionData();
//            $loadedAttribute->setSelectedValue($value);
//            return $loadedAttribute->getSelectedOptions();
//        } else {
//            return ;
//        }
    }
    
    public function getGlobalAttribute(Array $identity = array())
    {
        
    }
    
//    public function getAttributeListByName($name, $range = 'all')
//    {
//        $attributeList = Class_Core::getListModel('Eav_Attribute')
//            ->addAttributeNameFilter($name)
//            ->load();
//        
//        Zend_Debug::dump($attributeList);
//    }
    
//    
//    public function getGraphic()
//    {
//        
//    }
//    
//    public function memberSelector()
//    {
//        $selector = Class_Core::getListModel('Product');
//        return $selector;
//    }
    
    public function loadMember()
    {
        $this->_memberList = Class_Core::getListModel('Product_Sale')->addComponentDetail($this->getEntityId())
            ->addFilter("main_table.saleType", "group")
            ->load()
            ->getListData();
        return $this;
    }
    
    public function getMember()
    {
        if(!isset($this->_memberList)) {
            $this->loadMember();
        }
        return $this->_memberList;
    }
    
    public function loadConfigItem()
    {
        $this->_configItem = Class_Core::getListModel('Product')->addComponentDetail($this->getEntityId())
            ->addFilter("main_table.saleType", "group")
            ->load()
            ->getListData();
        return $this;
    }
    
    public function getConfigItem()
    {
        return $this->_configItem;
    }
    
    protected function _afterSave()
    {
        $this->getValueCollection('int')->save();
        $this->getValueCollection('varchar')->save();
        $this->getValueCollection('text')->save();
        $this->getValueCollection('datetime')->save();
//        $newValues = $this->getNewValues();
//        $acData = $this->getAttributeCollectionData();
        
//        $intValueCollection = new Class_Model_Eav_Value_Collection();
//        $varcharValueCollection = new Class_Model_Eav_Value_Collection();
//        $textValueCollection = new Class_Model_Eav_Value_Collection();
//        $values = 
        
//        foreach($newValues as $attributeId => $value) {
//            $data = $acData[$attributeId];
//            $table = Class_TableFactory::getTable(
//                $this->getResourceName()."_".$data['storageType'],
//                array('name'=>$this->getResourceName()."_".$data['storageType'])
//            );
//            
//            $resource = $table->fetchAll($table->select()
//                ->where('entityId = ?', $this->getInsertId())
//                ->where('attributeId = ?', $attributeId)
//            )->current();
//            
//            if(is_null($resource)) {
//                $resource = $table->createRow();
//                $resource->entityId = $this->getInsertId();
//                $resource->attributeId = $attributeId;
//            }
//            $resource->value = $value;
//            $resource->save();
//        }
    }
}