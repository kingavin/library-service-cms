<?php
class Class_Tree
{
    protected $_treeData;
    protected $_root;
    protected static $_renderer = null;
    
    public function __construct($treeData, Class_Tree_Branch $root)
    {
        $this->_treeData = $treeData;
        $this->setRoot($root);
        $this->_findBranchChildren($root);
    }
    
    protected function _findBranchChildren($branch)
    {
        $count = count($this->_treeData);
        if($count > 0) {
            $id = $branch->getId();
            for($i = 0; $i < $count; $i++) {
                if($this->_treeData[$i]->parentId == $id) {
                    $branch->appendChild($this->_treeData[$i]);
                    $this->_findBranchChildren($this->_treeData[$i]);
                }
            }
        }
        $branch->sortChildren();
        return;
    }
    
    protected function getTrailById($id)
    {
    	$trail = $this->_root->getTrailById($id);
    	return $trail;
    }
    
    public function setRoot($root)
    {
        $this->_root = $root;
        return $this;
    }
    
    public function getRoot()
    {
        return $this->_root;
    }
    
	public function toSelectOptions($preArr = array())
    {
    	$tmpArr = $preArr;
        foreach($this->_root->getChildren() as $cBranch) {
        	$tmpArr[$cBranch->getId()] = $cBranch->getDescription();
        	if($cBranch->hasChildren()) {
        		$tmpArr = $tmpArr + $cBranch->toSelectOptions();
        	}
        }
        return $tmpArr;
    }
    
    public static function getRenderer()
    {
        return self::$_renderer;
    }
    
    public static function setRenderer(Class_Tree_Renderer $renderer)
    {
        self::$_renderer = $renderer;
    }
}