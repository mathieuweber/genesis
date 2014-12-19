<?php
require_once('Gen/Image/Interface.php');

class Gen_Image_Gd implements Gen_Image_Interface
{
	public static function getHeight($image)
	{
		$height = imagesy($image);
	}
	
	public static function getWidth($image)
	{
		$width = imagesx($image);
	}
	
	public static function getFormat($filePath)
	{
		$imageInfo = getimagesize($filePath);
		if ($imageInfo === false) {
			return false;
		}
		switch ($imageInfo['mime'])
		{
			case 'image/gif':
				return 'gif';
				break;
				
			case 'image/jpeg':
				return 'jpg';
				break;
				
			case 'image/png':
				return 'png';
				break;
		}
		return false;
	}
	
	public static function create($filePath)
	{
		if (!file_exists($filePath)) {
			return false;
		}
		
		$imageInfo = getimagesize($filePath);
		if ($imageInfo === false) {
			return false;
		}
		
		switch ($imageInfo['mime']) {
			case 'image/gif':
				$image = imagecreatefromgif($filePath);
				break;
			case 'image/jpeg':
				$image = imagecreatefromjpeg($filePath);
				break;
			case 'image/png':
				$image = imagecreatefrompng($filePath);
				break;
			default:
				throw new Exception('Unsupported image format in Gen_Image_Gd::create');
				return false;
		}
		
		return $image;
	}
	
	public static function write($image, $filePath, $format, $quality = 90, $destroy = true)
	{
		$result = false;
		
		$dir = dirname($filePath);
		
		if (!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}

		switch ($format) {
			case 'jpg':
			case 'jpeg':
			
				/** flattern image on white background for PGN **/
				$width = imagesx($image);
				$height = imagesy($image);
				$tmp = imagecreatetruecolor($width, $height);
				$white = imagecolorallocate($tmp, 255, 255, 255);
				imagefill($tmp, 0, 0, $white);
				imagecopyresampled($tmp, $image, 0, 0, 0, 0, $width, $height, $width, $height);
				
				$result = imagejpeg($tmp, $filePath, $quality);
				break;
				
			case 'png':
				$quality = round(9*(1 - $quality/100));
				$result = imagepng($image, $filePath, $quality);
				break;
				
			case 'gif':
				$result = imagegif($image, $filePath);
				break;
				
			default:
				throw new Exception('Unsupported image format in Gen_Image_Gd::create');
				break;
		}
		
		if ($destroy) {
			imageDestroy($image);
		}
		
		return $result;
	}
	
	public static function render($image, $format, $quality = 90)
	{
		switch ($format) {
			case 'jpg':
			case 'jpeg':
				header('Content-Type: image/jpeg');
				$result = imagejpeg($image, null, $quality);
				break;
				
			case 'png':
				header('Content-Type: image/png');
				$quality = round(9*(1 - $quality/100));
				$result = imagepng($image, null, $quality);
				break;
				
			case 'gif':
				header('Content-Type: image/gif');
				$result = imagegif($image, null);
				break;
				
			default:
				require_once('Gen/Log.php');
				Gen_Log::log('Unsupported image format', 'Gen_Image_Gd::write', 'warning');
				$result = false;
				break;
		}
		
		imageDestroy($image);
	
		return $result;
	}
	
	public static function destroy($image)
	{
		return imageDestroy($image);
	}
	
	public static function crop($image, $width, $height = null, $x = null, $y = null)
	{
		$height = ($height === null) ? $width : $height;
		
		$sw = imagesx($image);
		$sh = imagesy($image);
		
		$sx = ($x === null) ? round(($sw - $width)/2) : $x;
		$sy = ($y === null) ? round(($sh - $height)/2) : $y;
		$dx = $dy = 0;
		
		$crop = imagecreatetruecolor($width, $height);
		$white = imagecolorallocate($crop, 255, 255, 255);
		imagefill($crop, 0, 0, $white);
			
		imagecopyresampled($crop, $image, $dx, $dy, $sx, $sy, $width, $height, $width, $height);
		
		return $crop;
	}
	
	public static function resize($image, $width, $height = null)
	{
		$sw = imagesx($image);
		$sh = imagesy($image);
		
		if($sh == 0 || $sw == 0) {
			return false;
		}
		
		$sr = round($sh/$sw, 2);
		
		$width = (int) $width;
		$height = ($height === null) ? round($width * $sr) : $height;
		$dr = round($height/$width, 2);
		
		if($sr < $dr) {
			$dw = $width;
			$dh = round($sr * $dw);
			$dy = round(($height - $dh) / 2);
			$dx = 0;
		} elseif($sr > $dr) {
			$dh = $height;
			$dw = round($dh / $sr);
			$dx = round(($width - $dw) / 2);
			$dy = 0;
		} else {
			$dx = $dy = 0;
			$dw = $width;
			$dh = $height;
		}
		
		$sx = $sy = 0;
		
		$resize = imagecreatetruecolor($width, $height);
		$white = imagecolorallocate($resize, 255, 255, 255);
		imagefill($resize, 0, 0, $white);
		
		imagecopyresampled($resize, $image, $dx, $dy, $sx, $sy, $dw, $dh, $sw, $sh);
		return $resize;
	}
	
