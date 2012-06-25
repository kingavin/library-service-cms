<?php
class Class_Tree_Solid_Group
{
	protected static $_root;
	
    public static function getRoot()
    {
    	if(is_null(self::$_root)) {
	        $groupTb = Class_Base::_('Group');
	        $data = $groupTb->fetchAll();
	        
	    	$root = $groupTb->createRow(array(
	        	'id' => 0,
	        	'parentId' => null,
	            'label' => 'ROOT',
	            'sort' => 0
	        ));
        	
	        $tree = new Class_Tree($data, $root);
	        self::$_root = $tree->getRoot();
    	}
    	return self::$_root;
    }
    
    /**
	 * Find a branch by a given ID
	 * @param Interger $id
	 * @return Class_Tree_Branch
	 */
    
    public static function findBranchById($id)
    {
    	$root = self::getRoot();
    	return $root->findChildObjById($id);
    }
}