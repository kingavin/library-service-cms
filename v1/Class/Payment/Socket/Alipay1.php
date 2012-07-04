<?php
class Class_Payment_Socket_Alipay
{
    protected $_securityCode;
    
    protected $_partner;
    protected $_sellerEmail;
    protected $_subject;
    
    protected $_gateway;
    protected $_signType;
    protected $_transport;
    protected $_service;
    
    public function __construct()
    {
        $this->_securityCode = Class_Core::siteInfo('securityCode');
        
        $this->_partner = Class_Core::siteInfo('partnerId');
        $this->_sellerEmail = Class_Core::siteInfo('sellerEmail');
        $this->_subject = Class_Core::siteInfo('siteName');
        
        $this->_gateway = "https://www.alipay.com/cooperate/gateway.do?";
        $this->_signType = "MD5";
        $this->_transport = "https";
        
        $this->_service = "trade_create_by_buyer";
    }
    
    function createUrl(Class_Model_Order $order, Class_Model_Address $address, $httpHost, $subject = null, $body = null)
    {
        if(is_null($subject)) {
            $subject = $this->_subject;
        }
        if(is_null($body)) {
            $body = "感谢选购".$this->_subject."产品";
        }
        //$httpHost = $_SERVER ['HTTP_HOST'] 
        $parameter = array(
        	"_input_charset" => "utf-8",
            "service" => $this->_service,
            "partner" => $this->_partner,
        	"seller_email" => $this->_sellerEmail,
            "return_url" => "http://".$httpHost."/payment-gateway/return/",
            "notify_url" => "http://".$httpHost."/payment-gateway/notify/",
            
            "subject" => $subject,
            "body" => $body,
            "out_trade_no" => $order->getData('id'),
            "price" => $order->getData('sub_total'),
            "payment_type" => "1",
            "quantity" => "1",
            "show_url" => "http://".$httpHost."/user/order-list/id/".$order->getData('id'),
            
            "receive_name" => $address->getData('consignee'),
            "receive_address" => $address->getData('provinceName').$address->getData('cityName').$address->getData('addressDetail'),    
            "receive_zip" => $address->getData('postcode'),          
            "receive_mobile" => $address->getData('mobile') ,  
            "receive_phone" => $address->getData('phone'),
            
            "logistics_fee" =>$order->getData('delivery_fee'),
            "logistics_payment" =>'BUYER_PAY',
            "logistics_type" =>'EXPRESS'
        );
        $parameter = $this->_paraFilter($parameter);
        $parameter = $this->_argSort($parameter);
        
        $arg = "";
        $url = "";
        while (list($key, $val) = each($parameter)) {
//            $arg.= $key."=".$val."&";
            $arg.= $key."=".urlencode($val)."&";
            $url.= $key."=".$val."&";
        }
        $prestr = substr($arg, 0, count($arg) - 2);
        $preurl = substr($url, 0, count($url) - 2);
        Class_Core::log($prestr.' is prestr', 'payment');
        $mysign = $this->_sign($preurl.$this->_securityCode);
        Class_Core::log($mysign.' is mysign', 'payment');
        Class_Core::log($this->_securityCode.' is sc', 'payment');
        return $this->_gateway.$arg."sign=".$mysign."&sign_type=".$this->_signType;
    }
    
//        var $gateway = "https://www.alipay.com/cooperate/gateway.do?";         //支付接口
//    var $parameter;       //全部需要传递的参数
//    var $security_code;   //安全校验码
//    var $mysign;          //签名
    
    
    protected function _paraFilter($parameter) { //除去数组中的空值和签名模式
        $para = array();
        while(list ($key, $val) = each ($parameter)) {
			switch($key) {
			    case 'sign':
			    case 'sign_type':
			    case 'controller':
			    case 'action':
			    case 'module':
			        break;
			    default:
			        if($val != "") {
			            $para[$key] = $parameter[$key];
			        }
			        break;
			}
		}
        return $para;
    }
    
