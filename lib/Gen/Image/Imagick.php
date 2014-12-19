<?php
require_once('Gen/Image/Interface.php');

class Gen_Image_Imagick implements Gen_Image_Interface
	
	public static function getHeight($image)
	{
		return $image->getImageHeight();
	}
	
	public static function getWidth($image)
	{
		return $image->getImageWidth();
	}
	
	public static function create($filePath)
	{
		if (!file_exists($filePath)) {
			return false;
		}
		
		$image = new Imagick();
		$image->readImage($filePath);
		
		return $image;
	}
	
	public static function write($image, $filePath, $format, $quality = 90, $destroy = true)
	{
		$dir = dirname($filePath);
		
		if (!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}
		
		$image->setImageFormat(strtoupper($format));
			
		if ($format == 'jpeg' || $format == 'jpg') {
			$image->setImageCompression(Imagick::COMPRESSION_JPEG);
			$image->setImageCompressionQuality($quality);
		}
			
		$result = $image->writeImage($filePath);
				
		if ($destroy) {
			$image->destroy();
		}
		
		return $result;
	}
	
	public static function render($image, $format, $quality = 90)
	{
		switch ($format) {
			case 'jpg':
			case 'jpeg':
				header('Content-Type: image/jpeg');
				echo $image;
				break;
				
			case 'png':
				header('Content-Type: image/png');
				echo $image;
				break;
				
			case 'gif':
				header('Content-Type: image/gif');
				echo $image;
				break;
				
			default:
				require_once('Gen/Log.php');
				Gen_Log::log('Unsupported image format', 'Gen_Image_Gd::write', 'warning');
				$image->destroy();
				return false;
				break;
		}
		$image->destroy();
		return true;
	}
	
	public static function destroy($image)
	{
		return $image->destroy();
	}
	
	public static function crop($image, $width, $height = null, $x = null, $y = null)
	{
		$height = (null === $height) ? $width : $height;
		$sw = $image->getImageWidth();
		$sh = $image->getImageHeight();
		$x = ($x === null) ? intval(($sw - $width)/2) : $x;
		$y = ($y === null) ? intval(($sh - $height)/2) : $y;
		
		$result = $image->clone();
		$result->cropImage($width, $height, $x, $y);

		return $result;
	}
	
	public static function resize($image, $width, $height = null)
	{
		$height = (null === $height) ? $width : $height;
		
		$result = $image->clone();
		$result->resizeImage($width, $height);

		return $result;
	}
	
	public static function thumb($image, $width, $height = null)
	{
		$height = (null === $height) ? $width : $height;
		
		$result = $image->clone();
		$result->cropThumbnailImage($width, $height);

		return $result;
	}
}