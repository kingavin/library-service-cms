<?php
class Class_Plugin
{
	public function run($type, $action, $selector = null)
	{
		$plugin = new Class_Plugin_Subdomain();
		$plugin->$action($selector);
	}
}