<?php
/**
 * 加载sdk包以及错误代码包
 */
require_once '../sdk.class.php';
//require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'error.class.php';

$oss_sdk_service = new ALIOSS();

/**
 * 获取bucket列表,该方法主要获取该账户下所有的bucket列表
 * 使用方法如下：
 * $bucket_list = $oss_sdk_service->list_bucket();
 */
//$bucket_list = $oss_sdk_service->list_bucket();
//print_r($bucket_list);die();

/**
 * 获得指定bucket的ACL
 * 调用方法如下：
 * $get_bucket_acl = $oss_sdk_service->get_bucket_acl($bucket,$options);
 * 其中需要传入的参数为 某一$bucket的名称,$options为一个关联数组，该方法可以传入Content-Type,使用系统内置的定义方式
 */
//$bucket = 'php-sdk-1330835674';
//$options = array(
//	ALIOSS::OSS_CONTENT_TYPE => 'text/xml',
//);
//$get_bucket_acl = $oss_sdk_service->get_bucket_acl($bucket,$options);
//print_r($get_bucket_acl);die();


/**
 * 设置指定bucket的ACL,目前只有三种acl private,public-read,public-read-write
 * 调用方法如下：
 * $set_bucket_acl = $oss_sdk_service->set_bucket_acl($bucket, $acl);
 * 其中需要指定$bucket,以及$acl的值,$acl的取值只能是private,public-read,public-read-write其中之一,$acl使用系统内置的定义方式
 * 对应的值为 private => ALIOSS::OSS_ACL_TYPE_PRIVATE ,public-read=> ALIOSS::OSS_ACL_TYPE_PUBLIC_READ ,public-read-write=> ALIOSS::OSS_ACL_TYPE_PUBLIC_READ_WRITE
 */
//$bucket = 'php-sdk-1330835674';
//$acl = ALIOSS::OSS_ACL_TYPE_PRIVATE;
//$set_bucket_acl = $oss_sdk_service->set_bucket_acl($bucket, $acl);
//print_r($set_bucket_acl);die();

/**
 * 创建bucket
 * 调用方法如下：
 * $create_bucket = $oss_sdk_service->create_bucket($bucket, $acl);
 * 其中需要传入$bucket,$acl是可选的，如果不指定ACL，则默认会是private,
 *$acl的取值只能是private,public-read,public-read-write其中之一,$acl使用系统内置的定义方式
 * 对应的值为 private => ALIOSS::OSS_ACL_TYPE_PRIVATE ,public-read=> ALIOSS::OSS_ACL_TYPE_PUBLIC_READ ,public-read-write=> ALIOSS::OSS_ACL_TYPE_PUBLIC_READ_WRITE
 */
//$bucket = 'oss-php-sdk-'.time();
//$acl = ALIOSS::OSS_ACL_TYPE_PUBLIC_READ;
//$create_bucket = $oss_sdk_service->create_bucket($bucket, $acl);
//print_r($create_bucket);die();


/**
 * 删除bucket
 * 调用方法如下：
 * $delete_bucket = $oss_sdk_service->delete_bucket($bucket);
 * 需要传入$bucket参数,其中$bucket内不能有内容，如果有内容则本次删除请求不会成功
 */
//$bucket = 'oss-php-sdk-1330928803';
//$delete_bucket = $oss_sdk_service->delete_bucket($bucket);
//print_r($delete_bucket);die();

/**
 * 获得bucket下的object列表
 * 调用方法如下：
 * $list_object = $oss_sdk_service->list_object($bucket,$options);
 * 其中需要传入$bucket参数,$options为可选参数，如果需要传入$options,必须为数组，其中数据的key必须为指定的，否则参数无效
 * $options = array(
 * 		'max-keys' 	=> max-keys用于限定此次返回object的最大数，如果不设定，默认为100，max-keys取值不能大于100。
 * 		'prefix'	=> 限定返回的object key必须以prefix作为前缀。注意使用prefix查询时，返回的key中仍会包含prefix。
 * 		'delimiter' => 是一个用于对Object名字进行分组的字符。所有名字包含指定的前缀且第一次出现delimiter字符之间的object作为一组元素
 * 		'marker'	=> 用户设定结果从marker之后按字母排序的第一个开始返回。
 * )
 * 其中 prefix，marker用来实现分页显示效果，参数的长度必须小于256字节。
 */
//$bucket = 'php-sdk-1330835674';
//$options = array(
//	'delimiter' => '/',
//	'prefix' => '',
//	'max-keys' => 10,
//	'marker' => 'myobject-1330850469.pdf',
//);
//$list_object = $oss_sdk_service->list_object($bucket,$options);
//print_r($list_object);die();


