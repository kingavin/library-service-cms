<?php
/**
 * OSS(Open Storage Services) PHP SDK v1.0.0
 */


//设置默认时区
date_default_timezone_set('Asia/shanghai');

//检测OSS_API_PATHO是否设置
if(!defined('OSS_API_PATH'))
define('OSS_API_PATH', dirname(__FILE__));


//Look for include file in the same directory (e.g. `./conf.inc.php`).
require_once OSS_API_PATH.DIRECTORY_SEPARATOR.'conf.inc.php';

//Load Request Class
require_once OSS_API_PATH.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'requestcore'.DIRECTORY_SEPARATOR.'requestcore.class.php';

//Load MimeTypes
require_once OSS_API_PATH.DIRECTORY_SEPARATOR.'util'.DIRECTORY_SEPARATOR.'mimetypes.class.php';

//检测语言包
try{
	if(file_exists(OSS_API_PATH.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.LANG.'.inc.php')){
		require_once OSS_API_PATH.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.LANG.'.inc.php';
	}else{
		throw new OSS_Exception(OSS_LANG_FILE_NOT_EXIST);
	}
}catch (OSS_Exception $e){
	die($e->getMessage());
}

// EXCEPTIONS

/**
 * Default OSS_Exception.
 */
class OSS_Exception extends Exception {}


//Check Curl extention
//$extensions = get_loaded_extensions();
//try {
//	if($extensions){
//		if(!in_array('curl', $extensions)){
//			throw new OSS_Exception(OSS_CURL_EXTENSION_MUST_BE_LOAD);
//		}
//	}else{
//		throw new OSS_Exception(OSS_NO_ANY_EXTENSIONS_LOADED);
//	}
//}catch (OSS_Exception $e){
//	die($e->getMessage());
//}
//not required

//CLASS
/**
 * OSS PHP SDK 基类，以便于将来Service端扩展基类
 * @author xiaobing.meng@alibaba-inc.com
 * @since 2011-11-14
 */
class ALIOSS{
	/*%******************************************************************************************%*/
	// CONSTANTS

	/**
	 * 阿里云OSS服务公网地址
	 */
	const DEFAULT_OSS_HOST = 'storage.aliyun.com';
	//const DEFAULT_OSS_HOST = 'oss-test.aliyun-inc.com:8080';

	/**
	 * Name of software
	 */
	const NAME = OSS_NAME;

	/**
	 * Build id of software
	 */
	const BUILD = OSS_BUILD;

	/**
	 * Version of software
	 */
	const VERSION = OSS_VERSION;

	/**
	 * Author of software
	 */
	const AUTHOR = OSS_AUTHOR;

	/*%******************************************************************************************%*/
	//OSS内部常量

	const OSS_BUCKET = 'bucket';
	const OSS_OBJECT = 'object';
	const OSS_HEADERS = 'headers';
	const OSS_METHOD = 'method';
	const OSS_QUERY = 'query';
	const OSS_BASENAME = 'basename';
	const OSS_MAX_KEYS = 'max-keys';
	const OSS_UPLOAD_ID = 'uploadId';
	const OSS_MAX_KEYS_VALUE = 100;
	const OSS_MAX_OBJECT_GROUP_VALUE = 1000;
	const OSS_FILE_SLICE_SIZE = 8192;
	const OSS_PREFIX = 'prefix';
	const OSS_DELIMITER = 'delimiter';
	const OSS_MARKER = 'marker';
	const OSS_CONTENT_MD5 = 'Content-Md5';
	const OSS_CONTENT_TYPE = 'Content-Type';
	const OSS_CONTENT_LENGTH = 'Content-Length';
	const OSS_IF_MODIFIED_SINCE = 'If-Modified-Since';
	const OSS_IF_UNMODIFIED_SINCE = 'If-Unmodified-Since';
	const OSS_IF_MATCH = 'If-Match';
	const OSS_IF_NONE_MATCH = 'If-None-Match';
	const OSS_CACHE_CONTROL = 'Cache-Control';
	const OSS_EXPIRES = 'Expires';
	const OSS_CONTENT_COING = 'Content-Coding';
	const OSS_CONTENT_DISPOSTION = 'Content-Disposition';
	const OSS_RANGE = 'Range';
	const OS_CONTENT_RANGE = 'Content-Range';
	const OSS_CONTENT = 'content';
	const OSS_LENGTH = 'length';
	const OSS_HOST = 'Host';
	const OSS_DATE = 'Date';
	const OSS_AUTHORIZATION = 'Authorization';
	const OSS_DEFAULT_PREFIX = 'x-oss-';

	/*%******************************************************************************************%*/
	//外链URL相关常量

	const OSS_URL_ACCESS_KEY_ID = 'OSSAccessKeyId';
	const OSS_URL_EXPIRES = 'Expires';
	const OSS_URL_SIGNATURE = 'Signature';

	/*%******************************************************************************************%*/
	//请求方法常量

	const OSS_HTTP_GET = 'GET';
	const OSS_HTTP_PUT = 'PUT';
	const OSS_HTTP_HEAD = 'HEAD';
	const OSS_HTTP_POST = 'POST';
	const OSS_HTTP_DELETE = 'DELETE';


	/*%******************************************************************************************%*/
	//ACL TYPE

	//ACL
	const OSS_ACL = 'x-oss-acl';

	//OBJECT GROUP
	const OSS_OBJECT_GROUP = 'x-oss-file-group';
	
	//Multi Part
	const OSS_MULTI_PART = 'uploads';

	//OBJECT COPY SOURCE
	const OSS_OBJECT_COPY_SOURCE = 'x-oss-copy-source';

	//私有权限，仅限于bucket的所有者
	const OSS_ACL_TYPE_PRIVATE = 'private';

	//公共读权限,
	const OSS_ACL_TYPE_PUBLIC_READ = 'public-read';

	//所有权限
	const OSS_ACL_TYPE_PUBLIC_READ_WRITE = 'public-read-write';

	//OSS ACL类型数组
	static $OSS_ACL_TYPES = array(
	self::OSS_ACL_TYPE_PRIVATE,
	self::OSS_ACL_TYPE_PUBLIC_READ,
	self::OSS_ACL_TYPE_PUBLIC_READ_WRITE
	);


	/*%******************************************************************************************%*/
	// PROPERTIES

	/**
	 * 是否使用SSL
	 */
	protected $use_ssl = false;

	/**
	 * 是否开启debug模式
	 */
	protected $debug_mode = false;
	
	/**
	 * The OSS API ACCESS ID
	 */
	private $access_id;

	/**
	 * The OSS API ACCESS KEY
	 */
	private $access_key;

	/**
	 * OSS server 地址
	 */
	private $hostname;

	/**
	 * OSS语言包
	 */
	private $lang;

	/*%******************************************************************************************************%*/
	//构造函数

