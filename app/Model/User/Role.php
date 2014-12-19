<?php
require_once('Gen/Enum.php');

class User_Role extends Gen_Enum
{
	const VIEWER = 'VIEWER';
	const MANAGER = 'MANAGER';

	protected static $_data = array(
		self::VIEWER => array('label' => "Viewer"),
		self::MANAGER => array('label' => "Manager")
	);
}