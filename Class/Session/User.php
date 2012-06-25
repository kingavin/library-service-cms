<?php
class Class_Session_User
{
	private function __construct(){}
	private function __clone(){}
	private static $_instance = null;

	private static $_md5salt = '^hjkIOU#1&';
	private static $_md5salt2 = '*89o54i23a?';

	private $_isLogin = null;

	/**
	 * @return Class_Session_User
	 */
	public static function getInstance()
	{
		if(is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public static function getMd5Password($password)
	{
		$md5Password = md5($password.self::$_md5salt);
		return $md5Password;
	}

	public static function getLiv($loginName, $userId)
	{
		return md5($loginName.self::$_md5salt.$userId.self::$_md5salt2);
	}

	public function register($loginName, $password)
	{
		if(!is_null($loginName) && !is_null($password)) {
			$userCo = App_Factory::_m('User');
			$userDoc = $userCo->addFilter('loginName', $loginName)->
				fetchOne();
			
			if(!empty($userDoc)) {
				return false;
			}
			
			$userDoc = $userCo->create();
			$userDoc->loginName = $loginName;
			$userDoc->password = self::getMd5Password($password);
			$userDoc->created = date('Y-m-d H:i', time());
			$userDoc->status = 'active';
			$userDoc->save();
			$this->login($userDoc);
		}
		return $userDoc;
	}

	public function login($post)
	{
		$userDoc = null;
		if(is_array($post)) {
			if(isset($post['loginName']) && isset($post['password'])) {
				$loginName = $post['loginName'];
				$password = $post['password'];
				$md5Password = self::getMd5Password($password);
				
				$userCo = App_Factory::_m('User');
				$userDoc = $userCo->fetchOne(array('loginName' => $loginName, 'password' => $md5Password));
				
			}
		} else if($post instanceof Class_Mongo_User_Doc) {
			$user = $post;
		}

		if(is_null($userDoc)) {
			return false;
		}
		$userDoc->lastLogin = date("Y-m-d H:i:s");
		$userDoc->save();

		$this->_updateCookie(array(
    		'user_loginName' => $userDoc->loginName,
    		'user_id' => $userDoc->getId(),
        	'user_liv' => self::getLiv($userDoc->loginName, $userDoc->getId())
		));
		$this->_isLogin = true;
		return true;
	}

	public function logout()
	{
		setcookie('user_loginName', '', 1, '/');
		setcookie('user_id', '', 1, '/');
		setcookie('user_liv', '', 1, '/');
		$this->_isLogin = false;
	}

	public function isLogin()
	{
		if($this->_isLogin == null) {
			if(isset($_COOKIE['user_loginName']) && $_COOKIE['user_loginName'] != '' && isset($_COOKIE['user_id']) && $_COOKIE['user_id'] != '') {
				$livToken = self::getLiv($_COOKIE['user_loginName'], $_COOKIE['user_id']);
				if($livToken == $_COOKIE['user_liv']) {
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

	public function getLoginName()
	{
		if($this->isLogin()) {
			return $_COOKIE['user_loginName'];
		}
		return 'nobody';
	}

	public function getUserId()
	{
		if($this->isLogin()) {
			return $_COOKIE['user_id'];
		}
		return 'nobody';
	}

	public function _updateCookie($arr, $domainName = null)
	{
		if($domainName == null) {
			setcookie('user_loginName', $arr['user_loginName'], time()+60*60*24*7, '/');
			setcookie('user_id', $arr['user_id'], time()+60*60*24*7, '/');
			setcookie('user_liv', $arr['user_liv'], time()+60*60*24*7, '/');
		} else {
			setcookie('user_loginName', $arr['user_loginName'], time()+60*60*24*7, '/', $domainName);
			setcookie('user_id', $arr['user_id'], time()+60*60*24*7, '/', $domainName);
			setcookie('user_liv', $arr['user_liv'], time()+60*60*24*7, '/', $domainName);
		}
	}
}