/**
 * 获得$bucket下的某个object,$object为文件，不能为目录（该API在SDK中暂未实现Range功能）
 * 调用方法如下：
 * $get_object = $oss_sdk_service->get_object($bucket,$object);
 * 其中需要传入$bucket,$object参数，参数为必须
 */
//$bucket = 'php-sdk-1330835674';
//$object = 'myobject1330836576.txt';
//$get_object = $oss_sdk_service->get_object($bucket,$object);
//print_r($get_object);die();


/**
 * 创建文件夹(是虚拟文件夹)
 * 调用方法如下：
 * $create_object_dir = $oss_sdk_service->create_object_dir($bucket,$object);
 * 其中需要传入$bucket,$object参数，参数为必须
 */
//$bucket = 'php-sdk-1330835674';
//$object = 'oss-object-'.time();
//$create_object_dir = $oss_sdk_service->create_object_dir($bucket,$object);
//print_r($create_object_dir);die();


/**
 * 通过http body上传文件,适用于直接写入内容的上传，比较小的文件
 * 调用方法如下：
 * $upload_file_by_content = $oss_sdk_service->upload_file_by_content($bucket,$object,$upload_file_options);
 * 其中的$bucket,$object以及$upload_file_options为必选参数，$upload_file_options必须为数组，且key必须为规定的值，否则会上传失败,
 * $object是文件名称，如果上传的文件不是直接位于bucket下，而是位于某一子目录下，则$object = 'dir_name/dir_name/file_name'
 * 其中的content 为文件的内容，$length为文件的大小
 * $upload_file_options = array(
 *	'content' => $content,
 *	'length' => $length,
 * );
 */
//$bucket = 'php-sdk-1330835674';
//$object = 'oss-file-name-'.time().'.txt';
//$content = 'aaaaaaaaaaaaaa';
//$upload_file_options = array(
//	'content' => $content,
// 	'length' => strlen($content),
//);
//$upload_file_by_content = $oss_sdk_service->upload_file_by_content($bucket,$object,$upload_file_options);
//print_r($upload_file_by_content);die();


/**
 * 通过文件方式上传,适合小文件上传，大文件上传请使用multipart
 */
//$bucket = 'php-sdk-1328520898';
//$file = "d:\\ccc.pdf";  //文件路径
//$object = 'cpp.how.to.program-'.time().'.pdf';  //object名称
//$response_upload_file_by_file = $oss_sdk_service->upload_file_by_file($bucket,$object,$file);
//print_r($response_upload_file_by_file);die();


/**
 * Copy Object
 * 调用方法如下：
 * $copy_object_result = $oss_sdk_service->copy_object($from_bucket, $from_object,$to_bucket, $to_object)
 * 其中需要传入$from_bucket,$from_object,$to_bucket,$to_object等参数，均不能为空
 */
//$from_bucket = 'php-sdk-1330835674';
//$to_bucket = 'php-sdk-1330835674';
//$from_object = 'Cpp.How.to.Program.7-1330855122.pdf';
//$to_object = 'copy-object-'.time().'.pdf';
//$copy_object_result = $oss_sdk_service->copy_object($from_bucket, $from_object,$to_bucket, $to_object);
//print_r($copy_object_result);die();



/**
 * 获取带签名的外链URL
 * 调用方法如下：
 * $get_sign_url = $oss_sdk_service->get_sign_url($bucket,$object,$timeout)
 * 其中需要传入$bucket,$object不能为空，$timeout为过期的秒数，从当前时间往后多少秒过期，若不传，则使用默认值60
 */
//$bucket = 'php-sdk-1330835674';
//$object = 'Cpp.How.to.Program.7-1330855122.pdf';
//$timeout = 3600;
//$get_sign_url = $oss_sdk_service->get_sign_url($bucket,$object,$timeout);
//print_r($get_sign_url);die();

/**
 * 检测Object是否存在
 * 调用方法如下：
 * $is_object_exist = $oss_sdk_service->is_object_exist($bucket, $object);
 * 其中需要传入$bucket,$object参数，参数为必须
 */
//$bucket = 'php-sdk-1330835674';
//$object = 'Cpp.How.to.Program.7-1330855122.pdf';
//$is_object_exist = $oss_sdk_service->is_object_exist($bucket, $object);
//var_dump($is_object_exist?'Exist':'Not Exist');die();


/**
 * 获得某一个object的URL
 * 调用方法如下：
 * $get_object_url = $oss_sdk_service->get_object_url($bucket, $object);
 * 其中需要传入$bucket,$object参数，参数为必须,$object为文件
 */
