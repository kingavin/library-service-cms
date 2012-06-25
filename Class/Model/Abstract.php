<?php
class Class_Model_Abstract
{
    protected $_resourceName;
    protected $_resource = null;
    
    protected $_data = array();
    protected $_extraData = array();
    protected $_identityKey = null;
    protected $_primary = array();
    
    protected $_cacheTag = false;
    
    protected $_extraFields = array();
    
    protected function _init($resourceName)
    {
        $this->_setResourceName($resourceName);
        $this->_initData();
    }
    
    protected function _initData()
    {
        $metadata = Class_TableFactory::getTable(
            $this->_resourceName,
            array('name' => $this->_resourceName)
        )->getMetadata();
        foreach($metadata as $fieldName => $description) {
            $this->_data[$fieldName] = null;
            if($description['PRIMARY']) {
                $this->_primary[] = $fieldName;
            }
        }
    }

    protected function _setResourceName($resourceName)
    {
        if (!isset($resourceName)) {
            throw new Exception("Resouce name required!");
        }
        $this->_resourceName = $resourceName;
    }
    
    protected function _getResourceName()
    {
        return $this->_resourceName;
    }

    protected function _getResource()
    {
        if(empty($this->_resourceName)) {
            throw new Exception('Resource is not set');
        }
        if(!is_null($this->_resource)) {
            return $this->_resource;
        }
        //2009-09//
        $this->_getResourceById();
        if(!is_null($this->_resource)) {
            return $this->_resource;
        }
        //2009-09//
        $table = Class_TableFactory::getTable(
            $this->_resourceName,
            array('name' => $this->_resourceName)
        );
        $select = $table->select();
        
        $whereFlag = false;
        foreach($this->_data as $k => $v) {
            if(!is_null($v)) {
                $whereFlag = true;
                $select->where($k.' = ?', $v);
            }
        }
        if(!$whereFlag) {
            throw new Exception('Searching fields required');
        }
        $this->_resource = $table->fetchRow($select);
        if($this->_resource == null) {
            $this->_resource = $table->createRow();
        }
        return $this->_resource;
    }
    
    protected function _getResourceById()
    {
        if(empty($this->_resourceName)) {
            throw new Exception('Resource is not set');
        }
        if(!is_null($this->_resource)) {
            return $this->_resource;
        }
        
        $table = Class_TableFactory::getTable(
            $this->_resourceName,
            array('name' => $this->_resourceName)
        );
        $select = $table->select();
        
        foreach($this->_primary as $key) {
            $v = $this->_data[$key];
            if(is_null($v)) {
                return null;
            } else {
                $select->where($key.' = ?', $v);
            }
        }
        $this->_resource = $table->fetchRow($select);
        return $this->_resource;
    }
    
//disabled by gavin
//I would like to implement another function for _getResourceById()
//because list return object data, but null returns if any data is changed before save()....
//added function are marked by 2009-09 tag
    
//    public function loadById()
//    {
//        $this->_data = $this->_getResourceById()->toArray();
//        $this->_afterLoad();
//        return $this;
//    }
//

    
//    public function addField(Array $table, $link, Array $fields)
//    {
//        $tempArr = array();
//        $tempArr['table'] = $table;
//        $tempArr['link'] = $link;
//        $tempArr['fields'] = $fields;
//        
//        $this->_extraFields[] = $tempArr;
//        return $this;
//    }
    
//    public function _loadResource()
//    {
//        if(empty($this->_resourceName)) {
//            throw new Exception('Resource is not set');
//        }
//        
//        $table = Class_TableFactory::getTable(
//            $this->_resourceName,
//            array('name' => $this->_resourceName)
//        );
//        $select = $table->select();
//        
//        $whereFlag = false;
//        foreach($this->_data as $k => $v) {
//            if(!is_null($v)) {
//                $whereFlag = true;
//                $select->where($k.' = ?', $v);
//            }
//        }
//        
//        if(!$whereFlag) {
//            throw new Exception('Searching fields required');
//        }
//        
//        foreach($this->_extraFields as $ef) {
//            $select->joinLeft(
//                $ef['table'],
//                $ef['link'],
//                $ef['fields']
//            );
//        }
//        
//        $this->_resource = $table->fetchRow($select);
//        if($this->_resource == null) {
//            $this->_resource = $table->createRow();
//        }
//        return $this->_resource;
//    }
    
