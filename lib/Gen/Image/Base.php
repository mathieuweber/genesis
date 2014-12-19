<?php

class Gen_Image_Base {
	
	const FORMAT_BMP  = 'bmp';
	const FORMAT_GIF  = 'gif';
	const FORMAT_JPEG = 'jpeg';
	const FORMAT_PNG  = 'png';
	
	public static $fontDir;
	
	protected static $_supported_formats = array(
		self::FORMAT_BMP, self::FORMAT_GIF, self::FORMAT_JPEG, self::FORMAT_PNG
	);
	
	protected static $imagick = null;
	
	protected static $gd = null;
	
	public static function hasGd()
	{
		if (self::$gd === null) {
			self::$gd = extension_loaded('gd');
		}
		
		return self::$gd;
	}
	
	public static function hasImagick()
	{
		if (self::$imagick === null) {
			self::$imagick = extension_loaded('imagick');
		}
		
		return self::$imagick;
	}
	
	public static function getHeight($image)
	{
		if (self::hasImagick()) {
			$height = $image->getImageHeight();
		} elseif (self::hasGd()) {
			$height = imagesy($image);
		}
		
		return $height;
	}
	
	public static function getWidth($image)
	{
		if (self::hasImagick()) {
			$width = $image->getImageWidth();
		} elseif (self::hasGd()) {
			$width = imagesx($image);
		}
		
		return $width;
	}
	
	public static function create($filePath)
	{
		if (!file_exists($filePath)) {
			return false;
		}
		
		if (self::hasImagick()) {
			$image = new Imagick();
			$image->readImage($filePath);
		} elseif (self::hasGd()) {
			$imageInfo = getimagesize($filePath);
			if ($imageInfo === false) return false;
			
			switch ($imageInfo['mime']) {
				case 'image/bmp':
					$image = self::imageCreateFromBmp($filePath);
					break;
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
					return false;
			}
		}
		
		return $image;
	}
	
	public static function write($image, $filePath, $format, $quality = 90, $destroy = true)
	{
		$success = false;
		
		$dir = dirname($filePath);
		
		if (!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}
		
		if (self::hasImagick()) {
			$image->setImageFormat(strtoupper($format));
			
			if ($format == self::FORMAT_JPEG) {
				$image->setImageCompression(Imagick::COMPRESSION_JPEG);
				$image->setImageCompressionQuality($quality);
			}
			
			$success = $image->writeImage($filePath);
		} elseif (self::hasGd()) {
			switch ($format) {
				case self::FORMAT_JPEG:
					$success = imageJpeg($image, $filePath, $quality);
					break;
					
				case self::FORMAT_PNG:
					$success = imagepng($image, $filePath, $quality);
					break;
					
				case self::FORMAT_GIF:
					$success = imagegif($image, $filePath);
					break;
					
				default:
					break;
			}
		}
		
		if ($destroy) {
			self::destroy($image);
		}
		
		return $success;
	}
	
	public static function destroy($image)
	{
		if (self::hasImagick()) {
			$image->destroy();
		} elseif (self::hasGd()) {
			imageDestroy($image);
		}
		
		return true;
	}
	
	public static function resize($image0, $width, $height)
	{
		if (self::hasImagick()) {
			$image = $image0->clone();
			$image->cropThumbnailImage($width, $height);
		} elseif (self::hasGd()) {
			$dr = $height/$width;
			
			$sw = imagesx($image0);
			$sh = imagesy($image0);
			
			if ($sw == 0) return false;
			
			$sr = $sh / $sw;
			
			$image = imagecreatetruecolor($width, $height);
			$white = imagecolorallocate($image, 255, 255, 255);
			imagefill($image, 0, 0, $white);
			
			if ($sr == $dr) {
				$dx = $dy = 0;
				$rw = $width;
				$rh = $height;
			} elseif ($sr > $dr) {
				$rw = $width;
				$rh = intval($sr * $rw);
				$dy = intval(($height - $rh) / 2);
				$dx = 0;
			} else {
				$rh = $height;
				$rw = intval($rh / $sr);
				$dx = intval(($width - $rw) / 2);
				$dy = 0;
		   }
		   
		   imagecopyresampled($image, $image0, $dx, $dy, 0, 0, $rw, $rh, $sw, $sh);
		}
		
		return $image;
	}
	
