<?php

require_once Yii::app()->basePath.'/vendor/autoload.php';

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!function_exists('getimagesizefromstring')) {
    function getimagesizefromstring($data)
    {
        $uri = 'data://application/octet-stream;base64,' . base64_encode($data);
        return getimagesize($uri);
    }
}

/**
 * Description of FacebookThumbnailComponent
 *
 * @author Jimmy
 */
 
class FacebookThumbnailComponent extends CApplicationComponent {
    
    const DEFAULT_FACEBOOK_THUMBNAIL_WIDTH = 1200;
    const DEFAULT_FACEBOOK_THUMBNAIL_HEIGHT = 630;
    const DEFAULT_WATERMARK_PATH = '/../img/watermark.png';
    
    // recommended facebook thumbnail width
    private $_facebookThumbnailWidth = self::DEFAULT_FACEBOOK_THUMBNAIL_WIDTH;
    // recommended facebook thumbnail height
    private $_facebookThumbnailHeight = self::DEFAULT_FACEBOOK_THUMBNAIL_HEIGHT;
    // path of watermark image (relative to Yii::app()->basePath)
    private $_watermarkPath = self::DEFAULT_WATERMARK_PATH;
    
    public function setFacebookThumbnailWidth($facebookThumbnailWidth) {
	$this->_facebookThumbnailWidth = $facebookThumbnailWidth;
    }

    public function setFacebookThumbnailHeight($facebookThumbnailHeight) {
	$this->_facebookThumbnailHeight = $facebookThumbnailHeight;
    }

    public function setWatermarkPath($watermarkPath) {
	$this->_watermarkPath = $watermarkPath;
    }
    
    public function renderWatermark() {
	readfile($this->getWatermarkAbsolutePath());
    }
    
    public function renderThumbnail($youtubeId) {
	// get the thumbnail path
	try {
	    $imageUrl = Yii::app()->youtube->getThumbnailPath($youtubeId);
	} catch (Exception $ex) {
	    $this->renderWaterMark();
	    return;
	}
	// read the image content from curl
	$ch = curl_init($imageUrl);
	if (!$ch) {
	    $this->renderWatermark();
	    return;
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$imageContent = curl_exec($ch);
	curl_close($ch);
	// read the image size
	list($width, $height, $type, $attr) = 
		getimagesizefromstring($imageContent);
	// calculate the result width and height
	$facebookWidthHeightRatio = 
		$this->_facebookThumbnailWidth / $this->_facebookThumbnailHeight;
	$widthHeightRatio = $width / $height;
	if ($widthHeightRatio / $facebookWidthHeightRatio > 1) {
	    // result height = FACEBOOK_HEIGHT
	    $resultHeight = $this->_facebookThumbnailHeight;
	    $resultWidth = $width * $this->_facebookThumbnailHeight / $height;
	} else {
	    $resultWidth = $this->_facebookThumbnailWidth;
	    $resultHeight = $height * $this->_facebookThumbnailWidth / $width;
	}
	// create result image
	$resultImage = imagecreatetruecolor($resultWidth, $resultHeight);
	// sample original image
	$resource = imagecreatefromstring($imageContent);
	imagecopyresampled($resultImage, $resource, 0, 0, 0, 0, 
		$resultWidth, $resultHeight, $width, $height);
	imagedestroy($resource);
	// watermark image
	list($watermarkWidth, $watermarkHeight, $watermarkType, $watermarkAttr) = 
		getimagesize($this->getWatermarkAbsolutePath());
	// calculate watermark position
	$distX = ($resultWidth - $watermarkWidth) / 2;
	$distY = ($resultHeight - $watermarkHeight) / 2;
	$watermark = imagecreatefrompng($this->getWatermarkAbsolutePath());
	imagecopy($resultImage, $watermark, $distX, $distY, 0, 0, $watermarkWidth, $watermarkHeight);
	imagedestroy($watermark);
	// output result image
	imagepng($resultImage);
	// remove result image
	imagedestroy($resultImage);
    }
    
    private function getWatermarkAbsolutePath() {
	return Yii::app()->basePath . $this->_watermarkPath;
    }
    
}