	/**
	 * 构造函数
	 * @param string $_access_id (Optional)
	 * @param string $access_key (Optional)
	 * @param string $hostname (Optional)
	 * @throws OSS_Exception
	 * @author	xiaobing.meng@alibaba-inc.com
	 * @since	2011-11-08
	 */
	public function __construct($access_id = NULL,$access_key = NULL, $hostname = NULL  ){
		//验证access_id,access_key
		try{
			if(!$access_id && !defined('OSS_ACCESS_ID')){
				throw new OSS_Exception(NOT_SET_OSS_ACCESS_ID);
			}
		}catch (OSS_Exception $e){
			die($e->getMessage());
		}

		try{
			if(!$access_key && !defined('OSS_ACCESS_KEY')){
				throw new OSS_Exception(NOT_SET_OSS_ACCESS_KEY);
			}
		}catch (OSS_Exception $e){
			die($e->getMessage());
		}

		try{
			if($access_id && $access_key){
				$this->access_id = $access_id;
				$this->access_key = $access_key;
				return true;
			}elseif (defined('OSS_ACCESS_ID') && defined('OSS_ACCESS_KEY')){
				$this->access_id = OSS_ACCESS_ID;
				$this->access_key = OSS_ACCESS_KEY;
			}else{
				throw new OSS_Exception(NOT_SET_OSS_ACCESS_ID_AND_ACCESS_KEY);
			}
		}catch (OSS_Exception $e){
			die($e->getMessage());
		}

		//验证access_id 和 access_key 是否为空
		try{
			if(empty($this->access_id) || empty($this->access_key)){
				throw new OSS_Exception(OSS_ACCESS_ID_OR_ACCESS_KEY_EMPTY);
			}
		}catch (OSS_Exception $e){
			die($e->getMessage());
		}

		//验证主机地址
		if(NULL === $hostname){
			$this->hostname = self::DEFAULT_OSS_HOST;
		}else{
			$this->hostname = $hostname;
		}
	}


	/*%******************************************************************************************************%*/
	//请求相关

	/**
	 * 发送消息到OSS
	 * @param array $options (Required)
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 */
	public function auth($options){
		//print_R($options);die();
		//验证Bucket,list_bucket时不需要验证
		try{
			if(!( ('/' == $options[self::OSS_OBJECT]) && ('' == $options[self::OSS_BUCKET]) && ('GET' == $options[self::OSS_METHOD])) && !$this->validate_bucket($options[self::OSS_BUCKET])){
				throw new OSS_Exception('"'.$options[self::OSS_BUCKET].'"'.OSS_BUCKET_NAME_INVALID,'-100');
			}
		}catch (OSS_Exception $e){
			die($e->getMessage());
		}

		//验证Object
		try{
			if(isset($options[self::OSS_OBJECT]) && !$this->validate_object($options[self::OSS_OBJECT])){
				throw  new OSS_Exception($options[self::OSS_OBJECT].OSS_OBJECT_NAME_INVALID, -300);
			}
		}catch (OSS_Exception $e){
			die($e->getMessage());
		}

		//验证ACL
		try{
			if(isset($options[self::OSS_HEADERS][self::OSS_ACL]) && !empty($options[self::OSS_HEADERS][self::OSS_ACL])){
				if(!in_array(strtolower($options[self::OSS_HEADERS][self::OSS_ACL]), self::$OSS_ACL_TYPES)){
					throw new OSS_Exception($options[self::OSS_HEADERS][self::OSS_ACL].':'.OSS_ACL_INVALID, '-200');
				}
			}
		}catch (OSS_Exception $e){
			die($e->getMessage());
		}

		//构造url
		$url = $this->make_url($options);
		$options ['url'] = $url;

		//记录日志
		$this->log ( "[method:" . $options [self::OSS_METHOD] . "][url:$url]", $options );

		//创建请求
		$request = new RequestCore ( $options ['url'] );
	
		// Streaming uploads
		if (isset($options['fileUpload']))
		{
			if (is_resource($options['fileUpload']))
			{
				// Determine the length to read from the stream
				$length = null; // From current position until EOF by default, size determined by set_read_stream()

				if (isset($options[self::OSS_CONTENT_LENGTH]))
				{
					$length = $options[self::OSS_CONTENT_LENGTH];
				}
				elseif (isset($options['seekTo']))
				{
					// Read from seekTo until EOF by default
					$stats = fstat($options['fileUpload']);

					if ($stats && $stats['size'] >= 0)
					{
						$length = $stats['size'] - (integer) $options['seekTo'];
					}
				}

				$request->set_read_stream($options['fileUpload'], $length);

				if ($options[self::OSS_CONTENT_TYPE] === 'application/x-www-form-urlencoded')
				{
					$options[self::OSS_CONTENT_TYPE] = 'application/octet-stream';
				}
			}
			else
			{
				$request->set_read_file($options['fileUpload']);

				// Determine the length to read from the file
				$length = $request->read_stream_size; // The file size by default

				if (isset($options[self::OSS_CONTENT_LENGTH]))
				{
					$length = $options[self::OSS_CONTENT_LENGTH];
				}
				elseif (isset($options['seekTo']) && isset($length))
				{
					// Read from seekTo until EOF by default
					$length -= (integer) $options['seekTo'];
				}

				$request->set_read_stream_size($length);

				// Attempt to guess the correct mime-type
				if (isset($options[self::OSS_CONTENT_TYPE]) && ($options[self::OSS_CONTENT_TYPE] === 'application/x-www-form-urlencoded'))
				{
					$extension = explode('.', $options['fileUpload']);
					$extension = array_pop($extension);
					$mime_type = OSSMimeTypes::get_mimetype($extension);
					$options[self::OSS_CONTENT_TYPE] = $mime_type;
				}
			}

			//Need Redefine
			//$options[self::OSS_HEADERS] = array(self::OSS_CONTENT_LENGTH => $request->read_stream_size);
			$options[self::OSS_CONTENT_MD5] = '';

			$read = fread($request->read_stream, min($request->read_stream_size - $request->read_stream_read, $length));
			$out = $read === false ? '':$read;	
			$options[self::OSS_CONTENT] =  $out;	
		}

		// Handle streaming file offsets
		if (isset($options['seekTo']))
		{
			// Pass the seek position to RequestCore
			$request->set_seek_position((integer) $options['seekTo']);
		}		
		
		$headers = array (
			self::OSS_CONTENT_MD5 => (isset($options[self::OSS_CONTENT_MD5]) && !empty($options[self::OSS_CONTENT_MD5]))?$options[self::OSS_CONTENT_MD5]:'',
			self::OSS_CONTENT_TYPE => (isset($options[self::OSS_CONTENT_TYPE]) && !empty($options[self::OSS_CONTENT_TYPE]) )?$options[self::OSS_CONTENT_TYPE]:'application/x-www-form-urlencoded',
			self::OSS_DATE => gmdate('D, d M Y H:i:s \G\M\T'),
			self::OSS_HOST => self::DEFAULT_OSS_HOST,
		);

		//合并 HTTP headers
		if (isset ( $options [self::OSS_HEADERS] )) {
			$headers = array_merge ( $headers, $options [self::OSS_HEADERS] );
		}

		//构造resource串
		$resource = $this->make_resource($options);
		
		//获取签名
		$sign = $this->create_sign_for_nomal_auth($options[self::OSS_METHOD], $headers,$resource);

		//设置签名
		$headers[self::OSS_AUTHORIZATION] = $sign;

		//设置请求方法
		if (isset ( $options [self::OSS_METHOD] )) {
			$request->set_method ( $options [self::OSS_METHOD] );
		}

		//设置Http-Body
		if (isset ( $options [self::OSS_CONTENT] )) {
			$request->set_body ( $options [self::OSS_CONTENT] );
		}

		//增加Http-Header
		foreach ( $headers as $header_key => $header_value ) {
			$header_value = str_replace ( array ("\r", "\n" ), '', $header_value );
			if ($header_value !== '') {
				$request->add_header ( $header_key, $header_value );
			}
		}

		// Debug mode
		if ($this->debug_mode){
			$request->debug_mode = $this->debug_mode;
		}
		
		//发送请求
		$request->send_request();

		//返回ResponseCore
		return new ResponseCore ( $request->get_response_header (), $request->get_response_body (), $request->get_response_code () );
	}


