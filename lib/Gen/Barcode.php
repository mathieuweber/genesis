<?php

class Gen_Barcode
{
	protected static $a_code = array(
		0 => array(0,0,0,1,1,0,1),
		1 => array(0,0,1,1,0,0,1),
		2 => array(0,0,1,0,0,1,1),
		3 => array(0,1,1,1,1,0,1),
		4 => array(0,1,0,0,0,1,1),
		5 => array(0,1,1,0,0,0,1),
		6 => array(0,1,0,1,1,1,1),
		7 => array(0,1,1,1,0,1,1),
		8 => array(0,1,1,0,1,1,1),
		9 => array(0,0,0,1,0,1,1)
	);

	protected static $b_code = array(
		0 => array(0,1,0,0,1,1,1),
		1 => array(0,1,1,0,0,1,1),
		2 => array(0,0,1,1,0,1,1),
		3 => array(0,1,0,0,0,0,1),
		4 => array(0,0,1,1,1,0,1),
		5 => array(0,1,1,1,0,0,1),
		6 => array(0,0,0,0,1,0,1),
		7 => array(0,0,1,0,0,0,1),
		8 => array(0,0,0,1,0,0,1),
		9 => array(0,0,1,0,1,1,1)
	);

	protected static $c_code = array(
		0 => array(1,1,1,0,0,1,0),
		1 => array(1,1,0,0,1,1,0),
		2 => array(1,1,0,1,1,0,0),
		3 => array(1,0,0,0,0,1,0),
		4 => array(1,0,1,1,1,0,0),
		5 => array(1,0,0,1,1,1,0),
		6 => array(1,0,1,0,0,0,0),
		7 => array(1,0,0,0,1,0,0),
		8 => array(1,0,0,1,0,0,0),
		9 => array(1,1,1,0,1,0,0)
	);

	protected static $first_encoding = array(
		0 => array('a','a','a','a','a','a'),
		1 => array('a','a','b','a','b','b'),
		2 => array('a','a','b','b','a','b'),
		3 => array('a','a','b','b','b','a'),
		4 => array('a','b','a','a','b','b'),
		5 => array('a','b','b','a','a','b'),
		6 => array('a','b','b','b','a','a'),
		7 => array('a','b','a','b','a','b'),
		8 => array('a','b','a','b','b','a'),
		9 => array('a','b','b','a','b','a')
	);

	protected static $normal_guard = array(1,0,1);
	protected static $central_guard = array(0,1,0,1,0);

	public static function eanToBinary($ean)
	{
		$binary = self::$normal_guard;

		$encoding = self::$first_encoding[substr($ean, 0, 1)];

		for($i=1;$i<7;$i++)
		{
			$binary =array_merge($binary, ($encoding[$i-1]=='a' ? self::$a_code[substr($ean, $i, 1)] : self::$b_code[substr($ean, $i, 1)]));
		}

		$binary = array_merge($binary, self::$central_guard);

		for($i=7;$i<13;$i++)
		{
			$binary = array_merge($binary, self::$c_code[substr($ean, $i, 1)]);
		}

		$binary = array_merge($binary, self::$normal_guard);

		return $binary;
	}

	public static function binaryToSvg($binary)
	{
		$binary[]=0;//Colonne blanche supplémentaire, utilisé pour corriger un bug lors de la conversion en PNG

		$long_rect = array(0,1,2,3,45,46,47,48,49,92,93,94,95);

		$total = count($binary);

		$compress_binary = array();

		$j = -1;
		for($i=0; $i<$total; $i++)
		{
			if(!isset($compress_binary[$j]) || ($compress_binary[$j]['color'] != $binary[$i]))
			{
				$j++;
				$compress_binary[$j] = array('color' => $binary[$i], 'length' => 1, 'extra' => (in_array($i, $long_rect) ? true : false), 'position' => $i);
			}
			else
			{
				$compress_binary[$j]['length']++;
			}
		}

		$total = count($compress_binary);

		$str = "<rect width=\"100%\" height=\"100%\" fill=\"white\"/>\n";

		for($i=0; $i<$total; $i++)
		{
			$color = ($compress_binary[$i]['color'] == 1 ? 'black' : 'white');
			$str .= "<rect x=\"".(4+$compress_binary[$i]['position'])."%\" y=\"2.5%\" width=\"".$compress_binary[$i]['length']."%\" height=\"".($compress_binary[$i]['extra'] ? 80 : 75)."%\"  style=\"fill:".$color.";stroke:".$color.";stroke-width:0px;\"/>\n";
		}
	
		return $str;
	}

	public static function eanToSvg($ean, $width)
	{
		$first_p = substr($ean, 0, 1);
		$second_p = substr($ean, 1, 6);
		$third_p = substr($ean, 6, 6);
		$font_size = floor($width/15);
		
		$str  = "<?xml version=\"1.0\" standalone=\"no\"?>\n";
		$str .= "<!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 1.1//EN\" \"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd\">\n";
		$str .= "<svg width=\"".$width."px\" height=\"".($width/2)."px\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\">\n";
		$str .= self::binaryToSvg(self::eanToBinary($ean));
		$str .= "<text x=\"0%\" y=\"90%\" style=\"fill:black; font-size:".$font_size."px; font-family:Verdana;\" >".$first_p."</text>";
		for($i=0; $i<6; $i++)
		{
			$str .= "<text x=\"".(7.5+$i*7.5)."%\" y=\"90%\" style=\"fill:black; font-size:".$font_size."px; font-family:Verdana;\" >".substr($ean, $i+1, 1)."</text>";
			$str .= "<text x=\"".(54+$i*7.5)."%\" y=\"90%\" style=\"fill:black; font-size:".$font_size."px; font-family:Verdana;\" >".substr($ean, $i+7, 1)."</text>";
		}
		$str .= "</svg>";

		return $str;
	}
}
