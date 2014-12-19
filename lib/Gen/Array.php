<?php
/**
 * @category   Gen
 * @package	Gen_Array
 */
class Gen_Array
{
	public static function where(array $arr, $property, $condition)
	{
		$filtered = array();
		foreach($arr as $key => $value) {
			if (is_array($value) && isset($value[$property]) && $value[$property] == $condition) {
				$filtered[$key] = $value;
			}
		}
		return $filtered;
	}
	
	public static function reduce(array $arr, $property)
	{
		$reduced = array();
		foreach($arr as $key => $value) {
			if (is_array($value) && isset($value[$property])) {
				$reduced[$key] = $value[$property];
			}
		}
		return $reduced;
	}
	
	public static function search($str, array $arr)
	{
		require_once('Gen/Str.php');
		
		foreach($arr as $key => $value) {
			$value = Gen_Str::transformAccents($value);
			$value = mb_strtolower($value);
			
			if (preg_match("#$str#", $value)) return $key;
		}
		return false;
	}
	
	public static function count(array $arr)
	{
		return count($arr);
	}
	
	public static function computeNbrOfRecursiveElements(array $arr)
	{
		$result=array();
		foreach ($arr as $values):
			$num = 0;
			foreach ($arr as $value):
				if ($values == $value) {
					$num++;
				}
			endforeach;
			$result[$values] = $num;
		endforeach;
		return $result;
	}
	
	public static function getKeysFromArray(array $array)
	{
		$result = array();
		foreach ($array as $key => $data) :
			array_push($result,$key);
		endforeach;
		return $result;
	}
	
	public static function flatten(array $array) 
	{
		foreach($array as $k=>$v) {
			$array[$k]= (array) $v;
		}
		
		return call_user_func_array('array_merge',$array);
	}
}