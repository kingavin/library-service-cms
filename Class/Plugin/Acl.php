<?php
class Class_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    	if($request->getModuleName() == 'admin') {
        	$csa = Class_Session_Admin::getInstance();
        	
        	if(!$csa->isLogin()) {
				$sso = new App_SSO();
				if($csa->hasSSOToken()) {
					$st = $csa->getSSOToken();
					$response = $sso->auth($st);
					 
					$responseCode = $response[0];
					$xmlBody = $response[1];
					
					$xml = new SimpleXMLElement($xmlBody);
					switch($responseCode) {
						case '200':
							$csa->login($xml);
							header("Location: ".Class_Server::getSiteUrl().'/admin');
							break;
						case '403':
							//token not exist or expired, try to request with a new token
							$ssoToken = $csa->getSSOToken();
							$ssoLoginUrl = $sso->getLoginUrl('service-cms', Class_Server::getSiteUrl().'/admin', $ssoToken, Class_Server::API_KEY);
							header("Location: ".$ssoLoginUrl);
							break;
						default:
							echo "error while getting identity from server!";
							exit(1);
					}
				} else {
					$ssoToken = $csa->getSSOToken();
					$ssoLoginUrl = $sso->getLoginUrl('service-cms', Class_Server::getSiteUrl().'/admin', $ssoToken, Class_Server::API_KEY);
					header("Location: ".$ssoLoginUrl);
				}
			} else {
				if(!$csa->isResourceOwner()) {
					$request->setModuleName('default');
					$request->setControllerName('forbidden');
					$request->setActionName('not-resource-owner');
				} else {
		        	$roleId = $csa->getRoleId();
		            $acl = Class_Acl::getInstance();
		            $controllerName = $request->getControllerName();
		            $actionName = $request->getActionName();
		            
		            if(!$acl->isAllowed($roleId, $controllerName, $actionName)) {
		                if($roleId == 'nobody') {
		                    $request->setControllerName('index');
		                    $request->setActionName('login');
		                } else {
		                    $request->setControllerName('index');
		                    $request->setActionName('no-privilege');
		                }
		            }
				}
			}
        } else if($request->getModuleName() == 'rest') { 
        	
    	} else {
        	$clf = Class_Layout_Front::getInstance();
        	$resource = $clf->getResource();
        	
        	if(is_null($resource)) {
        		throw new Class_Exception_Pagemissing();
        	}
        }
    }
}