//$bucket = 'php-sdk-1330835674';
//$object = 'Cpp.How.to.Program.7-1330855122.pdf';
//$get_object_url = $oss_sdk_service->get_object_url($bucket, $object);
//var_dump($get_object_url?$get_object_url:'Not Exist');die();


/**
 * 获得object的meta
 * 调用方法如下：
 * $get_object_meta = $oss_sdk_service->get_object_meta($bucket, $object);
 * 其中需要传入$bucket,$object参数，参数为必须,如果object为文件夹，则需要添加'/'
 */
//$bucket = 'php-sdk-1330835674';
//$object = 'Cpp.How.to.Program.7-1330855122.pdf';
//$get_object_meta = $oss_sdk_service->get_object_meta($bucket, $object);
//print_r($get_object_meta);die();


/**
 * 删除object
 * 调用方法如下：
 * $delete_object = $oss_sdk_service->delete_object($bucket,$object);
 * 其中需要传入$bucket,$object参数，如果object为文件，则会直接删除，如果为文件夹，若文件夹不为空，则不能删除
 */
//$bucket = 'php-sdk-1330835674';
//$object = 'Cpp.How.to.Program.7-1330855122.pdf';
//$delete_object = $oss_sdk_service->delete_object($bucket,$object);
//print_r($delete_object);die();

/**
 * 创建object group
 * 调用方法如下：
 * $create_object_group = $oss_sdk_service->create_object_group($bucket,$object_group,$object_group_array);
 * 其中参数$object_group,$bucket,$object_group_array不为空，且$object_group_array内的object必须位于同一bucket下
 */
//$bucket = 'php-sdk-1330835674';
//$object_group = 'object-group-'.time();
//$object_group_array = array(
//	'Cpp.How.to.Program.7-1330855122.pdf',
//	'copy-object-1330929729.pdf',
//	'hh_axur-setup.pdf',
//);
//
//$create_object_group = $oss_sdk_service->create_object_group($bucket,$object_group,$object_group_array);
//print_r($create_object_group);die();


/*
 * 获取object group
 * 调用方法如下：
 * $get_object_group = $oss_sdk_service->get_object_group($bucket,$object_group);
 * 其中参数$object_group,$bucket为必须参数
 */
//$bucket = 'php-sdk-1330835674';
//$object_group = 'object-group-1330930277';
//$get_object_group = $oss_sdk_service->get_object_group($bucket,$object_group);
//print_r($get_object_group);die();


/**
 * 获取object group index 
 * 调用方法如下：
 * $get_object_group_index = $oss_sdk_service->get_object_group_index($bucket,$object_group);
 * 其中参数$object_group,$bucket为必须参数
 */
//$bucket = 'php-sdk-1330835674';
//$object_group = 'object-group-1330930277';
//$get_object_group_index = $oss_sdk_service->get_object_group_index($bucket,$object_group);
//print_r($get_object_group_index);die();


/**
 * 获取object group meta
 * 调用方法如下：
 * $get_object_group_meta = $oss_sdk_service->get_object_group_meta($bucket,$object_group);
 * 其中参数$object_group,$bucket为必须参数
 */
//$bucket = 'php-sdk-1330835674';
//$object_group = 'object-group-1330930277';
//$get_object_group_meta = $oss_sdk_service->get_object_group_meta($bucket,$object_group);
//print_r($get_object_group_meta);die();


/**
 * 删除object group 
 * 调用方法如下：
 * $delete_object_group = $oss_sdk_service->delete_object_group($bucket,$object_group);
 * 其中参数$object_group,$bucket为必须参数
 */
//$bucket = 'php-sdk-1330835674';
//$object_group = 'object-group-1330930277';
//$delete_object_group = $oss_sdk_service->delete_object_group($bucket,$object_group);
//print_r($delete_object_group);die();


/*%*********************************************************************************************************************%*/
//Mulit Part相关  ，适合大文件上传

/**
 * 使用Multi-Part上传文件，该操作需要经过如下的步骤
 * 1. 初始化上传，调用initiate_multipart_upload，其中$bucket,$object为必选
 * 2. 上传part,调用upload_part，$bucket, $object, $uploadId为必选，还需要传入一个关联数组作为参数，该数组如下定义：
 * 	  array(
 * 		'fileUpload' => $filepath,   文件路径
 *	    'partNumber' => ($i + 1),    文件编号
 *	    'seekTo' => (integer) $part['seekTo'],  读取文件位置
 *	    'length' => (integer) $part['length'],  该部分part文件长度
 *    )
 *    
 *    其中的seekTo,length会有程序自动生成
 * 
 * 3. 完成上传,调用complete_multipart_upload，其中$bucket, $object, $uploadId, $upload_parts为必选
 */

