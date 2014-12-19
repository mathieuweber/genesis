<?php
class Gen_Image
{
	protected static $_adapter;
	
	protected static function getAdapter()
	{
		if(null === self::$_adapter){
			if(extension_loaded('gd')) {
				require_once('Gen/Image/Gd.php');
				self::$_adapter = 'Gen_Image_Gd';
			} elseif(extension_loaded('imagick')) {
				require_once('Gen/Image/Imagick.php');
				self::$_adapter = 'Gen_Image_Imagick';
			} else {
				throw new Exception("No Graphical Library found in Gen_Image::getAdapter()");
			}
		}
		return self::$_adapter;
	}
	
	public static function __callStatic($method, $args)
	{
		$adapter = self::getAdapter();
		if(!method_exists($adapter, $method)) {
			throw new Exception('Call to undefined function `' . $method . '` for adapter `'. $adapter .'` in Gen_Image');
		}
		return call_user_func_array(array($adapter, $method), $args);
	}
	
	public static function getRatio($image)
	{
		$width = self::getWidth($image);
		$height = self::getHeight($image);

		if (!$width || !$height) {
			return false;
		}
		
		return $height/$width;
	}
	
	//Resize the image to match the new width. Same ratio as original. Do not crop height.
	public static function resizeWidth($img, $width)
	{
		$ratio = self::getRatio($img);
		return self::resize($img, $width, $width * $ratio);
	}
	
	//Same for height
	public static function resizeHeight($img, $height)
	{
		$ratio = self::getRatio($image0);
		return self::resize($image0, $height / $ratio, $height);
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