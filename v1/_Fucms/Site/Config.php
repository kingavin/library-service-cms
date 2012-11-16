<?php
namespace Fucms\Site;

use Mongo;

class Config
{
	public static function config($configArr)
	{
		$requestHost = $_SERVER['HTTP_HOST'];
		$m = new Mongo($configArr['server_center_host'], array('persist' => 'x'));
		$db = $m->selectDb('server_center');
		
		$siteDoc = $db->site->findOne(array('domain' => $requestHost));
		
		if(is_null($siteDoc)) {
			header('Location: http://www.enorange.com/no-site/');
			exit(0);
		}
		
		if(!$siteDoc['active']) {
			header('Location: http://www.enorange.com/site-expired/');
			exit(0);
		}
		
		\Class_Server::config($configArr['enviroment'], 'v2', $siteDoc['localSiteId'], $siteDoc['organizationCode'], $siteDoc['remoteSiteId']);
	}
}