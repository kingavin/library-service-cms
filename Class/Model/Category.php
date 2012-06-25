<?php
class Class_Model_Category extends Zend_Db_Table_Row_Abstract
{
    protected $_children = array();
    protected $_hasChildren = false;
    
    public function appendChild(Class_Model_Category $branch)
    {
        if($this->id == $branch->parentId) {
            $this->_children[$branch->order] = $branch;
            $this->_hasChildren = true;
        }
        return $this;
    }
    
    public function hasChildren()
    {
        return $this->_hasChildren;
    }
    
    public function findChildObjById($id = null)
    {
        if($this->id == $id) {
            return $this;
        }
        if($this->hasChildren()) {
            foreach($this->_children as $child) {
                if($child->findChildObjById($id) != null) {
                    return $child->findChildObjById($id);
                }
            }
        }
        return null;
    }
    
    public function getChildren($id = null)
    {
        if(!is_null($id)) {
            foreach($this->_children as $child) {
                if($child->id == $id) {
                    return $child;
                }
            }
            return null;
        }
        return $this->_children;
    }
    
    public function sortChildren()
    {
        ksort($this->_children);
        return $this;
    }
    
    public function render($param = array())
    {
        if(Class_Category::getRenderer() == null) {
            $renderer = new Class_Tree_Renderer();
        } else {
            $renderer = Class_Category::getRenderer();
        }
        ob_start();
        $renderer->run($this, $param);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
    
    public function getLeafs()
    {
        $tmpArr = array();
        if(!$this->hasChildren()) {
            $tmpArr[$this->id] = $this->id;
        }
        foreach($this->getChildren() as $cBranch) {
            $tmpArr += $cBranch->getLeafs();
        }
        return $tmpArr;
    }
    
    public function getNotLeafs()
    {
        $tmpArr = array();
        if($this->hasChildren()) {
            $tmpArr[$this->id] = $this->id;
        }
        foreach($this->getChildren() as $cBranch) {
            $tmpArr += $cBranch->getNotLeafs();
        }
        return $tmpArr;
    }
}