	/*%******************************************************************************************************%*/
	//关于Services的操作

	/**
	 *
	 * 获得用户的bucket列表
	 * @param array $options (Optional)
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function list_bucket($options = NULL) {
		//$options
		$this->validate_options($options);

		if (! $options) {
			$options = array ();
		}

		$options[self::OSS_BUCKET] = '';
		$options[self::OSS_METHOD] = 'GET';
		$options[self::OSS_OBJECT] = '/';
		$response = $this->auth ( $options );
		$this->log ( ($response->isOK () ? OSS_GET_BUCKET_LIST_SUCCESS : OSS_GET_BUCKET_LIST_FAILED)." Response: [" . $response->body . "]", $options );
		return $response;
	}


	/*%******************************************************************************************************%*/
	//Bucket相关操作

	/**
	 * 创建Bucket
	 * @param string $bucket (Required)
	 * @param string $acl (Optional)
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function create_bucket($bucket,$acl = self::OSS_ACL_TYPE_PRIVATE, $options = NULL){
		//$options
		$this->validate_options($options);

		if (! $options) {
			$options = array ();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'PUT';
		$options[self::OSS_OBJECT] = '/';
		$options[self::OSS_HEADERS] = array(self::OSS_ACL => $acl);
		$response = $this->auth ( $options );
		$this->log ( ($response->isOK () ? OSS_CREATE_BUCKET_SUCCESS : OSS_CREATE_BUCKET_FAILED)." Response: [" . $response->body . "]", $options );
		return $response;
	}

	/**
	 * 删除Bucket
	 * @param string $bucket (Required)
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function delete_bucket($bucket,$options = NULL){
		//$options
		$this->validate_options($options);

		if (! $options) {
			$options = array ();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'DELETE';
		$options[self::OSS_OBJECT] = '/';
		$response = $this->auth ( $options );
		$this->log ( ($response->isOK () ? OSS_DELETE_BUCKET_SUCCESS : OSS_DELETE_BUCKET_FAILED)." Response: [" . $response->body . "]", $options );
		return $response;
	}

	/**
	 * 获取Bucket的ACL
	 * @param string $bucket (Required)
	 * @param array $options (Optional)
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function get_bucket_acl($bucket ,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'GET';
		$options[self::OSS_OBJECT] = '/';
		$options[self::OSS_ACL] = TRUE;
		$response = $this->auth ( $options );
		$this->log ( ($response->isOK () ? OSS_GET_BUCKET_ACL_SUCCESS : OSS_GET_BUCKET_ACL_FAILED)." Response: [" . $response->body . "]", $options );
		return $response;
	}

	/**
	 * 设置bucket的ACL
	 * @param string $bucket (Required)
	 * @param string $acl  (Required)
	 * @param array $options (Optional)
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function set_bucket_acl($bucket ,$acl , $options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'PUT';
		$options[self::OSS_OBJECT] = '/';
		//$options[self::OSS_ACL] = TRUE;
		$options[self::OSS_HEADERS] = array(self::OSS_ACL => $acl);
		$response = $this->auth ( $options );
		$this->log ( ($response->isOK () ? OSS_SET_BUCKET_ACL_SUCCESS : OSS_SET_BUCKET_ACL_FAILED)." Response: [" . $response->body . "]", $options );
		return $response;
	}


	/*%******************************************************************************************************%*/
	//Object相关操作

	/**
	 * 获得Bucket下Object列表
	 * @param string $bucket (Required)
	 * @param array $options (Optional)
	 * 其中options中的参数如下
	 * $options = array(
	 * 		'max-keys' 	=> max-keys用于限定此次返回object的最大数，如果不设定，默认为100，max-keys取值不能大于100。
	 * 		'prefix'	=> 限定返回的object key必须以prefix作为前缀。注意使用prefix查询时，返回的key中仍会包含prefix。
	 * 		'delimiter' => 是一个用于对Object名字进行分组的字符。所有名字包含指定的前缀且第一次出现delimiter字符之间的object作为一组元素
	 * 		'marker'	=> 用户设定结果从marker之后按字母排序的第一个开始返回。
	 * )
	 * 其中 prefix，marker用来实现分页显示效果，参数的长度必须小于256字节。
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function list_object($bucket,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'GET';
		$options[self::OSS_OBJECT] = '/';
		$options[self::OSS_HEADERS] = array(
		self::OSS_DELIMITER => isset($options[self::OSS_DELIMITER])?$options[self::OSS_DELIMITER]:'/',
		self::OSS_PREFIX => isset($options[self::OSS_PREFIX])?$options[self::OSS_PREFIX]:'',
		self::OSS_MAX_KEYS => isset($options[self::OSS_MAX_KEYS])?$options[self::OSS_MAX_KEYS]:self::OSS_MAX_KEYS_VALUE,
		self::OSS_MARKER => isset($options[self::OSS_MARKER])?$options[self::OSS_MARKER]:'',
		);
				
		$response = $this->auth ( $options );
		$this->log( ($response->isOK () ? OSS_GET_OBJECT_LIST_SUCCESS : OSS_GET_OBJECT_LIST_FAILED)." Response: [" . $response->body . "]", $options );
		return $response;

	}

	/**
	 * 创建目录(目录和文件的区别在于，目录最后增加'/')
	 * @param string $bucket
	 * @param string $object
	 * @param array $options
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function create_object_dir($bucket,$object,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'PUT';
		$options[self::OSS_OBJECT] = $object.'/';   //虚拟目录需要以'/结尾'
		$options[self::OSS_CONTENT_LENGTH] = array(self::OSS_CONTENT_LENGTH => 0);

		$response = $this->auth ( $options );

		$this->log(($response->isOK () ? OSS_CREATE_OBJECT_DIR_SUCCESS : OSS_CREATE_OBJECT_DIR_FAILED)." Response: [" . $response->body . "]", $options );
		return $response;
	}

	/**
	 * 通过在http body中添加内容来上传文件，适合比较小的文件
	 * 根据api约定，需要在http header中增加content-length字段
	 * @param string $bucket (Required)
	 * @param string $object (Required)
	 * @param string $content (Required)
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function upload_file_by_content($bucket,$object,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		//内容校验
		$this->validate_content($options);

		//长度校验
		$this->validate_content_length($options);

		$filename = '';
		//文件名校验
		if(isset($options[self::OSS_BASENAME])){
			$filename = $options[self::OSS_BASENAME];
		}else{
			//从object中获得basename
			$filename = $object;
		}
			
		$objArr = explode('/', $object);
		$basename = array_pop($objArr);
		$extension = explode ( '.', $basename );
		$extension = array_pop ( $extension );
		$content_type = MimeTypes::get_mimetype(strtolower($extension));

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'PUT';
		$options[self::OSS_OBJECT] = $object;
		$options[self::OSS_CONTENT_LENGTH] = array(self::OSS_CONTENT_LENGTH => $options[self::OSS_LENGTH]);
		if(isset($content_type) && !empty($content_type)){
			$options[self::OSS_CONTENT_TYPE] = $content_type;
		}

		$response = $this->auth ( $options );
		$this->log(($response->isOK () ? OSS_UPLOAD_FILE_BY_CONTENT_SUCCESS : OSS_UPLOAD_FILE_BY_CONTENT_FAILED)." Response: [" . $response->body . "]", $options );
		return $response;
	}

	/**
	 * 上传文件，适合比较大的文件
	 * @param string $bucket (Required)
	 * @param string $object (Required)
	 * @param string $file (Required)
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2012-02-28
	 * @return ResponseCore
	 */
	public function upload_file_by_file($bucket,$object,$file,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options['fileUpload'] = $file;
		
		if(!file_exists($options['fileUpload'])){
			die($options['fileUpload'].OSS_FILE_NOT_EXIST);
		}
		
		$filesize = filesize($options['fileUpload']);
		$partsize = 1024 * 1024 ; //默认为 1M
		
		
		$extension = explode ( '.', $file );
		$extension = array_pop ( $extension );
		$content_type = MimeTypes::get_mimetype(strtolower($extension));
				
		$options[self::OSS_METHOD] = self::OSS_HTTP_PUT;
		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_OBJECT] = $object;
		$options[self::OSS_CONTENT_TYPE] = $content_type;
		$options[self::OSS_CONTENT_LENGTH] = $filesize;
				
