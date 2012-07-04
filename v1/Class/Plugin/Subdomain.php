<?php
class Class_Plugin_Subdomain
{
	public function beforeAdminLogin($selector)
	{
		$siteInfo = Zend_Registry::get('siteInfo');
		$selector->where('subdomain = ?', $siteInfo['subdomain']);
	}
}