<?php
class Class_Payment_Socket_99Bill
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
        $this->_subject = Class_Core::siteInfo('siteName');
        
        $this->_gateway = "https://www.alipay.com/cooperate/gateway.do?";
        $this->_signType = "MD5";
        $this->_transport = "https";
        
        $this->_service = "trade_create_by_buyer";
    }
    
    public function createUrl(Class_Model_Order $order, Class_Model_Address $address)
    {
        $totalFee = $order->getData('total');
        $orderId = $order->getData('id');
                
                $inputCharset = "1";
                $bgUrl = "http://www.pmatch.cn/payment-gateway/bill99-return/";
                $version = "v2.0";
                $language = "1";
                $signType = "1";
                
                //10018369224 bo
                
                $merchantAcctId = "1001760273501";
                $payerName=$address->getData('consignee');
                $payerContactType="1";
                $payerContact = Class_Customer::getData('email');
                
                $orderAmount = $totalFee * 100;
                $orderTime = date('YmdHis', strtotime($order->getData('created')));//$order->getData('created');//date('YmdHis');
//                $orderTime=$order->getData('created');
                $productName="眠趣商品";
                $productNum="1";
                $payType="00";
                $redoFlag="0";
                $key="BWIGFJ7DU2UJ8N2J";
                // bo 安全校验码key：wdnbesgfp8b73d42gtyx2d6ozjphdc63
                
//                $signMsgVal = "";
                $urlVar = "";
                $urlVar=$this->_appendParam($urlVar,"inputCharset",$inputCharset);
                $urlVar=$this->_appendParam($urlVar,"bgUrl", $bgUrl);
                $urlVar=$this->_appendParam($urlVar,"version",$version);
                $urlVar=$this->_appendParam($urlVar,"language",$language);
                $urlVar=$this->_appendParam($urlVar,"signType",$signType);
                $urlVar=$this->_appendParam($urlVar,"merchantAcctId",$merchantAcctId);
                $urlVar=$this->_appendParam($urlVar,"payerName",$payerName);
                $urlVar=$this->_appendParam($urlVar,"payerContactType",$payerContactType);
                $urlVar=$this->_appendParam($urlVar,"payerContact",$payerContact);
                $urlVar=$this->_appendParam($urlVar,"orderId",$orderId);
                $urlVar=$this->_appendParam($urlVar,"orderAmount",$orderAmount);
                $urlVar=$this->_appendParam($urlVar,"orderTime",$orderTime);
                $urlVar=$this->_appendParam($urlVar,"productName",$productName);
                $urlVar=$this->_appendParam($urlVar,"productNum",$productNum);
            //  $urlVar=_appendParam($urlVar,"productId",$productId);
//                $urlVar=$this->_appendParam($urlVar,"ext1",$ext1);
//                $urlVar=$this->_appendParam($urlVar,"ext2",$ext2);
                $urlVar=$this->_appendParam($urlVar,"payType",$payType); 
                $urlVar=$this->_appendParam($urlVar,"redoFlag",$redoFlag);
            //  $urlVar=_appendParam($urlVar,"pid",$pid);
            
                
                $signMsgVal=$this->_appendParam($urlVar,"key",$key);
                $signMsg=strtoupper(md5($signMsgVal));
                
//                echo '************<br />';
//                echo $orderAmount.'<br />';
//                echo '************<br />';
                $url = 'https://www.99bill.com/gateway/recvMerchantInfoAction.htm?'.$urlVar.'&signMsg='.$signMsg;
//                echo '************<br />';
//                echo $url.'<br />';
//                echo '************<br />';
//                die('fefefefe');
                
                //$this->_redirector->gotoUrl($url);
                return $url;
    }
    
    protected function _appendParam($returnStr,$paramId,$paramValue)
    {
        if($returnStr!="") {
            if($paramValue!="") {
                $returnStr.="&".$paramId."=".$paramValue;
            }
        } else {
            If($paramValue!="") {
               $returnStr=$paramId."=".$paramValue;
            }
        }
        return $returnStr;
    }
}