	public static function thumb($image, $width, $height = null)
	{
		if(null === $height) {
			$height = $width;
		}
		
		$dr = round($height/$width, 2);
		
		$sw = imagesx($image);
		$sh = imagesy($image);
		
		if ($sw == 0) {
			return false;
		}
		
		$sr = round($sh/$sw, 2);
		
		$thumb = imagecreatetruecolor($width, $height);
		$white = imagecolorallocate($thumb, 255, 255, 255);
		imagefill($thumb, 0, 0, $white);
		
		if ($sr == $dr) {
			$dx = $dy = 0;
			$rw = $width;
			$rh = $height;
		} elseif ($sr > $dr) {
			$rw = $width;
			$rh = round($sr * $rw);
			$dy = round(($height - $rh) / 2);
			$dx = 0;
		} else {
			$rh = $height;
			$rw = round($rh / $sr);
			$dx = round(($width - $rw) / 2);
			$dy = 0;
	   }
	   
	   imagecopyresampled($thumb, $image, $dx, $dy, 0, 0, $rw, $rh, $sw, $sh);
		
		return $thumb;
	}
	
	public static function createFromText($text, $width = 400, $height=null, $color=null)
	{
		$colors = array(
			array('r' => 75, 'g' => 170, 'b' => 228), //'Drops Books Blue'
			array('r' => 204, 'g' => 255, 'b' => 102), //'Green'  
			array('r' => 255, 'g' => 140, 'b' => 179), //'Purple'
			array('r' => 255, 'g' => 153, 'b' => 102), //   
			array('r' => 255, 'g' => 214, 'b' => 83), //'Yellow'  
			array('r' => 255, 'g' => 50, 'b' => 55), //'Youtube Red'
			array('r' => 255, 'g' => 245, 'b' => 204), //'Beige 
			array('r' => 255, 'g' => 140, 'b' => 179), //'Beige   
			array('r' => 200, 'g' => 208, 'b' => 247), //'Beige     
			array('r' => 238, 'g' => 197, 'b' => 247), //   
			array('r' => 232, 'g' => 247, 'b' => 197), //     
			array('r' => 201, 'g' => 174, 'b' => 222), //       
		);
		
		if($height === null) {
			$height = $width;
		}
		
		if($color === null) {
			$color = $colors[rand(0,count($colors)-1)];
		}
	
		/** clean text **/
		$text = mb_strtoupper(str_replace(array(',', ':', ';', '-', '_'), ' ', $text));
		$text = preg_replace('@ +@', ' ', $text);
		
		/** params */
		$font = realpath(dirname(__FILE__)).'/font/GothamBook.ttf';
		$charCount = strlen($text);
		$maxCharCount = round($width / round(sqrt($width * $height / $charCount)));
		$xMargin = 0.05 * $width;
		$yMargin = 0.05 * $height;
		
		/** compute lines **/
		$lines = array();
		$line = '';
		foreach(explode(' ', $text) as $word) {
			$line .= ($line ? ' ' : '') . $word;
			if(strlen($line) <= $maxCharCount) {
				continue;
			}
			$lines[] = $line;
			$line = '';
		}
		if($line != '') {$lines[] = $line;}

		/** determine word width & height **/
		$wordCount = count($lines);
		$maxWordHeight = ($height - ($wordCount + 1) *$yMargin)/$wordCount;
		$maxWordWidth = $width - 2 * $xMargin;
		$defaultFontSize = $maxWordHeight/0.97; // default font fits height
		$angle = 0;

		$img = imagecreatetruecolor($width, $height);
		$background_color = imagecolorallocate($img, $color['r'], $color['g'], $color['b']);
		imagefill($img, 0, 0, $background_color);
		$textColor = imagecolorallocate($img, 0, 0, 0);

		$results = array();
		$totalHeight = 0;
		foreach($lines as $i => $word)
		{	
			/** try width default font **/
			$word = trim($word);
			$box = @imageTTFBbox($defaultFontSize,$angle,$font,$word);
			$textWidth = abs($box[2] - $box[0]);
			$textHeight = abs($box[5] - $box[1]);
			
			/** if the width is to large, resize **/
			$fontSize = $defaultFontSize;
			if($textWidth > $maxWordWidth) {
				$fontSize = $defaultFontSize * $maxWordWidth / $textWidth;
				$box = @imageTTFBbox($fontSize,$angle,$font,$word);
				$textWidth = abs($box[2] - $box[0]);
				$textHeight = abs($box[5] - $box[1]);
			}
			
			$x = ($width - $textWidth)/2; // center the word
			$totalHeight += $yMargin  + $textHeight; // keep track of height for next word
			$results[] = array('x' => $x, 'width' => $textWidth, 'height' => $textHeight, 'font_size' => $fontSize, 'text' => $word);
		}

		//$borderColor = imagecolorallocate($img, 255, 0, 0);

		$y = ($height - $totalHeight + $yMargin)/2;
		foreach ($results as $i => $row) {
			//imagerectangle($img, $row['x'], $y, $row['x'] + $row['width'], $y + $row['height'], $borderColor);
			$y += $row['height'];
			imagettftext($img, $row['font_size'], $angle, $row['x']-0.05*$row['font_size'], $y, $textColor, $font, $row['text']);
			$y += $yMargin;
		}

		return $img;
	}
}