    protected function _getEmptyResource()
    {
        $table = Class_TableFactory::getTable(
            $this->_resourceName,
            array('name' => $this->_resourceName)
        );
        $this->_resource = $table->createRow();
    }
    
    protected function _afterLoad()
    {}
    
    protected function _beforeSave()
    {}

    protected function _afterSave()
    {}
    
    protected function _beforeUpdate()
    {}
    
    protected function _afterUpdate()
    {}
    
    protected function _beforeDelete()
    {}

    protected function _afterDelete()
    {}
    
    /*
     * @return Class_Model_Abstract
     */
    public function create()
    {
        $this->_getEmptyResource();
        $this->_afterLoad();
        return $this;
    }
    
    /*
     * @return Class_Model_Abstract
     */
    public function load()
    {
        $this->_data = $this->_getResource()->toArray();
        $this->_afterLoad();
        return $this;
    }
    
    /*
     * @return Class_Model_Abstract
     */
    public function save($transaction = true)
    {
        $this->_beforeSave();
        $resource = $this->_getResource();
        foreach($this->_data as $k => $v) {
            if($resource->$k != $v) {
                $resource->$k = $v;
            }
        }
        
        if($transaction) {
            $db = Zend_Registry::get('dbAdaptor');
            $db->beginTransaction();
            try {
                $this->_insertId = $resource->save();
                $this->_data = $resource->toArray();
                $this->_afterSave();
                $db->commit();
            } catch(Exception $e) {
                $db->rollback();
                throw $e;
            }
        } else {
            try {
                $this->_insertId = $resource->save();
                $this->_data = $resource->toArray();
                $this->_afterSave();
            } catch(Exception $e) {
                throw $e;
            }
        }
        return $this;
    }
    
    /*
     * @return Class_Model_Abstract
     */
    public function update()
    {
        $this->_beforeUpdate();
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
            $this->_afterUpdate();
            $db->commit();
        } catch(Exception $e) {
            $db->rollback();
            throw $e;
        }
        return $this;
    }
    
    /*
     * @return Class_Model_Abstract
     */
    public function delete()
    {
        $db = Zend_Registry::get('dbAdaptor');
        $db->beginTransaction();
        
        try {
            $this->_getResource()->delete();
            $this->_afterDelete();
            $db->commit();
        } catch(Exception $e) {
            $db->rollback();
            throw $e;
        }
        
        return $this;
    }
    
    public function getResource()
    {
        return $this->_getResource();
    }
    
    public function getResourceName()
    {
        return $this->_getResourceName();
    }
    
    public function reset()
    {
        $this->_resource = null;
        $this->_initData();
    }
    
    /*
     * @return Class_Model_Abstract
     */
    public function setData($key, $value = null)
    {
        if(is_array($key)) {
            foreach($key as $k => $v) {
                if(array_key_exists($k, $this->_data)) {
                    $this->_data[$k] = $v;
                } else {
                    $this->_extraData[$k] = $v;
                }
            }
        } else {
            if(array_key_exists($key, $this->_data)) {
                $this->_data[$key] = $value;
            } else {
                $this->_extraData[$key] = $value;
            }
        }
        return $this;
    }

    public function getData($key = '')
    {
        if ($key === '') {
            return $this->_data;
        } else {
            if(array_key_exists($key, $this->_data)) {
                return $this->_data[$key];
            } else if(array_key_exists($key, $this->_extraData)) {
                return $this->_extraData[$key];
            }
        }
        return null;
    }
    
    public function getExtraData()
    {
        return $this->_extraData;
    }
    
    public function getInsertId()
    {
        return $this->_insertId;
    }
}