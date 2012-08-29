<?php
class Class_Plugin_HeadFile extends Zend_Controller_Plugin_Abstract
{
	public function postDispatch(Zend_Controller_Request_Abstract $request)
	{
		if($request->getModuleName() != 'admin' && $request->getModuleName() != 'rest') {
			$view = new Zend_View();
			$csa = Class_Session_Admin::getInstance();
			
			$fileUrl = Class_Server::getSiteFolderPath();
			if($csa->getSessionData('localCssMode') == 'active') {
				$fileUrl = 'http://local.host/'.Class_Server::getOrgCode();
			}
			
			$headFileCo = App_Factory::_m('HeadFile');
			$headFileDocs = $headFileCo->fetchDoc();
			
			foreach($headFileDocs as $doc) {
				if($doc->isExtFile == 1) {
					if($doc->type == 'css') {
						$view->headLink()->appendStylesheet(Class_Server::extUrl().'/'.$doc->filename);
					} else {
						$view->headScript()->appendFile(Class_Server::extUrl().'/'.$doc->filename);
					}
				} else {
					if($doc->type == 'css') {
						$view->headLink()->appendStylesheet($fileUrl.'/'.$doc->filename);
					} else {
						$view->headScript()->appendFile($fileUrl.'/'.$doc->filename);
					}
				}
			}
		}
	}
}