<?php
class Class_Model_List_Abstract
{
    const SORT_ORDER_ASC = 'ASC';
    const SORT_ORDER_DESC = 'DESC';
	
    protected $_select = null;
    
    protected $_resourceName = null;
    protected $_resourceAlias;
    protected $_modelName = null;
    
    protected $_dataCollection = array();
    protected $_newDataCollection = array();
    protected $_dataList = array();
    
    protected $_totalRecords;
    
    protected $_idFieldName;
    
    protected $_filters = array();
    protected $_isFiltersRendered = false;
    
    protected $_orders = array();
    
    protected $_isCollectionLoaded = false;
    
    protected $_pageSize;
    protected $_page;

    //    public function __construct()
    //    {
    //        $this->_init ();
    //    }
    

    protected function _init($resourceName, Array $fields = NULL)
    {
        $this->_db = Zend_Registry::get('dbAdaptor');
        $this->_select = $this->_db->select();
        $this->setResourceName($resourceName);
        if (is_null($fields)) {
            $this->getSelect()->from(array('main_table' => $this->getResourceName()));
        } else {
            $this->getSelect()->from(array('main_table' => $this->getResourceName()), $fields);
        }
    }
    
    protected function _setSelect($resourceName, Array $fields = NULL)
    {
        if(is_null($this->getSelect())) {
            $this->_db = Zend_Registry::get('dbAdaptor');
            $this->_select = $this->_db->select();
            $this->setResourceName($resourceName);
            if (is_null($fields)) {
                $this->getSelect()->from(array('main_table' => $this->getResourceName()));
            } else {
                $this->getSelect()->from(array('main_table' => $this->getResourceName()), $fields);
            }
        }
    }

    protected function _setIdFieldName($fieldName)
    {
        $this->_idFieldName = $fieldName;
        return $this;
    }

    public function getSelect()
    {
        return $this->_select;
    }

    public function getConnection()
    {
        return $this->_db;
    }

    public function getSelectString()
    {
        $this->_renderFilters()->_renderOrders()->_renderLimit();
        return $this->getSelect()->__toString();
    }

    public function getSize()
    {
        if (is_null($this->_totalRecords)) {
            $sql = $this->getSelectCountSql();//echo $sql;die();
            $this->_totalRecords = $this->_db->fetchOne($sql);
        }
        return intval($this->_totalRecords);
    }

    public function getSelectCountSql()
    {
        $this->_renderFilters();
        
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        
        $countSelect->from('', 'COUNT(*)');
        
        return $countSelect;
    }
    
    /*
     * @return Class_Model_List_Abstract
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->_orders[$field] = new Zend_Db_Expr($field . ' ' . $direction);
        return $this;
    }
    
    /*
     * @return Class_Model_List_Abstract
     */
    public function setPageSize($size)
    {
        $this->_pageSize = $size;
        return $this;
    }
    
    /*
     * @return Class_Model_List_Abstract
     */
    public function setPage($page)
    {
        $this->_page = $page;
        return $this;
    }
    
    /*
     * @return Class_Model_List_Abstract
     */
    public function addFilter($field, $value, $type = 'and')
    {
        $filter = array();
        $filter['field'] = $field;
        $filter['value'] = $value;
        $filter['type'] = strtolower($type);
        
        $this->_filters[] = $filter;
        $this->_isFiltersRendered = false;
        return $this;
    }

    protected function _renderFilters()
    {
        if ($this->_isFiltersRendered) {
            return $this;
        }
        foreach ( $this->_filters as $filter ) {
            switch ($filter['type']) {
                case 'or' :
                    $condition = $this->_db->quoteInto($filter['field'] . '=?', $filter['value']);
                    $this->getSelect()->orWhere($condition);
                    break;
                case 'string' :
                    $this->getSelect()->where($filter['value']);
                    break;
                default :
                    $condition = $this->_db->quoteInto($filter['field'] . '=?', $filter['value']);
                    $this->getSelect()->where($condition);
            }
        }
        $this->_isFiltersRendered = true;
        return $this;
    }
    
    /*
     * @return Class_Model_List_Abstract
     */
    protected function _renderOrders()
    {
        foreach ( $this->_orders as $orderExpr ) {
            $this->getSelect()->order($orderExpr);
        }
        return $this;
    }
    
