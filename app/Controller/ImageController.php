<?php
require_once('ApplicationController.php');

class ImageController extends ApplicationController
{
	public function textAction()
	{
		$text = $this->getParam('txt', "Not Found");
		$width = (int) $this->getParam('width', 400);
		$height = (int) $this->getParam('height', $width);
		
		if($width<20) $width=20;
		if($height<20) $height=20;
		if($width>1000) $width=1000;
		if($height>1000) $height=1000;
		
		require_once('Gen/Image.php');
		$fileName = CACHE_DIR.'img/text/'.md5('image_text'.$text.$width.$height);
		if(!file_exists($fileName)) {
			$img = Gen_Image::createFromText($text, $width, $height);
			Gen_Image::write($img, $fileName, 'jpeg', 100, false);
		} else {
			$img = Gen_Image::create($fileName);
		}
		header('Content-Type: image/jpeg');
		imagejpeg($img, NULL, 90);
		imagedestroy($img);
		exit;
	}
	
	public function thumbAction()
	{
		$width = $this->getParam('width', 200);
		$height = $this->getParam('height', $width);
		$imgName = $this->getParam('file');
		
		$srcFile = IMG_DIR.$imgName;
		$destFile = CACHE_DIR.'img/thumb/'.md5($width.$height.$imgName).'.jpg';
		
		require_once('Gen/Image.php');
		if(!file_exists($destFile)) {
			
			if(!file_exists($srcFile)) {
				$img = Gen_Image::createFromText("404 Not Found", $width);
			} else {
				$img = Gen_Image::create($srcFile);
				$img = Gen_Image::thumb($img, $width, $height);
				Gen_Image::write($img, $destFile, 'jpeg', 100, false);
			}
		} else {
			$img = Gen_Image::create($destFile);
		}
		header('Content-Type: image/jpeg');
		imagejpeg($img, NULL, 90);
		imagedestroy($img);
		exit;
	}
	
	public function cropAction()
	{
		$width = $this->getParam('width', 200);
		$height = $this->getParam('height');
		$x = $this->getParam('x');
		$y = $this->getParam('y');
		$format = $this->getParam('format', 'jpg');
		$imgName = $this->getParam('file');
		
		$srcFile = IMG_DIR.$imgName;
		$destFile = CACHE_DIR.'img/crop/'.md5($width.$height.$x.$y.$imgName.$format).'.'.$format;
		
		require_once('Gen/Image.php');
		if(!file_exists($destFile))
		{
			
			if(!file_exists($srcFile)) {
				$img = Gen_Image::createFromText("404 Not Found", $width);
			} else {
				$img = Gen_Image::create($srcFile);
				$img = Gen_Image::crop($img, $width, $height, $x, $y);
				Gen_Image::write($img, $destFile, $format, 100, false);
			}
		} else {
			$img = Gen_Image::create($destFile);
		}
		Gen_Image::render($img, $format);
		exit;
	}
	
	public function resizeAction()
	{
		$width = $this->getParam('width', 200);
		$height = $this->getParam('height');
		$format = $this->getParam('format', 'jpeg');
		$imgName = $this->getParam('file');
		
		$srcFile = IMG_DIR.$imgName;
		$destFile = CACHE_DIR.'img/resize/'.md5($width.$height.$imgName.$format).'.'.$format;
		
		require_once('Gen/Image.php');
		if(!file_exists($destFile))
		{
			
			if(!file_exists($srcFile)) {
				$img = Gen_Image::createFromText("404 Not Found", $width);
			} else {
				$img = Gen_Image::create($srcFile);
				$img = Gen_Image::resize($img, $width, $height);
				Gen_Image::write($img, $destFile, $format, 100, false);
			}
		} else {
			$img = Gen_Image::create($destFile);
		}
		Gen_Image::render($img, $format);
		exit;
	}
}