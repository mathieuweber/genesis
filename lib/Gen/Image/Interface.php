<?php
interface Gen_Image_Interface {
	
	public static function getHeight($image);
	
	public static function getWidth($image);
	
	public static function create($filePath);
		
	public static function write($image, $filePath, $format, $quality = 90, $destroy = true);
	
	public static function destroy($image);
	
	public static function thumb($image, $width, $height = null);
	
	public static function crop($image, $width, $height = null, $x = null, $y = null);
	
	public static function resize($image, $width, $height = null);
	
}