	//Utility ? Currently unused
	public static function imageCopyResampledBicubic(&$dst_image, &$src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
	{
		$rX = $src_w / $dst_w;
		$rY = $src_h / $dst_h;
		
		if ($rX < 1 && $rY < 1) {
			return imagecopyresized($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
		}
		
		// we should first cut the piece we are interested in from the source
		$src_img = imagecreatetruecolor($src_w, $src_h);
		imagecopy($src_img, $src_image, 0, 0, $src_x, $src_y, $src_w, $src_h);

		// this one is used as temporary image
		$dst_img = imagecreatetruecolor($dst_w, $dst_h);

		imagepalettecopy($dst_img, $src_img);
		$w = 0;
		for ($y = 0; $y < $dst_h; $y++)  {
			$ow = $w; $w = round(($y + 1) * $rY);
			$t = 0;
			for ($x = 0; $x < $dst_w; $x++)  {
				$r = $g = $b = 0; $a = 0;
				$ot = $t; $t = round(($x + 1) * $rX);
				for ($u = 0; $u < $w - $ow; $u++)  {
					for ($p = 0; $p < $t - $ot; $p++)  {
						$c = imagecolorsforindex($src_img, imagecolorat($src_img, $ot + $p, $ow + $u));
						$r += $c['red'];
						$g += $c['green'];
						$b += $c['blue'];
						$a++;
					}
				}
				if($a) {
					imagesetpixel($dst_img, $x, $y, imagecolorclosest($dst_img, $r / $a, $g / $a, $b / $a));
				}
			}
		}

		// apply the temp image over the returned image and use the destination x,y coordinates
		imagecopy($dst_image, $dst_img, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h);

		// we should return true since ImageCopyResampled/ImageCopyResized do it
		return true;
	}
	
	/**
	 * Creates a bmp image ressource from a filename
	 * credit to alexander@alexauto.nl
	 * script found on http://www.php.net
	 */
	public static function imageCreateFromBmp($fileName)
	{
		// Load the image into a string
		$file = fopen($fileName,"rb");
		$read = fread($file,10);
		while (!feof($file) && ($read<>"")) {
			$read .= fread($file,1024);
		}
	   
		$temp   =	unpack("H*", $read);
		$hex	=	$temp[1];
		$header =	substr($hex, 0, 108);
	   
		// Process the header
		// Structure: http://www.fastgraph.com/help/bmp_header_format.html
		if (substr($header, 0, 4) == "424d") {
			// Cut it in parts of 2 bytes
			$header_parts = str_split($header, 2);
		   
			// Get the width 4 bytes
			$width = hexdec($header_parts[19].$header_parts[18]);
		   
			// Get the height 4 bytes
			$height = hexdec($header_parts[23].$header_parts[22]);
		   
			// Unset the header params
			unset($header_parts);
		}
	   
		// Define starting X and Y
		$x = 0;
		$y = 1;
	   
		// Create newimage
		$image = imagecreatetruecolor($width,$height);
	   
		// Grab the body from the image
		$body = substr($hex,108);

		// Calculate if padding at the end-line is needed
		// Divided by two to keep overview.
		// 1 byte = 2 HEX-chars
		$body_size   = (strlen($body)/2);
		$header_size = ($width * $height);

		// Use end-line padding? Only when needed
		$usePadding = ($body_size > ($header_size * 3) +4);
	   
		/**
		 * Using a for-loop with index-calculation instead of str_split
		 * to avoid large memory consumption
		 * Calculate the next DWORD-position in the body
		 */
		for ($i=0; $i < $body_size; $i += 3) {
			// Calculate line-ending and padding
			if ($x >= $width) {
				// If padding needed, ignore image-padding
				// Shift i to the ending of the current 32-bit-block
				if ($usePadding) {
					$i += $width%4;
				}
				// Reset horizontal position
				$x = 0;
			   
				// Raise the height-position (bottom-up)
				$y++;
			   
				// Reached the image-height? Break the for-loop
				if ($y > $height) {
					break;
				}
			}
		   
			// Calculation of the RGB-pixel (defined as BGR in image-data)
			// Define $i_pos as absolute position in the body
			$i_pos	=	$i*2;
			$r		=	hexdec($body[$i_pos+4].$body[$i_pos+5]);
			$g		=	hexdec($body[$i_pos+2].$body[$i_pos+3]);
			$b		=	hexdec($body[$i_pos].$body[$i_pos+1]);
		   
			// Calculate and draw the pixel
			$color	=	imagecolorallocate($image, $r, $g, $b);
			imagesetpixel($image, $x, $height-$y, $color);
		   
			// Raise the horizontal position
			$x++;
		}
	   
		// Unset the body / free the memory
		unset($body);
	   
		// Return image-object
		return $image;
	}
}