<?php
class Class_Server
{
	const API_KEY = 'zvmiopav7BbuifbahoUifbqov541huog5vua4ofaweafeq98fvvxreqh';
	
	protected static $_siteId = null;
	protected static $_orgCode = null;
	
	protected static $_configPath = null;
	protected static $_config = null;
	protected static $_enviroment = 'production';
	protected static $_libVersion = 'v1';
	protected static $_siteFolder = null;
	
	public static function config($env, $libVersion, $siteId, $orgCode, $siteFolder)
	{
		self::$_enviroment = $env;
		self::$_libVersion = $libVersion;
		self::$_siteId = $siteId;
		self::$_orgCode = $orgCode;
		self::$_siteFolder = $siteFolder;
	}
	
	public static function setLibVersion($ver)
	{
		self::$_libVersion = $ver;
	}
	
	public static function getImageUrl()
	{
		return 'http://storage.aliyun.com/public-misc';
	}
	
	public static function getImageFolderUrl()
	{
		die('get-image-folder-url function removed! user getImageUrl instead!!');
		$url = self::getImageUrl();
		$url.= '/'.self::$_siteFolder;
		return $url;
	}
	
	public static function getSiteUrl()
	{	
		return 'http://'.$_SERVER['HTTP_HOST'];
	}
	
	public static function setSiteId($id)
	{
		self::$_siteId = $id;
	}
	
	public static function getSiteFolderPath()
	{
		$url = self::getImageUrl();
		$url.= '/'.self::$_siteFolder;
		return $url;
	}
	
	public static function getSiteId()
	{
		if(is_null(self::$_siteId)) {
			throw new Exception('not able to detect site id');
		}
		return self::$_siteId;
	}
	
	public static function getSUId()
	{
		return self::getServerId().'-'.self::getSiteId();
	}
	
	public static function getServerId()
	{
		$config = self::getConfig();
		return $config->server->id;
	}
	
	public static function getSiteFolder()
	{
		return self::$_siteFolder;
	}
	
	public static function getEnv()
	{
		return self::$_enviroment;
	}
	
	public static function extUrl()
	{
		if(self::$_enviroment == 'production') {
			$url = "http://st.onlinefu.com/ext";
		} else {
			$url = "http://lib.eo.test/ext";
		}
		return $url;
	}
	
	public static function libUrl()
	{
//		$url = "http://";
//		$url.= self::name('lib');
//		$url.= '/cms/'.self::$_libVersion;
		if(self::$_enviroment == 'production') {
			$url = "http://st.onlinefu.com/cms/".self::$_libVersion;
		} else {
			$url = "http://lib.eo.test/cms/".self::$_libVersion;
		}
		return $url;
	}
	
	public static function fileUrl()
	{
		return 'http://storage.aliyun.com/public-misc/'.self::$_orgCode;
	}
	
	public static function getFileServer()
	{
		if(self::$_enviroment == 'production') {
			$url = "http://file.enorange.com";
		} else {
			$url = "http://file.eo.test";
		}
		return $url;
	}
	
	public static function miscUrl()
	{
		$url = "http://";
		$url.= self::name('misc');
		if(!is_null(self::$_siteFolder)) {
			$url.= '/'.self::$_siteFolder;
		}
		return $url;
	}
	
	public static function name($type = null)
	{
//		$config = self::getConfig();
		$name = null;
		switch($type) {
			case 'ext':
				$name = 'lib.enorange.test';
				break;
			case 'lib':
				$name = 'lib.enorange.com';
				break;
			case 'misc':
				$name = 'misc.enorange.com';
				break;
			default:
				throw new Exception('server type '.$type.' is not defined');
		}
		return $name;
	}
	
	protected static function getConfig()
	{
		if(self::$_config == null) {
			if(is_null(self::$_configPath)) {
				throw new Exception('config file server.ini required, use setConfigPath() to set the right path before usage!');
			}
			self::$_config = new Zend_Config_Ini(self::$_configPath, 'localhost');
		}
		return self::$_config;
	}
	
	public static function setConfigPath($path)
	{
		self::$_configPath = $path;
	}
	
	public static function getOrgCode()
	{
		return self::$_orgCode;
	}
	
	public static function getMongoServer()
	{
		if(self::$_enviroment == 'production') {
			return 'mongodb://craftgavin:whothirstformagic?@127.0.0.1';
		} else {
			return '127.0.0.1';
		}
	}
}