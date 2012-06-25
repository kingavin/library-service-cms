<?php
class Class_HTML
{
	protected static $_siteInfo = null;
	
	static function graphic($fileName, $nameSuffix = null)
    {
        if(is_null($nameSuffix)) {
            $fileName = $fileName.'.jpg';
            $missingName = '/images/graphic_missing.jpg';
        } else {
            $fileName = $fileName.'_'.$nameSuffix.'.jpg';
            $missingName = '/images/graphic_missing_'.$nameSuffix.'.jpg';
        }
        if(is_file(HTML_PATH.'/graphics'.$fileName)) {
            return '/graphics'.$fileName;
        } else {
            return $missingName;
        }
    }
    
    static function substr($str, $start, $length, $charset = "utf-8", $suffix = true)
	{
		if(function_exists("mb_substr")){
			if(mb_strlen($str, $charset) <= $length)
				return $str;
			$slice = mb_substr($str, $start, $length, $charset);
		} else {
			$re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
			$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
			$re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
			$re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
			preg_match_all($re[$charset], $str, $match);
			if(count($match[0]) <= $length)
				return $str;
			$slice = join("",array_slice($match[0], $start, $length));
		}
		if($suffix)
			return $slice."..";
		return $slice;
	}
	
	static function outputImage($url, $type = 'main')
	{
		if(is_null(self::$_siteInfo)) {
			self::$_siteInfo = Zend_Registry::get('siteInfo');
		}
		
		$urlArr = parse_url($url);
		if(isset($urlArr['host'])) {
			return $url;
		} else {
			return Class_Server::getImageUrl().'/'.$url;
		}
	}
}