    //构造支付宝外部服务接口控制
//    function __construct($parameter, $security_code, $sign_type = "MD5", $transport= "https") {
//        $this->parameter      = $this->para_filter($parameter);
//        $this->security_code  = $security_code;
//        $this->sign_type      = $sign_type;
//        $this->mysign         = '';
//        $this->transport      = $transport;
//        if($parameter['_input_charset'] == "")
//        $this->parameter['_input_charset']='GBK';
//        if($this->transport == "https") {
//            $this->gateway = "https://www.alipay.com/cooperate/gateway.do?";
//        } else $this->gateway = "http://www.alipay.com/cooperate/gateway.do?";
//        $sort_array = array();
//        $arg = "";
//        $sort_array = $this->_argSort($this->parameter);
//        while (list ($key, $val) = each ($sort_array)) {
//            $arg.=$key."=".$this->charset_encode($val,$this->parameter['_input_charset'])."&";
//        }
//        $prestr = substr($arg,0,count($arg)-2);  //去掉最后一个问号
//        $this->mysign = $this->sign($prestr.$this->security_code);
//    }
    
    function create_url() {
        $url        = $this->gateway;
        $sort_array = array();
        $arg        = "";
        $sort_array = $this->_argSort($this->parameter);
        while (list ($key, $val) = each ($sort_array)) {
            $arg.=$key."=".urlencode($this->charset_encode($val,$this->parameter['_input_charset']))."&";
        }
        $url.= $arg."sign=" .$this->mysign ."&sign_type=".$this->sign_type;
        return $url;

    }

    protected function _argSort($array)
    {
        ksort($array);
        reset($array);
        return $array;
    }

    protected function _sign($prestr) {
        $mysign = "";
        if($this->_signType == 'MD5') {
            $mysign = md5($prestr);
        } elseif($this->_signType =='DSA') {
            die("DSA 签名方法待后续开发，请先使用MD5签名方式");
        } else {
            die("支付宝暂不支持".$this->_signType."类型的签名方式");
        }
        return $mysign;

    }
    
    protected function sign($prestr) {
        $mysign = "";
        if($this->sign_type == 'MD5') {
            $mysign = md5($prestr);
        }elseif($this->sign_type =='DSA') {
            //DSA 签名方法待后续开发
            die("DSA 签名方法待后续开发，请先使用MD5签名方式");
        }else {
            die("支付宝暂不支持".$this->sign_type."类型的签名方式");
        }
        return $mysign;

    }
    
