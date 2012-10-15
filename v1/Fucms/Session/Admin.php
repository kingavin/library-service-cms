<?php
namespace Fucms\Session;

use Exception;
use SimpleXMLElement;
use Zend\Json\Json;
use Core\Session\SsoUser;

class Admin extends SsoUser
{
	private static $_instance = null;
	
	private static $_md5salt = 'Hgoc&639Jgo';
	private static $_md5salt2 = 'jiohGY6&*9';
	
	private $_isLogin = null;
	
	
	
	
	protected $_sessionContainerName = 'sso\admin';
    /**
     * @return Class_Session_Admin
     */
	/*
    public static function getInstance()
    {
    	if(is_null(self::$_instance)) {
    		self::$_instance = new self();
    	}
    	return self::$_instance;
    }
    */
	/*
	public static function getLiv($userData, $userId, $startTimeStamp)
	{
		return md5($userData.self::$_md5salt.$userId.self::$_md5salt2.$startTimeStamp);
	}
	
	*/
	
	
/*    
	public function hasSSOToken()
	{
		if(isset($_COOKIE['st']) && $_COOKIE['st'] != '') {
			return true;
		}
		return false;
	}
	
	public function getSSOToken()
	{
		if(isset($_COOKIE['st']) && $_COOKIE['st'] != '') {
			return $_COOKIE['st'];
		} else {
			$token = md5(time());
			setcookie('st', $token, time()+$this->_expTime, '/');
			return $token;
		}
	}
*/
	public function login($xml)
	{
		if($xml instanceof SimpleXMLElement) {
			$user = $xml;
		}

		if(is_null($user)) {
			return false;
		}
		$userId = $user->attributes()->userId;
		$startTimeStamp = time();
		$userDataArr = array();
		foreach ($user->children() as $tag => $val) {
	    	$userDataArr[$tag] = (string)$val;
	    }
	    
	    $this->setSessionValue('isLogin', true);
	    $this->setUserData($userDataArr);
//	    $userDataStr = Json::encode($userDataArr);
//		$liv = self::getLiv($userDataStr, $userId, $startTimeStamp);
		
//		$this->_updateCookie(array(
//    		'userId' => $userId,
//        	'startTimeStamp' => $startTimeStamp,
//        	'userData' => $userData,
//        	'liv' => $liv
//		));
		$this->_isLogin = true;
		return true;
	}
	
	public function logout()
	{
		setcookie('userId', '', 1, '/');
		setcookie('startTimeStamp', '', 1, '/');
		setcookie('userData', '', 1, '/');
		setcookie('liv', '', 1, '/');
		$this->_isLogin = false;
	}
	
	/*
	public function isLogin()
	{
		if($this->_isLogin == null) {
			if(isset($_COOKIE['userId']) && $_COOKIE['userId'] != '') {
				$livToken = self::getLiv($_COOKIE['userData'], $_COOKIE['userId'], $_COOKIE['startTimeStamp']);
				if($livToken == $_COOKIE['liv']) {
					$this->_isLogin = true;
				} else {
					$this->_isLogin = false;
					$this->logout();
				}
			} else {
				$this->_isLogin = false;
			}
		}
		return $this->_isLogin;
	}
	*/
	
	public function hasPrivilege()
	{
		if(!$this->isLogin()) {
			return false;
		}
		if(
			$this->getUserData('userType') != 'designer' &&
			($this->getUserData('orgCode') != Class_Server::getOrgCode())
		) {
			return false;
		}
		return true;
	}
	
	public function getHomeLocation()
	{
		return '/';
	}
	
	public function getUserId()
	{
		if($this->isLogin()) {
			return $_COOKIE['userId'];
		}
		return 'nobody';
	}
	
	/*
	public function getUserData($key)
	{
		throw new Exception('deprecated method');
		
		if($this->isLogin()) {
			$userData = Zend_Json::decode($_COOKIE['userData']);
			return $userData[$key];
		}
		return null;
	}
	*/
	public function getOrgCode()
	{
		return $this->getUserData('orgCode');
	}
	
	public function _updateCookie($cookies)
	{
		foreach($cookies as $k => $v) {
    		setcookie($k, $v, time()+$this->_expTime, '/');
    	}
	}
	
	public function getRoleId()
	{
		return 0;
	}
	
	public function isResourceOwner()
	{
		if($this->getUserData('orgCode') == Class_Server::getOrgCode()) {
			return true;
		}
		return false;
	}
	
	/*
	public function setSessionData($name, $value)
	{
		$session = new Zend_Session_Namespace('admin');
		$session->$name = $value;
	}
    
	public function getSessionData($name)
	{
		$session = new Zend_Session_Namespace('admin');
		return $session->$name;
	}
	*/
}