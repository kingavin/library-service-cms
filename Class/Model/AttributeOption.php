<?php
class Class_Model_AttributeOption extends Class_Model_Abstract
{
  public function __construct()
  {
    
  }
  
  public function save($form, $post, $avid)
  {
    if($form->isValid($post)) {
      $obj = Class_TableFactory::getRow('eav_attribute_option', array('name'=>'eav_attribute_option'), $avid);
      $obj->id = $avid;
      $obj->setFromArray($form->getValues());
      $obj->save();
      $this->_insertId = $obj->save();
      return true;
    }
    return false;
  }
  
  public function load($aid)
  {
    $obj = Class_TableFactory::getRow('eav_attribute_option', array('name'=>'eav_attribute_option'), $aid);
    return $obj->toArray();
  }
  
  public function loadList($filters)
  {
    $table = Class_TableFactory::getTable('eav_attribute_option', array('name'=>'eav_attribute_option'));
    $qry = $table->select();
    foreach($filters as $key => $val) {
      $qry->where("$key = ?", $val);
    }
    $objs = $table->fetchAll($qry);
    return $objs->toArray();
  }
  
  public function delete($avid)
  {
    $obj = Class_TableFactory::getRow('eav_attribute_option', array('name'=>'eav_attribute_option'), $avid);
    $obj->delete();
    return $this;
  }
}