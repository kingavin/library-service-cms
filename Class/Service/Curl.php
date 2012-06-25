<?php
class Class_Service_Curl
{
	public function putFile($file, $filepath, $host)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $host.'/upload.php');
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
		curl_setopt($ch, CURLOPT_POST, true);
		$post = array(
			"file" => '@'.$file,
			"filepath" => $filepath
		);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
		return $response = curl_exec($ch);
	}
	
	public function get($post, $url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
		curl_setopt($ch, CURLOPT_POST, true);
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
		return $response = curl_exec($ch);
	}
}