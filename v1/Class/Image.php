<?php
class Class_Image
{
    const TYPE_JPG = 'jpg';
    const TYPE_GIF = 'gif';
    const TYPE_PNG = 'png';
    
    const RIGHTCORNER = 'rightCorner';
    const CENTER = 'center';
    const BOTTOM = 'bottom';
    
    protected $_im = null;
    protected $_imOrigin = null;
    protected $_waterMark = null;
    protected $_waterPos = null;
    
    public function setWaterMark($filePath, $pos = 'rightCorner')
    {
        $this->_waterMark = imagecreatefromgif($filePath);
        $this->_waterPos = $pos;
        
    	if (!is_null($this->_waterMark) && !is_null($this->_imOrigin)) {
            $watermarkWidth = imagesx($this->_waterMark);
            $watermarkHeight = imagesy($this->_waterMark);
            $imageWidth = imagesx($this->_imOrigin);
            $imageHeight = imagesy($this->_imOrigin);
            switch($this->_waterPos) {
                case 'center':
                    $dest_x = ($imageWidth - $watermarkWidth)/2;
                    $dest_y = ($imageHeight - $watermarkHeight)/2;
                    break;
                case 'rightCorner':
                    $dest_x = $imageWidth - $watermarkWidth - 5;
                    $dest_y = $imageHeight - $watermarkHeight - 5;
                    break;
                case 'bottom' :
                	$dest_x = ($imageWidth - $watermarkWidth)/2;
                    $dest_y = ($imageHeight - $watermarkHeight)/2+$imageHeight/4;
                    break;
            }
            imagecopymerge($this->_imOrigin, $this->_waterMark, $dest_x, $dest_y, 0, 0, $watermarkWidth, $watermarkHeight,40);  
            imagedestroy($this->_watermark);
        }else{
        	throw new Zend_Exception('waterMark or img not found!');
        }
        return $this;
    }
    
    protected function _filenameToMime($filename)
    {
        $types = array(
            'jpg' => self::TYPE_JPG,
            'gif' => self::TYPE_GIF,
            'png' => self::TYPE_PNG
        );
        foreach($types as $extension => $mime) {
            if(preg_match('/\.'.$extension.'$/', $filename)) {
                return $mime;   
            }
        }
        throw new Zend_Exception('Unknown image type.');
    }
    
    public function readImage($filename, $type = NULL)
    {
        if($type === NULL) {
            $type = $this->_filenameToMime($filename);
        }
        switch($type) {
            case self::TYPE_JPG:
                $this->_imOrigin = imagecreatefromjpeg($filename);
                break;
            case self::TYPE_GIF:
                $this->_imOrigin = imagecreatefromgif($filename);
                break;
            case self::TYPE_PNG:
                $this->_imOrigin = imagecreatefrompng($filename);
                break;
            default:
                break;
        }
        $this->_im = null;
        
        
        return $this;
    }
    
    public function writeImage($filename, $type = NULL)
    {
        if(is_null($this->_imOrigin)) {
            throw new Exception('image file not found');
        }
        if(is_null($this->_im)) {
            $this->_im = $this->_imOrigin;
        }
        
        if ($type === NULL) {
            $type = $this->_filenameToMime($filename);
        }
        
        switch ($type) {
            case self::TYPE_JPG:
                imagejpeg($this->_im, $filename, 100);
                break;
            case self::TYPE_GIF:
                imagegif($this->_im, $filename, 100);
                break;
            case self::TYPE_PNG:
                imagepng($this->_im, $filename, 100);
                break;
            default:
                break;
        }
        return $this;
    }
    
    public function resize($width = NULL, $height = NULL, $fit = NULL)
    {
        if(is_null($this->_imOrigin)) {
            throw new Exception('image file not found');
        }
        $fit = (bool) $fit;
        
        if ($width === NULL || $height === NULL) {
            return $this;
        }
        
        $origX = imagesx($this->_imOrigin);
        $origY = imagesy($this->_imOrigin);
        
        $this->_im = imagecreatetruecolor($width, $height);
        imagecopyresampled($this->_im, $this->_imOrigin, 0, 0, 0, 0, $width, $height, $origX, $origY);
        return $this;
    }
}