		//return $file_part;
		$response = $this->auth($options);
		return $response;
	}
	
	
	/**
	 * 拷贝Object
	 * @param string $bucket (Required)
	 * @param string $from_object (Required)
	 * @param string $to_object (Required)
	 * @param string $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-21
	 * @return ResponseCore
	 */
	public function copy_object($from_bucket,$from_object,$to_bucket,$to_object,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//from bucket
		$this->is_empty($from_bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//to bucket
		$this->is_empty($to_bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//from object
		$this->is_empty($from_object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		//to object
		$this->is_empty($to_object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $from_bucket;
		$options[self::OSS_METHOD] = 'PUT';
		$options[self::OSS_OBJECT] = $to_object;
		$options[self::OSS_HEADERS] = array(self::OSS_OBJECT_COPY_SOURCE => '/'.$from_bucket.'/'.$from_object);

		$response = $this->auth ( $options );

		$this->log(($response->isOK () ? OSS_COPY_OBJECT_SUCCESS : OSS_COPY_OBJECT_FAILED)." Response: [" . $response->body . "]", $options );
		return $response;
	}

	/**
	 * 获得object的meta信息
	 * @param string $bucket (Required)
	 * @param string $object (Required)
	 * @param string $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function get_object_meta($bucket,$object,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'HEAD';
		$options[self::OSS_OBJECT] = $object;

		$response = $this->auth ( $options );
		$this->log( ($response->isOK () ? OSS_GET_OBJECT_META_SUCCESS : OSS_GET_OBJECT_META_FAILED)." Response: [" . $response->body . "]", $options );
		return $response;
	}

	/**
	 * 删除object
	 * @param string $bucket(Required)
	 * @param string $object (Required)
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function delete_object($bucket,$object,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'DELETE';
		$options[self::OSS_OBJECT] = $object;

		$response = $this->auth ( $options );
		$this->log( ($response->isOK () ? OSS_DELETE_OBJECT_SUCCESS : OSS_DELETE_OBJECT_FAILED)." Response: [" . $response->body . "]", $options );
		return $response;
	}

	/**
	 * 获得Object内容
	 * @param string $bucket(Required)
	 * @param string $object (Required)
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function get_object($bucket,$object,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'GET';
		$options[self::OSS_OBJECT] = $object;

		$response = $this->auth ( $options );
		$this->log( ($response->isOK () ? OSS_GET_OBJECT_SUCCESS : OSS_GET_OBJECT_FAILED)." Response: [" . $response->body . "]", $options );
		return $response;
	}

	/**
	 * 检测Object是否存在
	 * @param string $bucket(Required)
	 * @param string $object (Required)
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return boolean
	 */
	public function is_object_exist($bucket,$object,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'GET';
		$options[self::OSS_OBJECT] = $object;

		$response = $this->get_object_meta($bucket, $object,$options);
		$this->log ( ($response->isOK () ? OSS_OBJECT_EXIST : OSS_OBJECT_NOT_EXIST)." Response: [" . $response->body . "]", $options );

		if($response->isOK()){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 获得Object url
	 * @param string $bucket(Required)
	 * @param string $object (Required)
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return string
	 */
	public function get_object_url($bucket,$object,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = self::OSS_HTTP_GET;
		$options[self::OSS_OBJECT] = $object;

		$response = $this->get_object_meta($bucket, $object,$options);
		$this->log ( ($response->isOK () ? OSS_OBJECT_EXIST : OSS_OBJECT_NOT_EXIST)." Response: [" . $response->body . "]", $options );

		if($response->isOK()){
			return $this->make_url($options);
		}else{
			return false;
		}
	}

	/*%******************************************************************************************************%*/
	//Multi Part相关操作	
	
	/**
	 * 计算文件可以分成多少个part，以及每个part的长度以及起始位置
	 * 方法必须在 <upload_part()>中调用
	 *
	 * @param integer $filesize (Required) 文件大小
	 * @param integer $part_size (Required) part大小,默认5M
	 * @return array An array 包含 key-value 键值对. Key 为 `seekTo` 和 `length`.
	 */	
	public function get_multipart_counts($filesize, $part_size = 5242880 ){
		$i = 0;
		$sizecount = $filesize;
		$values = array();

		if((integer)$part_size <= 5242880){ 
			$part_size = 5242880;	//5M
		}elseif ((integer)$part_size > 524288000){
			$part_size = 524288000; //500M
		}else{
			$part_size = 52428800; //50M
		}		
		
		while ($sizecount > 0)
		{
			$sizecount -= $part_size;
			$values[] = array(
				'seekTo' => ($part_size * $i),
				'length' => (($sizecount > 0) ? $part_size : ($sizecount + $part_size)),
			);
			$i++;
		}

		return $values;		
	}
	
	/**
	 * 初始化multi-part upload，并且返回uploadId
	 * @param string $bucket (Required) Bucket名称
	 * @param string $object (Required) Object名称
	 * @param array $options (Optional) Key-Value数组，其中可以包括以下的key
	 * @return ResponseCore
	 */
	public function initiate_multipart_upload($bucket,$object,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		// 发送请求
		$options[self::OSS_METHOD] = self::OSS_HTTP_POST;
		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_OBJECT] = $object;
		$options[self::OSS_MULTI_PART] = 'uploads';
		$options[self::OSS_CONTENT] = '';
		//$options[self::OSS_CONTENT_LENGTH] = 0;
		$options[self::OSS_HEADERS] = array(self::OSS_CONTENT_TYPE => 'application/octet-stream');

		$response = $this->auth ( $options );
		$this->log( ($response->isOK () ? OSS_INITIATE_MULTI_PART_SUCCESS : OSS_INITIATE_MULTI_PART_FAILED)." Response: [" . $response->body . "]", $options );
		return $response;		
	}
	
	/**
	 * 上传part
	 * @param string $bucket (Required) Bucket名称
	 * @param string $object (Required) Object名称
	 * @param string $upload_id (Required) uploadId
	 * @param array $options (Optional) Key-Value数组，其中可以包括以下的key
	 * @return ResponseCore
	 */
	public function upload_part($bucket, $object, $upload_id, $options = null){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		if (!isset($options['fileUpload']) || !isset($options['partNumber'])){
			throw new OSS_Exception('The `fileUpload` and `partNumber` options are both required in ' . __FUNCTION__ . '().');
		}
				
		$options[self::OSS_METHOD] = self::OSS_HTTP_PUT;
		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_OBJECT] = $object;
		$options[self::OSS_UPLOAD_ID] = $upload_id;	
		
		if(isset($options['length'])){
			$options[self::OSS_CONTENT_LENGTH] =  $options['length'];
		}

		return $this->auth($options);
	}
	
	/**
	 * list part
	 * @param string $bucket (Required) Bucket名称
	 * @param string $object (Required) Object名称
	 * @param string $upload_id (Required) uploadId
	 * @param array $options (Optional) Key-Value数组，其中可以包括以下的key
	 * @return ResponseCore
	 */	
	public function list_parts($bucket, $object, $upload_id, $options = null){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_METHOD] = self::OSS_HTTP_GET;
		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_OBJECT] = $object;
		$options[self::OSS_UPLOAD_ID] = $upload_id;
		$options['query_string'] = array();

		foreach (array('max-parts', 'part-number-marker') as $param){
			if (isset($options[$param])){
				$options['query_string'][$param] = $options[$param];
				unset($options[$param]);
			}
		}	

		return $this->auth($options);
	}
	
	/**
	 * 中止上传mulit-part upload
	 * @param string $bucket (Required) Bucket名称
	 * @param string $object (Required) Object名称
	 * @param string $upload_id (Required) uploadId
	 * @param array $options (Optional) Key-Value数组，其中可以包括以下的key
	 * @return ResponseCore
	 */	
	public function abort_multipart_upload($bucket, $object, $upload_id, $options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_METHOD] = self::OSS_HTTP_DELETE;
		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_OBJECT] = $object;
		$options[self::OSS_UPLOAD_ID] = $upload_id;
		
		return $this->auth($options);
	}
	
	/**
	 * 完成multi-part上传
	 * @param string $bucket (Required) Bucket名称
	 * @param string $object (Required) Object名称
	 * @param string $upload_id (Required) uploadId
	 * @param string $parts xml格式文件
	 * @param array $options (Optional) Key-Value数组，其中可以包括以下的key
	 * @return ResponseCore
	 */	
	public function complete_multipart_upload($bucket, $object, $upload_id, $parts, $options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);
		
		$options[self::OSS_METHOD] = self::OSS_HTTP_POST;
		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_OBJECT] = $object;
		//$options[self::OSS_MULTI_PART] = 'uploads';
		$options[self::OSS_UPLOAD_ID] = $upload_id;
		$options[self::OSS_CONTENT_TYPE] = 'application/xml';

		
		if(is_string($parts)){
			$options[self::OSS_CONTENT] = $parts;
		}else if($parts instanceof SimpleXMLElement){
			$options[self::OSS_CONTENT] = $parts->asXML();
		}else if((is_array($parts) || $parts instanceof ResponseCore)){
			$xml = simplexml_load_string('<CompleteMultipartUpload></CompleteMultipartUpload>');

			if (is_array($parts)){
				//生成关联的xml
				foreach ($parts as $node){
					$part = $xml->addChild('Part');
					$part->addChild('PartNumber', $node['PartNumber']);
					$part->addChild('ETag', $node['ETag']);
				}
			}elseif ($parts instanceof ResponseCore){
				foreach ($parts->body->Part as $node){
					$part = $xml->addChild('Part');
					$part->addChild('PartNumber', (string) $node->PartNumber);
					$part->addChild('ETag', (string) $node->ETag);
				}
			}

			$options[self::OSS_CONTENT] = $xml->asXML();			
		}
		
		if(isset($options[self::OSS_CONTENT])){
			$options[self::OSS_CONTENT] = str_replace('<?xml version="1.0"?>', '',$options[self::OSS_CONTENT]);
		}
	
		return $this->auth($options);		
	}
	
	/**
	 * 列出multipart上传
	 * @param string $bucket (Requeired) bucket 
	 * @param array $options (Optional) 关联数组
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2012-03-05
	 * @return ResponseCore
	 */
	public function list_multipart_uploads($bucket, $options = null){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_METHOD] = self::OSS_HTTP_GET;
		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_OBJECT] = '/';
		$options[self::OSS_MULTI_PART] = 'uploads';

		foreach (array('key-marker', 'max-uploads', 'upload-id-marker') as $param){
			if (isset($options[$param])){
				$options['query_string'][$param] = $options[$param];
				unset($options[$param]);
			}
		}
				
		return $this->auth($options);
	}
	
	/**
	 * multipart上传统一封装，从初始化到完成multipart，以及出错后中止动作
	 * @param unknown_type $bucket
	 * @param unknown_type $object
	 * @param unknown_type $options
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2012-03-05
	 * @return ResponseCore 
	 */
	public function create_mpu_object($bucket, $object, $options = null){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		if(isset($options['length'])){
			$options[self::OSS_CONTENT_LENGTH] = $options['length'];
			unset($options['length']);
		}
		
		if(!isset($options['fileUpload'])){
			die('The `fileUpload` option is required in ' . __FUNCTION__ . '().');
		}elseif (is_resource($options['fileUpload'])){
			$upload_position = isset($options['seekTo']) ? (integer) $options['seekTo'] : ftell($options['fileUpload']);
			$upload_filesize = isset($options[self::OSS_CONTENT_TYPE]) ? (integer) $options[self::OSS_CONTENT_TYPE] : null;

			if (!isset($upload_filesize) && $upload_position !== false){
				$stats = fstat($options['fileUpload']);

				if ($stats && $stats['size'] >= 0){
					$upload_filesize = $stats['size'] - $upload_position;
				}
			}			
		}else{
			$upload_position = isset($options['seekTo']) ? (integer) $options['seekTo'] : 0;

			if (isset($options[self::OSS_CONTENT_TYPE])){
				$upload_filesize = (integer) $options[self::OSS_CONTENT_TYPE];
			}
			else{
				$upload_filesize = filesize($options['fileUpload']);

				if ($upload_filesize !== false){
					$upload_filesize -= $upload_position;
				}
			}			
		}
		
		if ($upload_position === false || !isset($upload_filesize) || $upload_filesize === false || $upload_filesize < 0){
			die('The size of `fileUpload` cannot be determined in ' . __FUNCTION__ . '().');
		}
		
		// 处理partSize
		if (isset($options['partSize'])){
			// 小于5M
			if ((integer) $options['partSize'] <= 5242880){
				$options['partSize'] = 5242880; // 5 MB
			}
			// 大于500M
			elseif ((integer) $options['partSize'] > 524288000){
				$options['partSize'] = 524288000; // 500 MB
			}
		}
		else{
			$options['partSize'] = 52428800; // 50 MB
		}

		// 如果上传的文件小于partSize,则直接使用普通方式上传
		if ($upload_filesize < $options['partSize'] && !isset($options['uploadId'])){
			return $this->upload_file_by_file($bucket, $object, $options['fileUpload']);
		}	

		// 初始化multipart
		if (isset($opt['uploadId'])){
			$upload_id = $opt['uploadId'];
		}else{
			//初始化
			$upload = $this->initiate_multipart_upload($bucket, $object);
			
			if (!$upload->isOK()){
				die('Init multi-part upload failed...');
			}
			$xml = new SimpleXmlIterator($upload->body);
			$uploadId = (string)$xml->UploadId;
		}		

		// 或的分片
		$pieces = $this->get_multipart_counts($upload_filesize, (integer) $options['partSize']);

		$response_upload_part = array();
		foreach ($pieces as $i => $piece){
			$response_upload_part[] = $this->upload_part($bucket, $object, $uploadId, array(
				//'expect' => '100-continue',
				'fileUpload' => $options['fileUpload'],
				'partNumber' => ($i + 1),
				'seekTo' => $upload_position + (integer) $piece['seekTo'],
				'length' => (integer) $piece['length'],
			));
		}
		
		$upload_parts = array();
		$upload_part_result = true;
		
		foreach ($response_upload_part as $i=>$response){
			$upload_part_result = $upload_part_result && $response->isOk();
		}
		
		if(!$upload_part_result){
			die('any part upload failed...,pls try again');
		}
		
		foreach ($response_upload_part as $i=>$response){
			$upload_parts[] = array(
				'PartNumber' => ($i + 1),
			    'ETag' => (string) $response->header['etag']
			);		
		}
				
		return $this->complete_multipart_upload($bucket, $object, $uploadId, $upload_parts);
	}
	
	
	/**
	 * 通过Multi-Part方式上传整个目录，其中的object默认为文件名
	 * @param string $bucket (Required) 
	 * @param string $dir  (Required) 必选
	 * @param boolean $recursive (Optional) 是否递归，如果为true，则递归读取所有目录，默认为不递归读取
	 * @param string $exclude 需要过滤的文件
	 * @param array $options (Optional) 关联数组
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2012-03-05
	 * @return ResponseCore 
	 */
	public function create_mtu_object_by_dir($bucket,$dir,$recursive = false,$exclude = ".|..|.svn",$options = null){
		//options
		$this->validate_options($options);

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//判断是否目录
		if(!is_dir($dir)){
			die($dir.' is not a directory...,pls check it');
		}
		
		$file_list_array = $this->read_dir($dir,$exclude,$recursive);
				
		if(!$file_list_array){
			die($dir.' is empty...');
		}
		
		$index = 1;
		
		foreach ($file_list_array as $item){
			$options = array(
				'fileUpload' => $item['path'],
				'partSize' => 5242880,
			);
			echo $index++.". ";
			$response = $this->create_mpu_object($bucket, $item['file'],$options);
			if($response->isOK()){
				echo "Upload file {".$item['path']." } successful..\n";
			}else{
				echo "Upload file {".$item['path']." } failed..\n";
			}
		}
	}
	
	
	
	
	/*%******************************************************************************************************%*/
	//Object Group相关操作

	/**
	 * 创建Object Group
	 * @param string $object_group (Required)  Object Group名称
	 * @param string $bucket (Required) Bucket名称
	 * @param array $object_arry (Required) object数组，所有的object必须在同一个bucket下
	 * 其中$object 数组的格式如下:
	 * $object = array(
	 * 		$object1,
	 * 		$object2,
	 * 		...
	 * )
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function create_object_group($bucket,$object_group  ,$object_arry,$options = NULL){
		//options
		$this->validate_options($options);

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object group
		$this->is_empty($object_group,OSS_OBJECT_GROUP_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'POST';
		$options[self::OSS_OBJECT] = $object_group;
		$options[self::OSS_CONTENT_TYPE] = 'txt/xml';  //重设Content-Type
		$options[self::OSS_OBJECT_GROUP] = true;	   //设置?group
		$options[self::OSS_CONTENT] = $this->make_object_group_xml($bucket,$object_arry);   //格式化xml

		$response = $this->auth ( $options );
		$this->log( ($response->isOK () ? OSS_CREATE_OBJECT_GROUP_SUCCESS : OSS_CREATE_OBJECT_GROUP_FAILED)." Response: [" . $response->body . "]", $options );
		return $response;
	}

	/**
	 * 获取Object Group
	 * @param string $object_group (Required)
	 * @param string $bucket	(Required)
	 * @param array $options	(Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function get_object_group($bucket,$object_group,$options = NULL){
		//options
		$this->validate_options($options);

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object group
		$this->is_empty($object_group,OSS_OBJECT_GROUP_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'GET';
		$options[self::OSS_OBJECT] = $object_group;
		//$options[self::OSS_OBJECT_GROUP] = true;	   //设置?group
		//$options[self::OSS_CONTENT_TYPE] = 'txt/xml';  //重设Content-Type
		$options[self::OSS_HEADERS] = array(self::OSS_OBJECT_GROUP => self::OSS_OBJECT_GROUP);  //header中的x-oss-file-group不能为空，否则返回值错误

		$response = $this->auth ( $options );
		$this->log( ($response->isOK () ? OSS_GET_OBJECT_GROUP_SUCCESS : OSS_GET_OBJECT_GROUP_FAILED)." Response: [" . $response->body . "]", $options );
		return $response;
	}

	/**
	 * 获取Object Group 的Object List信息
	 * @param string $object_group (Required)
	 * @param string $bucket	(Required)
	 * @param array $options	(Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function get_object_group_index($bucket,$object_group,$options = NULL){
		//options
		$this->validate_options($options);

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object group
		$this->is_empty($object_group,OSS_OBJECT_GROUP_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'GET';
		$options[self::OSS_OBJECT] = $object_group;
		$options[self::OSS_CONTENT_TYPE] = 'application/xml';  //重设Content-Type
		//$options[self::OSS_OBJECT_GROUP] = true;	   //设置?group
		$options[self::OSS_HEADERS] = array(self::OSS_OBJECT_GROUP => self::OSS_OBJECT_GROUP);

		$response = $this->auth ( $options );
		$this->log( ($response->isOK () ? OSS_GET_OBJECT_GROUP_INDEX_SUCCESS : OSS_GET_OBJECT_GROUP_INDEX_FAILED)." Response: [" . $response->body . "]", $options );
		return $response;
	}

	/**
	 * 获得object group的meta信息
	 * @param string $bucket (Required)
	 * @param string $object_group (Required)
	 * @param string $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function get_object_group_meta($bucket,$object_group,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object group
		$this->is_empty($object_group,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'HEAD';
		$options[self::OSS_OBJECT] = $object_group;
		$options[self::OSS_CONTENT_TYPE] = 'application/xml';  //重设Content-Type
		//$options[self::OSS_OBJECT_GROUP] = true;	   //设置?group
		$options[self::OSS_HEADERS] = array(self::OSS_OBJECT_GROUP => self::OSS_OBJECT_GROUP);

		$response = $this->auth ( $options );
		$this->log( ($response->isOK () ? OSS_GET_OBJECT_GROUP_META_SUCCESS : OSS_GET_OBJECT_GROUP_META_FAILED)." Response: [" . $response->body . "]", $options );
		return $response;
	}

	/**
	 * 删除Object Group
	 * @param string $bucket(Required)
	 * @param string $object_group (Required)
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function delete_object_group($bucket,$object_group,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object group
		$this->is_empty($object_group,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'DELETE';
		$options[self::OSS_OBJECT] = $object_group;

		$response = $this->auth ( $options );
		$this->log( ($response->isOK () ? OSS_DELETE_OBJECT_GROUP_SUCCESS : OSS_DELETE_GROUP_OBJECT_FAILED)." Response: [" . $response->body . "]", $options );
		return $response;
	}


	/*%******************************************************************************************************%*/
	//带签名的url相关

	/**
	 * 获取带签名的url
	 * @param string $bucket (Required)
	 * @param string $object (Required)
	 * @param int	 $timeout (Optional)
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-21
	 * @return string
	 */
	public function get_sign_url($bucket,$object,$timeout = 60,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_OBJECT] = $object;
		$options[self::OSS_METHOD] = self::OSS_HTTP_GET;

		$timeout = time() + $timeout;
		$headers = array (
		self::OSS_CONTENT_MD5 => '',
		self::OSS_CONTENT_TYPE => '',
		self::OSS_DATE => $timeout,
		self::OSS_HOST => self::DEFAULT_OSS_HOST,
		);

		$url = $this->make_url($options);

		$resource = $this->make_resource($options);

		$params = array();
		$params[] = self::OSS_URL_ACCESS_KEY_ID.'='.$this->access_id;
		$params[] = self::OSS_URL_EXPIRES.'='.$timeout;
		$params[] = self::OSS_URL_SIGNATURE.'='.urlencode($this->get_assign($this->access_key,self::OSS_HTTP_GET,$headers,$resource));

		return $url.'?'.implode('&', $params);
	}

	/*%******************************************************************************************************%*/
	//日志相关

	/**
	 * 记录日志
	 * @param string $msg (Required)
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-27
	 * @return void
	 */
	private function log($msg){
		return true;
		$log_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR;
		//检测日志目录是否存在
		try{
			if(!file_exists($log_path)){
				throw new OSS_Exception(OSS_LOG_PATH_NOT_EXIST);
			}
		}catch (OSS_Exception $e){
			die($e->getMessage());
		}

		$log_name = $log_path.'oss_sdk_php_'.date('Y-m-d').'.log';
		if(DEBUG){//输出日志信息
			echo "Debug Info :".date('Y-m-d H:i:s')." : ".$msg."\n";
		}

		try{
			if(!error_log("\nLog Info :".date('Y-m-d H:i:s')." : ".$msg."\n", 3,$log_name)){
				throw new OSS_Exception(OSS_WRITE_LOG_TO_FILE_FAILED);
			}
		}catch (OSS_Exception $e){
			die($e->getMessage());
		}
	}


	/*%******************************************************************************************************%*/
	//工具类相关

	/*
	 * 构造URL
	 * @param array $options (Optional)
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-27
	 * @return string
	 */
	private function make_url($options) {

		$url = "";
		$url .= $this->use_ssl ? 'https://' : 'http://';
		$url .= $this->hostname;
		$url .= '/' . $options [self::OSS_BUCKET];
		if (isset ( $options [self::OSS_OBJECT] ) && '/' !== $options [self::OSS_OBJECT]) {
			$url .= "/" . str_replace('%2F','/',rawurlencode ( $options [self::OSS_OBJECT] ));
		}

		//Acl
		if(isset($options[self::OSS_ACL]) && $options[self::OSS_ACL]){
			$url .= '?acl';
		}

		//Group
		if(isset($options[self::OSS_OBJECT_GROUP]) && $options[self::OSS_OBJECT_GROUP]){
			$url .= '?group';
		}
		
		//Multi Part
		if(isset($options[self::OSS_MULTI_PART]) && $options[self::OSS_MULTI_PART]){
			$url .='?uploads';
		}
		
		$query_string = $this->to_query_string($options);
		
		if(!empty($query_string)){
			if(strpos($url,'?') === false){
				$url .= '?'.$query_string;
			}else{
				$url .= '&'.$query_string;
			}
		}
		
		return $url;
	}

	/**
	 * 返回资源串，用户计算签名
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-27
	 * @return string
	 */
	private function make_resource($options){
		$resource = '';
		if(isset($options[self::OSS_BUCKET])){
			$resource .= '/'.$options[self::OSS_BUCKET];
		}

		if (isset ( $options [self::OSS_OBJECT] ) && '/' !== $options [self::OSS_OBJECT]) {
			$resource .= "/" . str_replace('%2F','/',rawurlencode ( $options [self::OSS_OBJECT] ));
		}

		//Acl
		if(isset($options[self::OSS_ACL])){
			$resource .= '?acl';
		}

		//Group
		if(isset($options[self::OSS_OBJECT_GROUP])){
			$resource .= '?group';
		}
		
		//Multi Part
		if(isset($options[self::OSS_MULTI_PART])){
			$resource .= '?uploads';
		}

		if(isset($options['query_string'])){
			unset($options['query_string']);
		}
		$query_string = $this->to_query_string($options);
		
		if(!empty($query_string)){
			if(strpos($resource,'?') === false){
				$resource .= '?'.$query_string;
			}else{
				$resource .= '&'.$query_string;
			}
		}		
		return $resource;
	}

	/**
	 * 生成query params
	 * @param array $array 关联数组
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2012-03-04
	 * @return string 返回诸如 key1=value1&key2=value2
	 */
	public function to_query_string($options = array()){
		$temp = array();

		$signable_querystringparams = array();
		$querystringparams = array();
		
		$signable_list = array(
			'partNumber',
			'uploadId',			
		);
		
		foreach ($signable_list as $item){
			if(isset($options[$item])){
				$signable_querystringparams[$item] = $options[$item];
			}
		}		
		
		if(isset($options['query_string'])){
			$querystringparams = array_merge($querystringparams,$options['query_string']);
		}
		
		$array = array_merge($querystringparams,$signable_querystringparams);
		
		foreach ($array as $key => $value){
			if (is_string($key) && !is_array($value)){
				$temp[] = rawurlencode($key) . '=' . rawurlencode($value);
			}
		}

		return implode('&', $temp);
	}
	
	
	/**
	 * 读取目录
	 * @param string $dir (Required) 目录名
	 * @param boolean $recursive (Optional) 是否递归，默认为false
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2012-03-05
	 * @return array
	 */
	private  function read_dir($dir,$exclude = ".|..|.svn",$recursive = false){
		static $file_list_array = array();
		
		$exclude_array = explode("|", $exclude);
		//读取目录
		if($handle = opendir($dir)){
			while ( false !== ($file = readdir($handle))){
				if(!in_array(strtolower($file),$exclude_array)){
					$new_file = $dir.'/'.$file;
					if(is_dir($new_file) && $recursive){
						$this->read_dir($new_file,$exclude,$recursive);
					}else{
						$file_list_array[] = array(
							'path' => $new_file,
							'file' => $file,
						);
					}
				}
			}
			
			closedir($handle);			
		}			
		
		return $file_list_array;
	}
	
	
	/**
	 * 转化object数组为固定个xml格式
	 * @param string $bucket (Required)
	 * @param array $object_array (Required)
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-27
	 * @return string
	 */
	private function make_object_group_xml($bucket,$object_array){
		$xml = '';
		$xml .= '<CreateFileGroup>';

		try{
			if($object_array){
				if(count($object_array) > self::OSS_MAX_OBJECT_GROUP_VALUE){
					throw new OSS_Exception(OSS_OBJECT_GROUP_TOO_MANY_OBJECT, '-401');
				}
				$index = 1;
				foreach ($object_array as $key=>$value){
					$object_meta = (array)$this->get_object_meta($bucket, $value);
					if(isset($object_meta) && isset($object_meta['status']) && isset($object_meta['header']) && isset($object_meta['header']['etag']) && $object_meta['status'] == 200){
						$xml .= '<Part>';
						$xml .= '<PartNumber>'.intval($index).'</PartNumber>';
						$xml .= '<PartName>'.$value.'</PartName>';
						$xml .= '<ETag>'.$object_meta['header']['etag'].'</ETag>';
						$xml .= '</Part>';

						$index++;
					}
				}
			}else{
				throw new OSS_Exception(OSS_OBJECT_ARRAY_IS_EMPTY, '-400');
			}
		}catch (OSS_Exception $e){
			die($e->getMessage());
		}

		$xml .= '</CreateFileGroup>';

		return $xml;
	}

	/**
	 * 检验bucket名称是否合法
	 * bucket的命名规范：
	 * 1. 只能包括小写字母，数字，下划线（_）和短横线（-）
	 * 2. 必须以小写字母或者数字开头
	 * 3. 长度必须在3-255字节之间
	 * @param string $bucket (Required)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-27
	 * @return boolean
	 */
	private function validate_bucket($bucket){
		$pattern = '/^[a-z0-9][a-z0-9_\\-]{2,254}$/';
		if (! preg_match ( $pattern, $bucket )) {
			return false;
		}
		return true;
	}

	/**
	 * 检验object名称是否合法
	 * object命名规范:
	 * 1. 规则长度必须在1-1023字节之间
	 * 2. 使用UTF-8编码
	 * @param string $object (Required)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-27
	 * @return boolean
	 */
	private function validate_object($object){
		$pattern = '/^.{1,1023}$/';
		if (empty ( $object ) || ! preg_match ( $pattern, $object )) {
			return false;
		}
		return true;
	}

	/**
	 * 检验$options
	 * @param array $options (Optional)
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-27
	 * @return boolean 
	 */
	private function validate_options($options){
		//$options
		try{
			if ($options != NULL && ! is_array ( $options )) {
				throw new OSS_Exception ($options.':'.OSS_OPTIONS_MUST_BE_ARRAY);
			}
		}catch (OSS_Exception $e){
			die($e->getMessage());
		}
	}

	/**
	 * 检测上传文件的内容
	 * @param array $options (Optional)
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since  2011-12-27
	 * @return string
	 */
	private function validate_content($options){
		try{
			if(isset($options[self::OSS_CONTENT])){
				if($options[self::OSS_CONTENT] == '' || !is_string($options[self::OSS_CONTENT])){
					throw new OSS_Exception(OSS_INVALID_HTTP_BODY_CONTENT,'-600');
				}
			}else{
				throw new OSS_Exception(OSS_NOT_SET_HTTP_CONTENT, '-601');
			}
		}catch(OSS_Exception $e){
			die($e->getMessage());
		}
	}

	/**
	 * 验证文件长度
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-27
	 * @return void
	 */
	private function validate_content_length($options){
		try{
			if(isset($options[self::OSS_LENGTH]) && is_numeric($options[self::OSS_LENGTH])){
				if( ! $options[self::OSS_LENGTH] > 0){
					throw new OSS_Exception(OSS_CONTENT_LENGTH_MUST_MORE_THAN_ZERO, '-602');
				}
			}else{
				throw new OSS_Exception(OSS_INVALID_CONTENT_LENGTH, '-602');
			}
		}catch(OSS_Exception $e){
			die($e->getMessage());
		}
	}

	/**
	 * 返回签名串
	 * @param string $access_key (Required)
	 * @param string $method (Required)
	 * @param array $headers (Required)
	 * @param string $resource (Required)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-27
	 * @return string
	 */
	public function get_assign($access_key,$method,$headers,$resource){
		$contentMD5 = $this->get_header_element(self::OSS_CONTENT_MD5, $headers);
		$contentType = $this->get_header_element(self::OSS_CONTENT_TYPE,$headers);
		$Date = $this->get_header_element(self::OSS_DATE, $headers);
		$canonicalizedOssHeaders = $this->format_headers($headers);
		$canonicalizedResource = rawurldecode($resource);

		if(!empty($canonicalizedOssHeaders)){
			$signStr = sprintf("%s\n%s\n%s\n%s\n%s\n%s",$method,$contentMD5,$contentType,$Date,$canonicalizedOssHeaders,$canonicalizedResource);
		}else{
			$signStr = sprintf("%s\n%s\n%s\n%s\n%s",$method,$contentMD5,$contentType,$Date,$canonicalizedResource);
		}

		return base64_encode(hash_hmac('sha1', $signStr, $access_key, true));
	}

	/**
	 * 获得某一个Header头信息
	 * @param string $key (Required)
	 * @param array $header (Required)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-27
	 * @return string
	 */
	public  function get_header_element($key , $headers){
		return isset($headers[$key]) ? $headers[$key] : '';
	}

	/**
	 * 构造签名字串
	 * @param string $method (Required)
	 * @param array $headers (Required)
	 * @param string $resource (Required)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-27
	 * @return string
	 */
	private function create_sign_for_nomal_auth($method,$headers,$resource){
		return "OSS " . $this->access_id . ":" .$this->get_assign($this->access_key, $method,$headers, $resource);
	}

	/**
	 * 生成CanonicalizedOSSHeaders字符串
	 * @param array $headers (Required)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-27
	 * @return string
	 */
	private function format_headers($headers = array()){
		$tmpHeaders = "";
		$tmpHeadersArr = array();
		if(is_array($headers) && $headers){
			foreach ($headers as $key=>$value){
				if(self::OSS_DEFAULT_PREFIX == substr(strtolower($key),0,strlen(self::OSS_DEFAULT_PREFIX))){
					$tmpHeadersArr[] = strtolower($key).":".$value;
				}
			}

			if($tmpHeadersArr){
				ksort($tmpHeadersArr);
				$tmpHeaders = implode(",", $tmpHeadersArr);
			}
		}

		return $tmpHeaders;
	}

	/**
	 * 校验BUCKET/OBJECT/OBJECT GROUP是否为空
	 * @param  string $name (Required)
	 * @param  string $errMsg (Required)
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-27
	 * @return void
	 */
	private function is_empty($name,$errMsg){
		try{
			if(empty($name)){
				throw new OSS_Exception($errMsg);
			}
		}catch (OSS_Exception $e){
			die($e->getMessage());
		}
	}

	/**
	 * 设置http header
	 * @param string $key (Required)
	 * @param string $value (Required)
	 * @param array $options (Required)
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @return void
	 */
	private static function set_options_header($key, $value, &$options) {
		try{
			if (isset ( $options [self::OSS_HEADERS] )) {
				if (! is_array ( $options [self::OSS_HEADERS] )) {
					throw new OSS_Exception(OSS_INVALID_OPTION_HEADERS, '-600');
				}
			} else {
				$options [self::OSS_HEADERS] = array ();
			}
		}catch(OSS_Exception $e){
			die($e->getMessage());
		}
		$options [self::OSS_HEADERS] [$key] = $value;
	}
}