//步骤1 ，初始化multipart
//$bucket = 'php-sdk-1330835674';
//$object = 'Cpp.How.to.Program.7-'.time().'.pdf';
//$filepath = "D:\\ccc.pdf";
//$response_initiate_multipart_upload = $oss_sdk_service->initiate_multipart_upload($bucket,$object);
//if(!$response_initiate_multipart_upload->isOK()){
//	die('initial multipart upload failed....');
//}

//解析返回，获取uploadId
//$xml = new SimpleXmlIterator($response_initiate_multipart_upload->body);
//$uploadId = (string)$xml->UploadId;

//步骤2 上传part
// 首先获取文件可以分多少个part
//$parts = $oss_sdk_service->get_multipart_counts(filesize($filepath), 5242880);
//$response_upload_part = array();
//foreach ($parts as $i => $part){
//	//开始上传part
//	$response_upload_part[] = $oss_sdk_service->upload_part($bucket, $object, $uploadId, array(
//		'fileUpload' => $filepath,
//	    'partNumber' => ($i + 1),
//	    'seekTo' => (integer) $part['seekTo'],
//	    'length' => (integer) $part['length'],
//	));
//}


//$upload_parts = array();
//$result = true;

//获取上传Part返回结果
//foreach ($response_upload_part as $i=>$response){
//	$result = $result && $response->isOk();
//}
//
//if(!$result){
//	$oss_sdk_service->abort_multipart_upload($bucket, $object, $uploadId);
//	die('any part upload failed...');
//}

//构造upload part
//foreach ($response_upload_part as $i=>$response){
//	$upload_parts[] = array(
//		'PartNumber' => ($i + 1),
//	    'ETag' => (string) $response->header['etag']
//	);		
//}

//步骤3 完成multipart upload
//$complete_multipart_upload_response = $oss_sdk_service->complete_multipart_upload($bucket, $object, $uploadId, $upload_parts);
//print_r($complete_multipart_upload_response);die();



/**
 * 列出multipart上传中的part，由于在列出part的时候需要有正在进行的multipart，故需要完成multi-part的步骤1和2,调用步骤如下：
 * 1. 初始化上传，调用initiate_multipart_upload，其中$bucket,$object为必选
 * 2. 上传part,调用upload_part，$bucket, $object, $uploadId为必选，还需要传入一个关联数组作为参数，该数组如下定义：
 * 	  array(
 * 		'fileUpload' => $filepath,   文件路径
 *	    'partNumber' => ($i + 1),    文件编号
 *	    'seekTo' => (integer) $part['seekTo'],  读取文件位置
 *	    'length' => (integer) $part['length'],  该部分part文件长度
 *    )
 *    
 *    其中的seekTo,length会有程序自动生成
 * 
 * 3. 列出parts,$oss_sdk_service->list_parts($bucket, $object, $uploadId,$options);其中$bucket, $object, $uploadId为必选,$options可选
 */
//步骤1 初始化
//$bucket = 'php-sdk-1330835674';
//$object = 'Cpp.How.to.Program.7-'.time().'.pdf';
//$filepath = "D:\\ccc.pdf";
//$response_initiate_multipart_upload = $oss_sdk_service->initiate_multipart_upload($bucket,$object);
//if(!$response_initiate_multipart_upload->isOK()){
//	die('initial multipart upload failed....');
//}

//$xml = new SimpleXmlIterator($response_initiate_multipart_upload->body);
//$uploadId = (string)$xml->UploadId;

//获取分片
//$parts = $oss_sdk_service->get_multipart_counts(filesize($filepath), 5242880);
//$response_upload_part = array();

//步骤2 上传part
//foreach ($parts as $i => $part){
//	$response_upload_part[] = $oss_sdk_service->upload_part($bucket, $object, $uploadId, array(
//		'fileUpload' => $filepath,
//	    'partNumber' => ($i + 1),
//	    'seekTo' => (integer) $part['seekTo'],
//	    'length' => (integer) $part['length'],
//	));
//}

//列出parts，调用方法($bucket, $object, $uploadId,$options);,其中$bucket, $object, $uploadId为必选参数，$options可选，$options若传，定义方式如下
// $options = array(
//		'max-parts' => 10 一次最多取多少个part
//		'part-number-marker' => 1,  从哪个part-number开始
//)

//$options = array(
//	'max-parts' => 10,
//	'part-number-marker' => 1,
//);

