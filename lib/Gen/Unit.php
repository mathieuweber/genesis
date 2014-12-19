<?php
/**
 * @category   Gen
 * @package	Gen_Unit
 */
class Gen_Unit
{
	const MM 	= 'mm';
	const CM 	= 'cm';
	const INCH 	= 'in';
	const PIXEL = 'px';
	const PT 	= 'pt';
	
	const DIGITAL_DPI = 72;
	const PRINT_DPI = 300;
	
	//To pixel conversions
	public static function mmToPixel($value, $dpi)
	{
		return round(self::mmToInch($value) * $dpi);
	}
	
	public static function inchToPixel($value, $dpi)
	{
		return round($value * $dpi);
	}
	
	public static function ptToPixel($value, $dpi)
	{
		return round($value * $dpi/72);
	}
	
	//From pixel conversions
	public static function pixelToMm($value, $dpi)
	{
		return self::inchToMm($value / $dpi);
	}
	
	public static function pixelToInch($value, $dpi)
	{
		return $value / $dpi;
	}
	
	public static function pixelToPt($value, $dpi)
	{
		return Math.round($value * 72/$dpi);
	}
	
	//Inch <-> Mm
	public static function mmToInch($value)
	{
		return $value / 25.4;
	}
	
	public static function inchToMm($value)
	{
		return $value * 25.4;
	}
	
	//Resolution
	public static function getResolution($pixel, $mm)
	{
		return $pixel / self::mmToInch($mm);
	}
}