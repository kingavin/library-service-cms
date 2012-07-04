<?php
class Class_Plugin_HeadFile extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
		if($request->getModuleName() != 'admin' && $request->getModuleName() != 'rest') {
			$headFileCo = App_Factory::_m('HeadFile');
	    	$headFileDocs = $headFileCo->fetchDoc();
	    	
	    	foreach($headFileDocs as $doc) {
	    		if($doc->type == 'css') {
	    			$view = new Zend_View();
	    			$view->headLink()->appendStylesheet(Class_Server::fileUrl().'/'.$doc->filename);
	    		} else {
	    			$view->headScript()->appendFile(Class_Server::fileUrl().'/'.$doc->filename);
	    		}
	    	}
		}
    }
}