    /*
     * @return Class_Model_List_Abstract
     */
    protected function _renderLimit()
    {
        if ($this->_pageSize) {
            if($this->_page) {
                $this->getSelect()->limitPage(intval($this->_page), $this->_pageSize);
            } else {
                $this->getSelect()->limitPage(1, $this->_pageSize);
            }
        }
        return $this;
    }

    /***********************************************************/
    /**
     * Add field filter to collection
     *
     * If $attribute is an array will add OR condition with following format:
     * array(
     *     array('attribute'=>'firstname', 'like'=>'test%'),
     *     array('attribute'=>'lastname', 'like'=>'test%'),
     * )
     * @return Class_Model_List_Abstract
     */
    public function addFieldToFilter($field, $condition = null)
    {
        $field = $this->_getMappedField($field);
        $this->_select->where($this->_getConditionSql($field, $condition));
        return $this;
    }

    protected function _getMappedField($field)
    {
        $mappedFiled = $field;
        
        $mapper = $this->_getMapper();
        
        if (isset($mapper['fields'][$field])) {
            $mappedFiled = $mapper['fields'][$field];
        }
        
        return $mappedFiled;
    }

    protected function _getMapper()
    {
        if (isset($this->_map)) {
            return $this->_map;
        } else {
            return false;
        }
    }

