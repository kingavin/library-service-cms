<?php
class Class_Func
{
	/**
	 * Construct a table model to read database
	 * @param String $modelName
	 * @return Array
	 */
    
    public static function buildArr(Zend_Db_Table_Rowset_Abstract $rowset, $key, $value = null, $arr = array())
	{
		if($value == null) {
			foreach($rowset as $row) {
				$arr[$row->$key] = $row->toArray();
			}
		} else {
			foreach($rowset as $row) {
				$arr[$row->$key] = $row->$value;
			}
		}
		return $arr;
	}
	
	public static function buildSelectOption($rowset, $preArr = null)
	{
		$root = new Class_Tree_Branch();
		$root->setFromArray(array(
        	'id' => 0,
        	'parentId' => null,
            'label' => 'ROOT',
            'sort' => 0
        ));
        
        $groupTree = new Class_Tree($rowset, $root);
        return $groupTree->toSelectOptions($preArr);
	}
	
	public static function count(Zend_Db_Table_Select $selector)
    {
    	$countSelect = clone $selector;
    	$table = $countSelect->getTable();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::FROM);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
		$countSelect->from($table, 'count(*)');
		
        $db = Zend_Registry::get('db');
        $totalRecords = $db->fetchOne($countSelect);
        return intval($totalRecords);
    }
    
    public static function buildNavi()
    {
    	if(Zend_Registry::isRegistered('containerArr')) {
			$containerArr = Zend_Registry::get('container');
		} else {
			$naviRows = array();
			if(!is_null($sectionId)) {
				$naviTb = Class_Base::_('Category');
				$selector = $naviTb->select()->where('sectionId = ?', $sectionId)
					->order('order ASC');
				$naviRows = $naviTb->fetchAll($selector);
			}
			foreach($naviRows as $row) {
			    switch($row->linkType) {
	                case 'uri':
	                    $page = new Zend_Navigation_Page_Uri(array(
	        		    	'uri' => $row->link,
	        		    	'label' => $row->label,
	        		        'title' => $row->label
	        		    ));
	        		    break;
	                case 'frontpage':
	                    $page = new Zend_Navigation_Page_Mvc(array(
	                    	'label' => $row->label,
	                        'title' => $row->label,
	                        'module' => $row->module,
	                        'controller' => $row->controller,
	                    	'action' => $row->action,
	                        'route' => 'frontpage',
	                        'params' => array('frontpageName' => $row->link),
	                    	'reset_params' => true
	                    ));
	        		    break;
	        		case 'home':
	                    $page = new Zend_Navigation_Page_Mvc(array(
	                    	'label' => $row->label,
	                        'title' => $row->label,
	                        'module' => 'default',
	                        'controller' => 'index',
	                    	'action' => 'index',
	                        'route' => 'default'
	                    ));
	        		    break;
	        		default:
	        		    $page = new Zend_Navigation_Page_Mvc(array(
	                    	'label' => $row->label,
	                        'title' => $row->label,
	                        'module' => $row->module,
	                        'controller' => $row->controller,
	                    	'action' => $row->action,
	                        'route' => $row->linkType,
	                    	'params' => array('id' => $row->link),
	                        'reset_params' => true
	                    ));
	        		    break;
			    }
			    $container->addPage($page);
			}
		}
		return $container;
    }
    
    public static function getDomain($requestHost, $ourDomain = "")
    {
		$tlds = array(
			'cc'	=> true,
			'cn'	=> array('com' => true, 'net' => true, 'org' => true),
		    	'com'	=> array('enorange' => true),
			'info'	=> true,
			'me'	=> true,
			'mobi'	=> true,
			'name'	=> true,
			'net'	=> true,
			'org'	=> true,
		    	'uk'	=> array('co' => true),
			'test'	=> true,
		    	'tv'	=> true
		);
		$parts = explode('.', $requestHost);
		$reversedParts = array_reverse($parts);
		$tmp = $tlds;
		
		foreach ($reversedParts as $key => $part) {
		    if (isset($tmp[$part])) {
		        $tmp = $tmp[$part];
		    } else {
		        break;
		    }
		}
		
		$domainNameArr = array_slice($parts, -$key - 1);
		$domainName = implode('.', $domainNameArr);
		
		if($domainName == $ourDomain) {
			$domainNameArr = array_slice($parts, -$key - 2);
			$domainName = implode('.', $domainNameArr);
		}
		return $domainName;
    }
    
    public static function getSubdomain($requestHost, $ourDomain)
    {
    	$hostParts = explode('.', $requestHost);
    	$ourParts = explode('.', $ourDomain);
    	
    	if(count($hostParts) > count($ourParts)) {
    		return $hostParts[0];
    	}
    	return '';
    }
    
    public static function saltMd5($orig)
    {
    	return md5($orig.MD5_SALT);
    }
    
    public static function getNumericArray($start = 1, $end, $inc = 1)
    {
    	$arr = array();
    	for($i = $start; $i < $end; $i = $i+$inc) {
    		$arr[$i] = $i;
    	}
    	return $arr;
    }
}