    function para_filter($parameter) { //除去数组中的空值和签名模式
        $para = array();
        while (list ($key, $val) = each ($parameter)) {
            if($key == "sign" || $key == "sign_type" || $val == "")
                continue;
            else
                $para[$key] = $parameter[$key];
        }
        return $para;
    }
    
    
    //实现多种字符编码方式
    function charset_encode($input, $_output_charset, $_input_charset="utf-8") {
        $output = "";
        if(!isset($_output_charset) )$_output_charset  = $this->parameter['_input_charset'];
        if($_input_charset == $_output_charset || $input ==null) {
            $output = $input;
        } elseif (function_exists("mb_convert_encoding")){
            $output = mb_convert_encoding($input,$_output_charset,$_input_charset);
        } elseif(function_exists("iconv")) {
            $output = iconv($_input_charset,$_output_charset,$input);
        } else die("sorry, you have no libs support for charset change.");
        return $output;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
	public function returnVerify($getParams) {
	    $sign = $getParams["sign"];
	    $this->_signType = $getParams["sign_type"];
	    
	    $sortedGet = $this->_paraFilter($getParams);
		$sortedGet = $this->_argSort($sortedGet);
		
		$arg = "";
		while (list ($key, $val) = each ($sortedGet)) {
	        $arg.= $key."=".$val."&";
		}
		$prestr = substr($arg, 0, count($arg)-2);
		$mysign = $this->_sign($prestr.$this->_securityCode);
		Class_Core::log($prestr.' => mysign:'.$mysign.' sign:'.$sign.'; loged in Class_Payment_Socket_Alipay:returnVerify', 'payment');
		if ($mysign == $sign) {
		    return true;
		} else {
		    return false;
		}
	}
    
	public function notifyVerify() {
		if($this->_transport == "https") {
			$verifyUrl = $this->_gateway."service=notify_verify" ."&partner=" .$this->_partner. "&notify_id=".$_POST["notify_id"];
		} else {
			$verifyUrl = $this->_gateway."partner=".$this->_partner."&notify_id=".$_POST["notify_id"];
		}
		Class_Core::log('verifyUrl in notifyVerify: '.$verifyUrl, 'payment');
		$verifyResult = $this->_getVerify($verifyUrl);
		$post = $this->_paraFilter($_POST);
		$sortPost = $this->_argSort($post);
		while (list ($key, $val) = each ($sortPost)) {
			$arg.=$key."=".$val."&";
		}
		$prestr = substr($arg, 0, count($arg)-2);
		$mysign = $this->_sign($prestr.$this->_securityCode);
		Class_Core::log('mysign:'.$mysign.'; sign:'.$_POST["sign"], 'payment');
		if(eregi("true$", $verifyResult) && $mysign == $_POST["sign"])  {
			return true;
		} else {
		    return false;
		}
	}
    
    
    
    
    
    
    
    
	public function notifyVerifyOld() {
		if($this->transport == "https") {
			$veryfy_url = $this->gateway. "service=notify_verify" ."&partner=" .$this->partner. "&notify_id=".$_POST["notify_id"];
		} else {
			$veryfy_url = $this->gateway. "partner=".$this->partner."&notify_id=".$_POST["notify_id"];
		}
		$veryfy_result  = $this->_getVerify($veryfy_url);
		$post           = $this->para_filter($_POST);
		$sort_post      = $this->_argSort($post);
		while (list ($key, $val) = each ($sort_post)) {
			$arg.=$key."=".$val."&";
		}
		$prestr = substr($arg,0,count($arg)-2);  //去掉最后一个&号
		$this->mysign = $this->sign($prestr.$this->security_code);
		Class_Core::log($info.' logtype is getVerify', 'payment');
		if (eregi("true$",$veryfy_result) && $this->mysign == $_POST["sign"])  {
			return true;
		} else return false;
	}
	
	public function returnVerifyOld() {
		$sort_get= $this->_argSort($_GET);
		while (list ($key, $val) = each ($sort_get)) {
			if($key != "sign" && $key != "sign_type")
			$arg.=$key."=".$val."&";
		}
		$prestr = substr($arg,0,count($arg)-2);  //去掉最后一个&号
		$this->mysign = $this->sign($prestr.$this->security_code);
		/*while (list ($key, $val) = each ($_GET)) {
		$arg_get.=$key."=".$val."&";
		}*/
//		log_result("return_url_log=".$_GET["sign"]."&".$this->mysign."&".$this->charset_decode(implode(",",$_GET),$this->_input_charset ));
		if ($this->mysign == $_GET["sign"])  return true;
		else return false;
	}
	
	protected function _getVerify($url, $time_out = "60") {
		$urlarr     = parse_url($url);
		$errno      = "";
		$errstr     = "";
		$transports = "";
		if($urlarr["scheme"] == "https") {
			$transports = "ssl://";
			$urlarr["port"] = "443";
		} else {
			$transports = "tcp://";
			$urlarr["port"] = "80";
		}
		Class_Core::log($transports.$urlarr['host'].' is the fsockopen url', 'payment');
		$fp=@fsockopen($transports.$urlarr['host'], $urlarr['port'], $errno, $errstr, $time_out);
		if(!$fp) {
		    Class_Core::log('socket port not open logtype is getVerify', 'payment');
			die("ERROR: $errno - $errstr<br />\n");
		} else {
			fputs($fp, "POST ".$urlarr["path"]." HTTP/1.1\r\n");
			fputs($fp, "Host: ".$urlarr["host"]."\r\n");
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-length: ".strlen($urlarr["query"])."\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $urlarr["query"] . "\r\n\r\n");
			while(!feof($fp)) {
				$info[]=@fgets($fp, 1024);
			}
			fclose($fp);
			$info = implode(",",$info);
			Class_Core::log($info.' logtype is getVerify', 'payment');
			return $info;
		}

	}
}