    protected function _getConditionSql($fieldName, $condition)
    {
        if (is_array($fieldName)) {
            foreach ( $fieldName as $f ) {
                $orSql = array();
                foreach ( $condition as $orCondition ) {
                    $orSql[] = "(" . $this->_getConditionSql($f[0], $f[1]) . ")";
                }
                $sql = "(" . join(" or ", $orSql) . ")";
            }
            return $sql;
        }
        
        $sql = '';
        
        if (is_array($condition)) {
            if (isset($condition['from']) || isset($condition['to'])) {
                if (isset($condition['from'])) {
                    if (empty($condition['date'])) {
                        if (empty($condition['datetime'])) {
                            $from = $condition['from'];
                        } else {
                            $from = $this->getConnection()->convertDateTime($condition['from']);
                        }
                    } else {
                        $from = $this->getConnection()->convertDate($condition['from']);
                    }
                    $sql .= $this->getConnection()->quoteInto("$fieldName >= ?", $from);
                }
                if (isset($condition['to'])) {
                    $sql .= empty($sql) ? '' : ' and ';
                    
                    if (empty($condition['date'])) {
                        if (empty($condition['datetime'])) {
                            $to = $condition['to'];
                        } else {
                            $to = $this->getConnection()->convertDateTime($condition['to']);
                        }
                    } else {
                        $to = $this->getConnection()->convertDate($condition['to']);
                    }
                    
                    $sql .= $this->getConnection()->quoteInto("$fieldName <= ?", $to);
                }
            } elseif (isset($condition['eq'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName = ?", $condition['eq']);
            } elseif (isset($condition['neq'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName != ?", $condition['neq']);
            } elseif (isset($condition['like'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName like ?", $condition['like']);
            } elseif (isset($condition['nlike'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName not like ?", $condition['nlike']);
            } elseif (isset($condition['in'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName in (?)", $condition['in']);
            } elseif (isset($condition['nin'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName not in (?)", $condition['nin']);
            } elseif (isset($condition['is'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName is ?", $condition['is']);
            } elseif (isset($condition['notnull'])) {
                $sql = "$fieldName is NOT NULL";
            } elseif (isset($condition['null'])) {
                $sql = "$fieldName is NULL";
            } elseif (isset($condition['moreq'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName >= ?", $condition['moreq']);
            } elseif (isset($condition['gt'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName > ?", $condition['gt']);
            } elseif (isset($condition['lt'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName < ?", $condition['lt']);
            } elseif (isset($condition['gteq'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName >= ?", $condition['gteq']);
            } elseif (isset($condition['lteq'])) {
                $sql = $this->getConnection()->quoteInto("$fieldName <= ?", $condition['lteq']);
            } elseif (isset($condition['finset'])) {
                $sql = $this->getConnection()->quoteInto("find_in_set(?,$fieldName)", $condition['finset']);
            } elseif (isset($condition['bitand'])) {
            	$sql = $this->getConnection()->quoteInto("$fieldName & ? = ?", $condition['bitand']);
            } else {
                $orSql = array();
                foreach ( $condition as $orCondition ) {
                    $orSql[] = "(" . $this->_getConditionSql($fieldName, $orCondition) . ")";
                }
                $sql = "(" . join(" or ", $orSql) . ")";
            }
        } else {
            $sql = $this->getConnection()->quoteInto("$fieldName = ?", ( string ) $condition);
        }
        return $sql;
    }

    /***********************************************************/
    /*
     * @return Class_Model_List_Abstract
     */
    public function distinct($flag)
    {
        $this->getSelect()->distinct($flag);
        return $this;
    }

    protected function _createModel($rawData)
    {
        if (! is_null($this->_modelName)) {
            return Class_Core::_($this->_modelName)->setData($rawData);
        } else {
            return null;
        }
    }

    public function getListData($indexField = null)
    {
        $tempArr = array();
        if(!is_null($indexField)) {
            foreach($this->_dataList as $data) {
                $tempArr[$data->getData($indexField)] = $data;
            }
        } else {
            $tempArr = $this->_dataList;
        }
        return $tempArr;
    }
    
    /*
     * @return Class_Model_List_Abstract
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($logQuery == true) {
            return $this->getSelect()->__toString();
        }
        
        if ($this->_isCollectionLoaded) {
            return $this;
        }
        
        $this->_renderFilters()->_renderOrders()->_renderLimit();
        
        if ($printQuery == true) {
            echo "<br />**********SQL PRINTING**********<br />";
            echo $this->getSelect()->__toString();
            echo "<br />**********SQL PRINTED**********<br />";
            return $this;
        } else {
            $this->_dataCollection = $this->getConnection()->fetchAll($this->getSelect());
            //Zend_Debug::dump($this->_dataCollection);
			foreach ( $this->_dataCollection as $rawData ) {
                $temp = $this->_createModel($rawData);
                            	//Zend_Debug::dump($temp);
                //modify by tery when $temp is null ,it is a bug
                if (!is_null($temp)) {
                    $this->_dataList[] = $temp;
                }
                //end
            }
            
            $this->_isCollectionLoaded = true;
            $this->_afterLoad();
            return $this;
        }
    }

    protected function _afterLoad()
    {
        return $this;
    }

    protected function _reset()
    {
        $this->getSelect()->reset();
        $this->_isCollectionLoaded = false;
        $this->_dataCollection = array();
        return $this;
    }
    
    public function setModelName($modelName)
    {
        $this->_modelName = $modelName;
        return $this;
    }
    
    public function getModelName()
    {
        return $this->_modelName;
    }

    public function setResourceName($resourceName)
    {
        $this->_resourceName = $resourceName;
        return $this;
    }

    public function getResourceName()
    {
        return $this->_resourceName;
    }

    public function setResourceAlias($resourceAlias)
    {
        $this->_resourceAlias = $resourceAlias;
        return $this;
    }

    public function getResourceAlias()
    {
        return $this->_resourceAlias;
    }

    public function getGroupData($groupKey, $itemKey = null, $valueKey = null)
    {
        $tempArr = array();
        foreach ( $this->getCollectionData() as $data ) {
            $key = $data[$groupKey];
            if (! array_key_exists($key, $tempArr)) {
                $tempArr[$key] = array();
            }
            $d = $data;
            if (! is_null($valueKey)) {
                $d = $data[$valueKey];
            }
            if (! is_null($itemKey)) {
                $tempArr[$key][$data[$itemKey]] = $d;
            } else {
                $tempArr[$key][] = $d;
            }
        }
        return $tempArr;
    }

    public function getNVPData($keyField, $valueField = null)
    {
        $dataNVP = array();
        if (is_null($valueField)) {
            foreach ( $this->_dataCollection as $data ) {
                $dataNVP[$data[$keyField]] = $data;
            }
        } else {
            foreach ( $this->_dataCollection as $data ) {
                $dataNVP[$data[$keyField]] = $data[$valueField];
            }
        }
        return $dataNVP;
    }

    public function addNewData($value)
    {
        $this->_newDataCollection[] = $value;
    }

    public function getNewCollectionData()
    {
        return $this->_newDataCollection;
    }

    public function getCollectionData()
    {
        return $this->_dataCollection;
    }

    public function quote($value, $type = null)
    {
        return $this->_db->quote($value, $type);
    }

    public function beginTransaction()
    {
        $this->_db->beginTransaction();
    }

    public function rollback()
    {
        $this->_db->rollback();
    }

    public function commit()
    {
        $this->_db->commit();
    }
}