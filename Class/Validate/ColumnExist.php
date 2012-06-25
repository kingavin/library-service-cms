<?php
class Class_Validate_ColumnExist extends Zend_Validate_Abstract
{
  public function isValid($value)
  {
    $userTable = Class_TableFactory::getTable($value['tableName'], array('name'=>$value['tableName']));
    $userSelect = $userTable->select();
    foreach($value['cv'] as $column => $value) {
      $userSelect = $userSelect->where("$column=?", $value);
    }
    $userRows = $userTable->fetchAll($userSelect);
    if(count($userRows) == 0)
      return true;
    return false;
  }
}