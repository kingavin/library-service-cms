<?php
require_once 'sdk.class.php';

class Class_Api_Oss_Instance
{
	protected $_alioss;
	
	protected static $_instance;
	
	protected function __construct()
	{
		$this->_alioss = new ALIOSS();
	}
	
	protected function __clone(){}
	
	/**
	 * 
	 * @return Class_Api_Oss_Instance
	 */
	public static function getInstance()
	{
		if(self::$_instance == null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function createObject($bucket, $filename, $content, $size = null)
	{
		$size = is_null($size) ? strlen($content) : $size;
		
		$upload_file_options = array(
			'content' => $content,
		 	'length' => $size,
		);
		$result = $this->_alioss->upload_file_by_content(
			$bucket,
			$filename,
			$upload_file_options
		);
		return $result;
	}
	
	public function setBucketAcl($bucket, $acl = 'public-read')
	{
		$result = $this->_alioss->set_bucket_acl($bucket, $acl);
		return $result;
	}
	
	public function removeObject($bucket, $object)
	{
		$result = $this->_alioss->delete_object($bucket, $object);
		return $result;
	}
	
	public function listObject($bucket, $options)
	{
		$result = $this->_alioss->list_object($bucket, $options);
		return $result;
	}
	
	public function copyObject($from_object, $to_object, $from_bucket = 'public-misc', $to_bucket = 'public-misc')
	{
		$result = $this->_alioss->copy_object($from_bucket, $from_object, $to_bucket, $to_object);
		return $result;
	}
	
	public function getProvider()
	{
		return "Alioss";
	}
}