//步骤3 列出parts
//$list_parts_response = $oss_sdk_service->list_parts($bucket, $object, $uploadId,$options);
//print_r($list_parts_response);die();



/**
 * 终止multi-part upload，因为有终止进行中的任务，故需要执行步骤1和2
 * 1. 初始化上传，调用initiate_multipart_upload，其中$bucket,$object为必选
 * 2. 上传part,调用upload_part，$bucket, $object, $uploadId为必选，还需要传入一个关联数组作为参数，该数组如下定义：
 * 	  array(
 * 		'fileUpload' => $filepath,   文件路径
 *	    'partNumber' => ($i + 1),    文件编号
 *	    'seekTo' => (integer) $part['seekTo'],  读取文件位置
 *	    'length' => (integer) $part['length'],  该部分part文件长度
 *    )
 *    
 *    其中的seekTo,length会有程序自动生成
 * 
 * 3. 终止multi-part,调用$oss_sdk_service->abort_multipart_upload($bucket, $object, $uploadId);其中$bucket, $object, $uploadId为必选
 */
//步骤1 初始化
//$bucket = 'php-sdk-1330835674';
//$object = 'Cpp.How.to.Program.7-'.time().'.pdf';
//$filepath = "D:\\ccc.pdf";
//$response_initiate_multipart_upload = $oss_sdk_service->initiate_multipart_upload($bucket,$object);
//if(!$response_initiate_multipart_upload->isOK()){
//	die('initial multipart upload failed....');
//}
//$xml = new SimpleXmlIterator($response_initiate_multipart_upload->body);
//$uploadId = (string)$xml->UploadId;

//$parts = $oss_sdk_service->get_multipart_counts(filesize($filepath), 5242880);
//$response_upload_part = array();

//步骤2 上传part
//foreach ($parts as $i => $part){
//	$response_upload_part[] = $oss_sdk_service->upload_part($bucket, $object, $uploadId, array(
//		'fileUpload' => $filepath,
//	    'partNumber' => ($i + 1),
//	    'seekTo' => (integer) $part['seekTo'],
//	    'length' => (integer) $part['length'],
//	));
//}

//步骤3  终止
//$abort_multipart_upload_response = $oss_sdk_service->abort_multipart_upload($bucket, $object, $uploadId);
//print_r($abort_multipart_upload_response);die();


/**
 * 列出所有的multipart upload
 * 调用方法 $oss_sdk_service->list_multipart_uploads($bucket);其中$bucket是必选参数
 */
//$bucket = 'php-sdk-1330835674';
//$list_multipart_uploads_response = $oss_sdk_service->list_multipart_uploads($bucket);
//print_r($list_multipart_uploads_response);die();


/**
 * 一次性完成multi-part upload，由于mulit-part upload涉及三个步骤，所以封装了该方法使得使用multi-part更加方便
 * 调用方法如下：
 * $oss_sdk_service->create_mpu_object($bucket, $object,$options);
 * 其中的$bucket,$object,$options为必须,$options的定义如下
 * $options = array(
 * 		'fileUpload' => $filepath,  文件路径
 * 		'partSize' => 5242880,      分片大小为了保证上传的效率，建议该值不要太大，建议使用5M = 5242880
 * )
 */
//$bucket = 'php-sdk-1330835674';
//$object = 'myobject-pdf-'.time().'.pdf';
//$filepath = "d:\\ccc.pdf";
//$options = array(
//	'fileUpload' => $filepath,
//	'partSize' => 5242880,
//);

//$create_mpu_object_response = $oss_sdk_service->create_mpu_object($bucket, $object,$options);
//print_r($create_mpu_object_response);die();


/**
 * 上传整个目录，通过multi-part,通过该方式创建的object默认为文件名
 * $oss_sdk_service->create_mtu_object_by_dir($bucket,$dir,$recursive = false,$exclude = ".|..|.svn",$options = null)
 * 其中$bucket,$dir为必选参数，$recursive,$exclude,$options可选
 * $bucket 为bucket名称
 * $dir 某一目录
 * $recursive 该参数设置是否递归读取目录。
 * $exclude 要过滤掉的文件，默认为系统默认生成的.,..,和svn文件.svn
 */
//$bucket = 'php-sdk-1329819217';
//$dir = "D:\\webapp\\Apache\\htdocs\\opencvdotnet";
//$recursive = true;
//$create_mtu_object_by_dir_response = $oss_sdk_service->create_mtu_object_by_dir($bucket,$dir,$recursive);
//print_r($create_mtu_object_by_dir_response);die();






