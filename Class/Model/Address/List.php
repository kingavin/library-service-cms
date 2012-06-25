<?php
class Class_Model_Address_List extends Class_Model_List_Abstract
{
    public function __construct()
    {
        $this->_init('address');
        $this->setModelName('Address');
    }
    
    public function loadAllForUser($userId, $printSql = false, $logQry = false)
    {
        $this->getSelect()->joinLeft(
                array('area_city' => 'area'), 
                'area_city.id = main_table.cityUnitId',
                array('area_city.name as cityName')
            )->joinLeft(
                array('area_province' => 'area'), 
                'area_province.id = main_table.provinceUnitId',
                array('area_province.name as provinceName')
            )->where(
            	'main_table.customerId = ?', $this->quote($userId, 'INTEGER')
            );
        $this->load($printSql, $logQry);
        return $this;
    }
}