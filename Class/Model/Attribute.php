<?php
class Class_Model_Attribute extends Class_Model_Abstract
{

    public function __construct()
    {
        $this->_init('eav_attribute');
    }

    public function save($form, $post, $aid)
    {
        if ($form->isValid($post)) {
            $obj = Class_TableFactory::getRow('eav_attribute', array('name' => 'eav_attribute'), $aid);
            $obj->id = $aid;
            $obj->setFromArray($form->getValues());
            $this->_insertId = $obj->save();
            return true;
        }
        return false;
    }

    public function load($avid)
    {
        $obj = Class_TableFactory::getRow('eav_attribute', array('name' => 'eav_attribute'), $avid);
        return $obj->toArray();
    }
    //  public function loadList($filters)
//  {
//    $table = Class_TableFactory::getTable('eav_attribute', array('name'=>'eav_attribute'));
//    $qry = $table->select();
//    foreach($filters as $key => $val) {
//      $qry->where("$key = ?", $val);
//    }
//    $objs = $table->fetchAll($qry);
//    return $objs->toArray();
//  }
}