<?php
require_once('Gen/Image/Base.php');

class Gen_Image extends Gen_Image_Base
{
	//Resize the image to match the new width. Same ratio as original. Do not crop height.
	public static function resizeWidth($image0, $width)
	{
		if (self::hasImagick()) {
			$image = $image0->clone();
			$image->thumbnailImage($width, 0);
		} elseif (self::hasGd()) {
			$ratio = self::getRatio($image0);
			$image = self::resize($image0, $width, $width/$ratio);
		}
		
		return $image;
	}
	
	//Same for height
	public static function resizeHeight($image0, $height)
	{
		if (self::hasImagick()) {
			$image = $image0->clone();
			$image->thumbnailImage(0, $height);
		} elseif (self::hasGd()) {
			$ratio = self::getRatio($image0);
			$image = self::resize($image0, $height*$ratio, $height);
		}
		
		return $image;
	}
	
	public static function getRatio($image)
	{
		$width = self::getWidth($image);
		$height = self::getHeight($image);

		if (!$width || !$height) {
			return false;
		}
		
		return $width/$height;
	}
	
	public static function squarify($image0, $length)
	{
		if (self::hasImagick()) {
			$image = self::resize($image0, $length, $length);
		} elseif (self::hasGd()) {
			$sx = imageSx($image0);
			$sy = imageSy($image0);
		
			$max = max($sx, $sy);
			$min = min($sx, $sy);
			$diff = $max - $min;
		
			if ($sx >= $sy) {
				$dx = intval($diff / 2);
				$dy = 0;
			} else {
				$dx = 0;
				$dy = intval($diff / 2);
			}
		
			$image = imagecreatetruecolor($length,$length);

			imagecopyresampled($image, $image0, 0, 0, $dx, $dy, $length, $length, $min, $min);
		}
		
		return $image;
	}
	
	public static function getIccProfile($key)
	{
		$path = MS_LIB_DIR.'/Gen/Image/ICC_Profiles/'.$key.'.icc';
		
		if (file_exists($path)) {
			return file_get_contents($path);
		}
	}
	
	public static function clean($image, $gray=false)
	{
		if (self::hasImagick()) {
			if (!self::isJpeg($image)) {
				$image->setImageFormat('JPEG');
			}
			
			if ($gray) {
				$image->profileImage('icc', self::getIccProfile('ISOcoated_v2_grey1c_bas'));
				$image->setImageType(Imagick::IMGTYPE_GRAYSCALE);
				$image->setImageColorspace(Imagick::COLORSPACE_GRAY);
			} else {
				$image->profileImage('icc', self::getIccProfile('eciRGB_v2'));
				$image->setImageType(Imagick::IMGTYPE_TRUECOLOR);
				$image->setImageColorspace(Imagick::COLORSPACE_RGB);
			}
		}
		
		return $image;
	}
	
	public static function getJpegCompressionQuality($file)
	{
		$cmd = MS_ROOT_DIR . 'shell/jpegquality ' . $file;
		$shOutput = shell_exec($cmd);
		$pos = strrpos ($shOutput , 'Average quality');
		
		if ($pos !== false) {
			$sub = trim(substr($shOutput, $pos));
			$cQ = preg_filter('#Average quality: [0-9\.]+% \(([0-9]+)%\)#', '$1', $sub);
		} else {
			$cQ = false;
		}
		
		if (!$cQ || !is_numeric($cQ) || $cQ < 0) {
			//If quality cannot be estimated, return 100
			$cQ = 100;
		}
		
		return $cQ;
	}
	
	public static function isJpeg($image)
	{
		if (self::hasImagick()) {
			return $image->getImageFormat() == 'JPEG';
		} else {
			//... GD cannot check his own image type...
			return false;
		}
	}
	
	public static function saveForWeb($src, $dest, $max = 1000, $quality = 90)
	{
		$img = self::create($src);
		$width = self::getWidth($img);
		$height = self::getHeight($img);
		if($width >= $height && $width > $max) {
			$img = self::resizeWidth($img, $max);
		} elseif($height > $max) {
			$img = self::resizeHeight($img, $max);
		}
		return self::write($img, $dest, 'jpeg', $quality, true);
	}
}