<?php
require_once 'cloudfiles.php';
require_once 'cloudfiles_http_local.php';

class Class_Service_Rackspace_File_Instance
{
	static protected $_username = 'enorange';
	static protected $_apikey = '656436588392a51f0fc5f3082fb4b5eb';
	static protected $_auth = null;
	
	public function __construct()
	{
		
	}
	
	/**
	 * @return CF_Authentication
	 * Enter description here ...
	 */
	public function getAuth()
	{
		if(is_null(self::$_auth)) {
			$serializer = Zend_Serializer::factory('PhpSerialize');
			$frontendOptions = array('lifetime' => 3600);
		    $backendOptions = array('cache_dir' => GENERAL_CACHE_PATH);
		    $authCache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
		    
			if(($serializedAuthData = $authCache->load('rackspaceFileAuth')) === false) {
				$auth = new CF_Authentication(self::$_username, self::$_apikey);
				$auth->authenticate();
				$serializedAuthData = $serializer->serialize($auth);
				$authCache->save($serializedAuthData, 'rackspaceFileAuth');
			} else {
				$auth = $serializer->unserialize($serializedAuthData);
			}
			self::$_auth = $auth;
		}
		return self::$_auth;
	}
	
	public function regenerateAuth()
	{
		$serializer = Zend_Serializer::factory('PhpSerialize');
		$frontendOptions = array('lifetime' => 3600);
		$backendOptions = array('cache_dir' => GENERAL_CACHE_PATH);
		$authCache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
		$authCache->  remove('rackspaceFileAuth');
		self::$_auth = null;
		return $this->getAuth();
	}
	
	public function getContainer($containerName)
	{
		if(empty($containerName)) {
			throw new Exception('container name can\'t be null');
		}
		$auth = $this->getAuth();
		try {
			$conn = new CF_Connection($auth);
			$container = $conn->create_container($containerName);
		} catch(Exception $e) {
			$this->regenerateAuth();
			$conn = new CF_Connection($auth);
			$container = $conn->create_container($containerName);
		}
		return $container;
	}
	
	public function createFolder()
	{
		
	}
	
	public function createObject($file, $filepath, $host)
	{
		
	}
	
	public function putFile()
	{
//		$ch = curl_init();
//		curl_setopt($ch, CURLOPT_URL, $host.'/upload.php');
//		curl_setopt($ch, CURLOPT_HEADER, 0);
//		curl_setopt($ch, CURLOPT_VERBOSE, 0);
//		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
//		curl_setopt($ch, CURLOPT_POST, true);
//		$post = array(
//			"file" => '@'.$file,
//			"filepath" => $filepath
//		);
//		curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
//		return $response = curl_exec($ch);
	}
	
	protected function _auth()
	{
		
	}
	
	public function getProvider()
	{
		return "Rackspace US";
	}
}