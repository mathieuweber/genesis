<?php

require_once('Tcpdf/tcpdf.php');

class Gen_Pdf extends TCPDF
{
	const H_ALIGN_LEFT   = 'L';
	const H_ALIGN_RIGHT  = 'R';
	const H_ALIGN_CENTER = 'C';
	const H_ALIGN_JUSTIFY = 'J';
	
	const V_ALIGN_TOP    = 'T';
	const V_ALIGN_BOTTOM = 'B';
	const V_ALIGN_MIDDLE = 'M';
	
	const ORIENTATION_PORTRAIT = 'P';
	const ORIENTATION_LANDSCAPE = 'L';
	
	const UNIT_MM = 'mm';
	
	protected $_colorspace = 'cmyk';

	public function __construct ($orientation=self::ORIENTATION_PORTRAIT, $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false)
	{
		parent::__construct ($orientation, $unit, $format, $unicode, $encoding, $diskcache);
	}
	
	public function setBottomCellMargin($value)
	{
		$this->setCellMargin('B', $value);
	}
	
	public function setTopCellMargin($value)
	{
		$this->setCellMargin('T', $value);
	}
	
	public function setRightCellMargin($value)
	{
		$this->setCellMargin('R', $value);
	}
	
	public function setLeftCellMargin($value)
	{
		$this->setCellMargin('L', $value);
	}
	
	public function setCellMargin($margin, $value)
	{
		$margins = $this->getCellMargins();
		if (isset($margins[$margin])) $margins[$margin] = $value;
		
		$this->setCellMargins($margins['L'], $margins['T'], $margins['R'], $margins['B']);
	}
	
	public function getBox($key, $page = null)
	{
		$dim = $this->getPageDimensions($page);
		if(!isset($dim[$key])) { return false; }
		
		$k = $this->k;
		$box = $dim[$key];
		return array(
			'llx' => $box['llx'] / $k,
			'lly' => $box['lly'] / $k,
			'urx' => $box['urx'] / $k,
			'ury' => $box['ury'] / $k,
			'width' => ($box['urx'] - $box['llx']) / $k,
			'height' => ($box['ury'] - $box['lly']) / $k
		);
	}

	public function setColorspace($colorspace)
	{
		$this->_colorspace = $colorspace;
	}

	/* Color conversion */
	public function convertHTMLColorToDec($hex='#FFFFFF')
	{
		switch($this->_colorspace)
		{
			case 'rgb':
				return $this->hex2rgb($hex);
			break;
			case 'gray':
				return $this->hex2gray($hex);
			break;
			case 'cmyk':
			default:
				return $this->hex2cmyk($hex);
			break;
		}
	}

	protected function hex2rgb($hex)
	{
	   $color = str_replace('#','',$hex);
	   $rgb = array(hexdec(substr($color,0,2)), hexdec(substr($color,2,2)), hexdec(substr($color,4,2)));
	   return $rgb;
	}

	protected function rgb2cmyk($rgb)
	{
		$r = $rgb[0];
		$g = $rgb[1];
		$b = $rgb[2];
		$cyan    = 255 - $r;
		$magenta = 255 - $g;
		$yellow  = 255 - $b;
		$black  = min($cyan, $magenta, $yellow);
		$cyan    = @(($cyan    - $black) / (255 - $black)) * 255;
		$magenta = @(($magenta - $black) / (255 - $black)) * 255;
		$yellow  = @(($yellow  - $black) / (255 - $black)) * 255;
		return array(round(100 * $cyan / 255), round(100 * $magenta / 255), round(100 * $yellow / 255), round(100 * $black / 255));
	}

	protected function hex2cmyk($hex)
	{
		return $this->rgb2cmyk($this->hex2rgb($hex));
	}

	protected function rgb2gray($rgb)
	{
		$r = $rgb[0];
		$g = $rgb[1];
		$b = $rgb[2];

		$g = round(($r+$g+$b)/3);
		return array($g, $g, $g);
	}

	protected function hex2gray($hex)
	{
		return $this->rgb2gray($this->hex2rgb($hex));
	}
}
