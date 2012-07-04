<?php
abstract class Class_Tree_Branch extends Zend_Db_Table_Row_Abstract
{
    protected $_children = array();
    protected $_hasChildren = false;
    
    abstract function getId();
    abstract function getOrder();
    abstract function getDescription();
    
    public function appendChild(Class_Tree_Branch $branch)
    {
        if($this->getId() == $branch->parentId) {
            $this->_children[] = $branch;
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
        $this->_bubbleSort(&$this->_children);
        return $this;
    }
    
    protected function _bubbleSort(&$array)
    {
		$count = count($array);
		if ($count <= 0)
			return false;
		for($i = 0; $i < $count; $i++) {
			for($j = $count - 1; $j > $i; $j--)	{
				$jKey = intval($array[$j]->getOrder());
				$jmKey = intval($array[$j-1]->getOrder());
				if($jKey < $jmKey) {
					$tmp = $array[$j];
					$array[$j] = $array[$j-1];
					$array[$j-1] = $tmp;
				}
			}
		}
    }
    
    public function getTrailById($id)
    {
    	if($this->getId() == $id) {
    		return array($this);
    	}
    	if($this->_hasChildren) {
	    	foreach($this->_children as $child) {
	    		$childTrails = $child->getTrailById($id, &$trailArr);
	    		if($childTrails !== false) {
					array_unshift($childTrails, $this);
    				return $childTrails;
	    		}
	    	}
	    	return false;
    	}
    	return false;
    }
    
    public function render($param = array())
    {
        if(Class_Tree::getRenderer() == null) {
            $renderer = new Class_Tree_Renderer();
        } else {
            $renderer = Class_Tree::getRenderer();
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
    
    public function toSelectOptions($prefix = '--')
    {
    	$tmpArr = array();
        foreach($this->getChildren() as $cBranch) {
        	$tmpArr[$cBranch->getId()] = $prefix.$cBranch->getDescription();
        	if($cBranch->hasChildren()) {
        		$tmpArr = $tmpArr + $cBranch->toSelectOptions();
        	}
        }
        return $tmpArr;
    }
    
    public function __toString()
    {
        return $this->id.' is the Branch ID';
    }
}
