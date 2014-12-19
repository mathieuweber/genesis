<?php

class Gen_Date
{
	const DEFAULT_LANGUAGE 	= 'en';
	
	protected static $_date_format  = array(
		'en' => array (
			'short' 		=> 'j M Y',
			'short_time'	=> 'j M Y \a\t g:ia',
			'long' 			=> 'F jS, Y',
			'long_time' 	=> 'F jS, Y \a\t g:ia',
			'numeric' 		=> 'Y/m/j',
			'numeric_time' 	=> 'Y/m/j \a\t g:ia',
			'smart_date' 	=> 'l, \a\t g:ia',
			'time' 			=> 'g:ia',
		),
		'fr' => array (
			'short' 		=> 'j M Y',
			'short_time'	=> 'j M Y à G\hi',
			'long' 			=> 'j F Y',
			'long_time' 	=> 'j F Y à G\hi',
			'numeric' 		=> 'd/m/Y',
			'numeric_time' 	=> 'd/m/Y à G\hi',
			'smart_date' 	=> 'l, à G\hi',
			'time' 			=> 'G\hi',
		),
		'de' => array (
			'short' 		=> 'j M Y',
			'short_time'	=> 'j M Y \u\m G:i',
			'long' 			=> 'j. F Y',
			'long_time' 	=> 'j. F Y \u\m G:i',
			'numeric' 		=> 'd.m.Y',
			'numeric_time' 	=> 'd.m.Y \u\m G:i',
			'smart_date' 	=> 'l, \u\m G:i',
			'time' 			=> 'G:i',
		),
		'es' => array (
			'short' 		=> 'j M Y',
			'short_time'	=> 'j M Y a las G:i',
			'long' 			=> 'j \d\e F Y',
			'long_time' 	=> 'j \d\e F Y a las G:i',
			'numeric' 		=> 'd/m/Y',
			'numeric_time' 	=> 'd/m/Y a las G:i',
			'smart_date' 	=> 'l, a las G:i',
			'time' 			=> 'G:i',
		),
		'it' => array (
			'short' 		=> 'j M Y',
			'short_time'	=> 'j M Y alle ore G.i',
			'long' 			=> 'j F Y',
			'long_time' 	=> 'j F Y alle ore G.i',
			'numeric' 		=> 'd/m/Y',
			'numeric_time' 	=> 'd/m/Y alle ore G.i',
			'smart_date' 	=> 'l, alle ore G.i',
			'time' 			=> 'G.i',
		),
		'pt' => array (
			'short' 		=> 'j M Y',
			'short_time'	=> 'j M Y às G:i',
			'long' 			=> 'j/n Y',
			'long_time' 	=> 'j/n Y às G:i',
			'numeric' 		=> 'd/m/Y',
			'numeric_time' 	=> 'd/m/Y às G:i',
			'smart_date' 	=> 'l, às G:i',
			'time' 			=> 'G:i',
		),
	);
	
	protected static $_date_messages  = array(
		'en' => array (
			'now' 		=> 'a few seconds ago',
			'seconds' 	=> '%d seconds ago',
			'minutes' 	=> array('singular' => 'a minute ago'	, 'plural' => '%d minutes ago'),
			'hours' 	=> array('singular' => 'an hour ago' 	, 'plural' => '%d hours ago'),
			'days' 		=> '%s',
			'date' 		=> '%s',
			'today' 	=> 'Today, at %s',
			'yesterday' => 'Yesterday, at %s',
			'tomorrow' 	=> 'Tomorrow, at %s',
			'period' 	=> 'from %s to %s',
		),
		'fr' => array (
			'now' 		=> 'Il y a quelques secondes',
			'seconds' 	=> 'Il y a %d secondes',
			'minutes' 	=> array('singular' => 'Il y a une minute'	, 'plural' => 'Il y a %d minutes'),
			'hours' 	=> array('singular' => 'Il y a une heure' 	, 'plural' => 'Il y a %d heures'),
			'days' 		=> '%s',
			'date' 		=> 'le %s',
			'today' 	=> 'Aujourd\'hui, à %s',
			'yesterday' => 'Hier, à %s',
			'tomorrow' 	=> 'Demain, à %s',
			'period' 	=> 'du %s au %s',
		),
		'de' => array (
			'now' 		=> 'Vor einigen Sekunden',
			'seconds' 	=> 'Vor %d Sekunden',
			'minutes' 	=> array('singular' => 'Vor einer Minute'	, 'plural' => 'Vor %d Minuten'),
			'hours' 	=> array('singular' => 'Vor einer Stunde' 	, 'plural' => 'Vor %d Stunden'),
			'days' 		=> '%s',
			'date' 		=> 'am %s',
			'today' 	=> 'Heute um %s',
			'yesterday' => 'Gestern um %s',
			'tomorrow' 	=> 'Morgen um %s',
			'period' 	=> 'von %s auf %s',
		),
		'es' => array (
			'now' 		=> 'Hace unos segundos',
			'seconds' 	=> 'Hace %d segundos',
			'minutes' 	=> array('singular' => 'Hace un minuto'	, 'plural' => 'Hace %d minutos'),
			'hours' 	=> array('singular' => 'Hace una hora' 	, 'plural' => 'Hace %d horas'),
			'days' 		=> '%s',
			'date' 		=> 'el %s',
			'today' 	=> 'Hoy a la(s) %s',
			'yesterday' => 'Ayer a la(s) %s',
			'tomorrow' 	=> 'Mañana a la(s) %s',
			'period' 	=> 'de %s a %s',
		),
		'it' => array (
			'now' 		=> 'Pochi secondi fa',
			'seconds' 	=> '%d secondi fa',
			'minutes' 	=> array('singular' => 'Un minuto fa'	, 'plural' => '%d minuti fa'),
			'hours' 	=> array('singular' => 'Uno ora fa' 	, 'plural' => '%d ore fa'),
			'days' 		=> '%s',
			'date' 		=> 'il %s',
			'today' 	=> 'Oggi alle %s',
			'yesterday' => 'Ieri alle %s',
			'tomorrow' 	=> 'Domani alle %s',
			'period' 	=> 'da %s \'a %s',
		),
		'pt' => array (
			'now' 		=> 'Há alguns segundos',
			'seconds' 	=> 'Há %d segundos',
			'minutes' 	=> array('singular' => 'Há um minuto'	, 'plural' => 'Há %d minutos'),
			'hours' 	=> array('singular' => 'Há uma hora' 	, 'plural' => 'Há %d horas'),
			'days' 		=> '%s',
			'date' 		=> 'a %s',
			'today' 	=> 'Hoje às %s',
			'yesterday' => 'Ontem às %s',
			'tomorrow' 	=> 'Amanhã às %s',
			'period' 	=> 'de %s a %s',
		),
	);
	
	protected static $_days = array(
		'en' => array(
			'Sunday' 	=> 'Sunday',
			'Sun' 	 	=> 'Sun',
			'Monday' 	=> 'Monday',
			'Mon' 		=> 'Mon',
			'Tuesday'	=> 'Tuesday',
			'Tue'		=> 'Tue',
			'Wednesday' => 'Wednesday',
			'Wed' 		=> 'Wed',
			'Thursday' 	=> 'Thursday',
			'Thu' 		=> 'Thu',
			'Friday' 	=> 'Friday',
			'Fri' 		=> 'Fri',
			'Saturday' 	=> 'Saturday',
			'Sat' 		=> 'Sat',
		),
		'fr' => array(
			'Sunday' 	=> 'Dimanche',
			'Sun' 	 	=> 'Dim',
			'Monday' 	=> 'Lundi',
			'Mon' 		=> 'Lun',
			'Tuesday'	=> 'Mardi',
			'Tue'		=> 'Mar',
			'Wednesday' => 'Mercredi',
			'Wed' 		=> 'Mer',
			'Thursday' 	=> 'Jeudi',
			'Thu' 		=> 'Jeu',
			'Friday' 	=> 'Vendredi',
			'Fri' 		=> 'Ven',
			'Saturday' 	=> 'Samedi',
			'Sat' 		=> 'Sam',
		),
		'de' => array(
			'Sunday' 	=> 'Sonntag',
			'Sun' 	 	=> 'So',
			'Monday' 	=> 'Montag',
			'Mon' 		=> 'Mo',
			'Tuesday'	=> 'Dienstag',
			'Tue'		=> 'Di',
			'Wednesday' => 'Mittwoch',
			'Wed' 		=> 'Mi',
			'Thursday' 	=> 'Donnerstag',
			'Thu' 		=> 'Do',
			'Friday' 	=> 'Freitag',
			'Fri' 		=> 'Fr',
			'Saturday' 	=> 'Samstag',
			'Sat' 		=> 'Sa',
		),
		'es' => array(
			'Sunday' 	=> 'Domingo',
			'Sun' 	 	=> 'Dom',
			'Monday' 	=> 'Lunes',
			'Mon' 		=> 'Lun',
			'Tuesday'	=> 'Martes',
			'Tue'		=> 'Mar',
			'Wednesday' => 'Miércoles',
			'Wed' 		=> 'Mié',
			'Thursday' 	=> 'Jueves',
			'Thu' 		=> 'Jue',
			'Friday' 	=> 'Viernes',
			'Fri' 		=> 'Vie',
			'Saturday' 	=> 'Sábado',
			'Sat' 		=> 'Sáb',
		),
		'it' => array(
			'Sunday' 	=> 'Domenica',
			'Sun' 	 	=> 'Dom',
			'Monday' 	=> 'Lunedì',
			'Mon' 		=> 'Lun',
			'Tuesday'	=> 'Martedì',
			'Tue'		=> 'Mar',
			'Wednesday' => 'Mercoledì',
			'Wed' 		=> 'Mer',
			'Thursday' 	=> 'Giovedì',
			'Thu' 		=> 'Gio',
			'Friday' 	=> 'Venerdì',
			'Fri' 		=> 'Ven',
			'Saturday' 	=> 'Sabato',
			'Sat' 		=> 'Sab',
		),
		'do' => array(
			'Sunday' 	=> 'Domingo',
			'Sun' 	 	=> 'Dom',
			'Monday' 	=> 'Segunda-feira',
			'Mon' 		=> 'Seg',
			'Tuesday'	=> 'Terca-feira',
			'Tue'		=> 'Ter',
			'Wednesday' => 'Quarta-feira',
			'Wed' 		=> 'Qua',
			'Thursday' 	=> 'Quinta-feira',
			'Thu' 		=> 'Qui',
			'Friday' 	=> 'Sexta-feira',
			'Fri' 		=> 'Sex',
			'Saturday' 	=> 'Sábado',
			'Sat' 		=> 'Sáb',
		),
	);
	
	protected static $_months = array(
		'en' => array(
			'January' 	=> 'January',
			'Jan' 		=> 'Jan',
			'February' 	=> 'February',
			'Feb' 		=> 'Feb',
			'March' 	=> 'March',
			'Mar' 		=> 'Mar',
			'April' 	=> 'April',
			'Apr' 		=> 'Apr',
			'May' 		=> 'May',
			'May' 		=> 'May',
			'June' 		=> 'June',
			'Jun' 		=> 'Jun',
			'July' 		=> 'July',
			'Jul' 		=> 'Jul',
			'August' 	=> 'August',
			'Aug' 		=> 'Aug',
			'September' => 'September',
			'Sep' 		=> 'Sep',
			'October' 	=> 'October',
			'Oct' 		=> 'Oct',
			'November' 	=> 'November',
			'Nov' 		=> 'Nov',
			'December' 	=> 'December',
			'Dec' 		=> 'Dec',
		),
		'fr' => array(
			'January' 	=> 'Janvier',
			'Jan' 		=> 'Jan',
			'February' 	=> 'Février',
			'Feb' 		=> 'Fév',
			'March' 	=> 'Mars',
			'Mar' 		=> 'Mar',
			'April' 	=> 'Avril',
			'Apr' 		=> 'Avr',
			'May' 		=> 'Mai',
			'May' 		=> 'Mai',
			'June' 		=> 'Juin',
			'Jun' 		=> 'Juin',
			'July' 		=> 'Juillet',
			'Jul' 		=> 'Juil',
			'August' 	=> 'Août',
			'Aug' 		=> 'Aou',
			'September' => 'Septembre',
			'Sep' 		=> 'Sep',
			'October' 	=> 'Octobre',
			'Oct' 		=> 'Oct',
			'November' 	=> 'Novembre',
			'Nov' 		=> 'Nov',
			'December' 	=> 'Décembre',
			'Dec' 		=> 'Déc',
		),
		'de' => array(
			'January' 	=> 'Januar',
			'Jan' 		=> 'Jan',
			'February' 	=> 'Februar',
			'Feb' 		=> 'Feb',
			'March' 	=> 'März',
			'Mar' 		=> 'Mrz',
			'April' 	=> 'April',
			'Apr' 		=> 'Apr',
			'May' 		=> 'Mai',
			'May' 		=> 'Mai',
			'June' 		=> 'Juni',
			'Jun' 		=> 'Jun',
			'July' 		=> 'Juli',
			'Jul' 		=> 'Jul',
			'August' 	=> 'August',
			'Aug' 		=> 'Aug',
			'September' => 'September',
			'Sep' 		=> 'Sep',
			'October' 	=> 'Oktober',
			'Oct' 		=> 'Okt',
			'November' 	=> 'November',
			'Nov' 		=> 'Nov',
			'December' 	=> 'Dezember',
			'Dec' 		=> 'Dez',
		),
		'es' => array(
			'January' 	=> 'Enero',
			'Jan' 		=> 'Ene',
			'February' 	=> 'Febrero',
			'Feb' 		=> 'Feb',
			'March' 	=> 'Marzo',
			'Mar' 		=> 'Mar',
			'April' 	=> 'Abril',
			'Apr' 		=> 'Abr',
			'May' 		=> 'Mayo',
			'May' 		=> 'May',
			'June' 		=> 'Junio',
			'Jun' 		=> 'Jun',
			'July' 		=> 'Julio',
			'Jul' 		=> 'Jul',
			'August' 	=> 'Agosto',
			'Aug' 		=> 'Ago',
			'September' => 'Septiembre',
			'Sep' 		=> 'Sep',
			'October' 	=> 'Octubre',
			'Oct' 		=> 'Oct',
			'November' 	=> 'Noviembre',
			'Nov' 		=> 'Nov',
			'December' 	=> 'Diciembre',
			'Dec' 		=> 'Dic',
		),
		'it' => array(
			'January' 	=> 'Gennaio',
			'Jan' 		=> 'Gen',
			'February' 	=> 'Febbraio',
			'Feb' 		=> 'Feb',
			'March' 	=> 'Marzo',
			'Mar' 		=> 'Mar',
			'April' 	=> 'Aprile',
			'Apr' 		=> 'Apr',
			'May' 		=> 'Maggio',
			'May' 		=> 'Mag',
			'June' 		=> 'Giugno',
			'Jun' 		=> 'Giu',
			'July' 		=> 'Luglio',
			'Jul' 		=> 'Lug',
			'August' 	=> 'Agosto',
			'Aug' 		=> 'Ago',
			'September' => 'Settembre',
			'Sep' 		=> 'Set',
			'October' 	=> 'Ottobre',
			'Oct' 		=> 'Ott',
			'November' 	=> 'Novembre',
			'Nov' 		=> 'Nov',
			'December' 	=> 'Dicembre',
			'Dec' 		=> 'Dic',
		),
		'pt' => array(
			'January' 	=> 'Janeiro ',
			'Jan' 		=> 'Jan',
			'February' 	=> 'Fevereiro',
			'Feb' 		=> 'Fev',
			'March' 	=> 'Março',
			'Mar' 		=> 'Mar',
			'April' 	=> 'Abril',
			'Apr' 		=> 'Abr',
			'May' 		=> 'Maio',
			'May' 		=> 'Mai',
			'June' 		=> 'Junho',
			'Jun' 		=> 'Jun',
			'July' 		=> 'Julho',
			'Jul' 		=> 'Jul',
			'August' 	=> 'Agosto',
			'Aug' 		=> 'Ago',
			'September' => 'Setembro',
			'Sep' 		=> 'Sep',
			'October' 	=> 'Outubro',
			'Oct' 		=> 'Out',
			'November' 	=> 'Novembro',
			'Nov' 		=> 'Nov',
			'December' 	=> 'Dezembro',
			'Dec' 		=> 'Dez',
		),		
	);
	
	public static function getWallLabel($type, $lang, $plural = 0)
	{
		if(!array_key_exists($lang, self::$_date_messages)) $lang = self::DEFAULT_LANGUAGE;
	
		if(is_array(self::$_date_messages[$lang][$type])) {
			if($plural > 1) {
				return self::$_date_messages[$lang][$type]['plural'];
			} else {
				return self::$_date_messages[$lang][$type]['singular'];
			}
		} else {
			return self::$_date_messages[$lang][$type];
		}	
	}

	public static function create($date)
	{
		if($date instanceof Datetime) {
			return $date;
		}
		if (is_array($date)) {
			$year = isset($date['year']) ? (int) $date['year'] : '00';
			$month = isset($date['month']) ? (int) $date['month'] : '00';
			$day = isset($date['day']) ? (int) $date['day'] : '00';
			$hour = isset($date['hour']) ? (int) $date['hour'] : '00';
			$min = isset($date['min']) ? (int) $date['min'] : '00';
			$sec = isset($date['sec']) ? (int) $date['sec'] : '00';
			return new DateTime($year.'-'.$month.'-'.$day.' '.$hour.':'.$min.':'.$sec);
		}
		return new DateTime($date);
	}
	
	public static function toTimestamp(Datetime $date)
	{
		return strtotime($date->format('Y-m-d h:i:s'));
	}
	
	public static function add(Datetime $date, $diff = array())
	{
		$timestamp = self::toTimestamp($date);
		if (isset($diff['week'])) {
			$diff['day'] = $diff['week'] * 7;
		}
		$newdate = date('Y-m-d h:i:s', mktime(
						date('h', $timestamp) + (isset($diff['hour']) ? $diff['hour'] : null),
						date('i', $timestamp) + (isset($diff['minute']) ? $diff['minute'] : null),
						date('s', $timestamp) + (isset($diff['second']) ? $diff['second'] : null),
						date('m', $timestamp) + (isset($diff['month']) ? $diff['month'] : null),
						date('d', $timestamp) + (isset($diff['day']) ? $diff['day'] : null),
						date('Y', $timestamp) + (isset($diff['year']) ? $diff['year'] : null)
					));
	  return new Datetime($newdate);
	}
	
	public static function compare(Datetime $date1, Datetime $date2)
	{
		return ((strtotime($date1->format('Y/m/d H:i:s')) - strtotime($date2->format('Y/m/d H:i:s'))) > 0);
	}
	
	public static function diff($date1,$date2=null)
	{
		if($date2 == null){
			$date2 = time();
		}
		else{
			$date2 = strtotime($date2->format('Y/m/d H:i:s'));
		}
		
		$date1 = self::create($date1);
		return round($date2 - strtotime($date1->format('Y/m/d H:i:s'))) / (3600 * 24);
	}
	
	public static function diffTime($date1, $date2 = null)
	{
		if($date2 == null){
			$date2 = time();
		}
		else{
			$date2 = strtotime($date2->format('Y/m/d H:i:s'));
		}
		$date1 = self::create($date1);
		return (strtotime($date1->format('Y/m/d H:i:s')) - $date2);
	
	}
	
	public static function greaterThan($date, $days)
	{
		return (self::diff($date) >= (int) $days);
	}
	
	public static function lessThan($date, $day)
	{
		return (self::diff($date) <= (int) $day);
	}
	
	public static function getAge($date)
	{
		$date = self::create($date);
		$today = new Datetime();
		$age = (int) ($today->format('Y') - $date->format('Y'));
		if ($today->format('m') < $date->format('m')) {
			return $age - 1;
		}
		if (($today->format('m') == $date->format('m')) && ($today->format('d') - $date->format('d'))) {
			return $age - 1;
		}
		return $age;
	}
	
	public static function translate($formatted_date, $lang) 
	{
		if(!array_key_exists($lang, self::$_date_messages)) $lang = self::DEFAULT_LANGUAGE;
	
		$formatted_date = str_replace(self::$_months[self::DEFAULT_LANGUAGE], self::$_months[$lang], $formatted_date);
		$formatted_date = str_replace(self::$_days[self::DEFAULT_LANGUAGE], self::$_days[$lang], $formatted_date);
		
		return $formatted_date;
	}

	public static function typeExist($type) 
	{
		return array_key_exists($type, self::$_date_format[self::DEFAULT_LANGUAGE]);
	}
	
	public static function format($date, $format = 'short', $timezone = false, $lang = null) 
	{
		if(empty($lang)) {
			require_once('Gen/I18n.php');
			$lang = (Gen_I18n::getLocale()) ? Gen_I18n::getLocale() : self::DEFAULT_LANGUAGE;
		}
	
		if(!array_key_exists($lang, self::$_date_format)) $lang = self::DEFAULT_LANGUAGE;
	
		if (!($date instanceof DateTime)) {
			$date = new DateTime((string) $date);
		}
		
		// if($timezone) {	
			// $timezone = Gen_Geo::getTimezone();
			// if($timezone) {
				// $date->setTimezone(new DateTimeZone($timezone));
			// }
		// }

		if(self::typeExist($format)) { 
			$date_formated = $date->format(self::$_date_format[$lang][$format]);
		} else {
			$date_formated = $date->format($format);
		}

		return self::translate($date_formated, $lang);
		
	}
	/**
	 * create a DateTime
	 * @param year : 2010
	 * @param month : janvier
	 * @param day : 10
	 */
	public static function array2datetime($year,$month="janvier",$day="01"){
		$monthArray = array(
			"janvier"	=> "01",
			"fevrier"	=> "02",
			"mars"		=> "03",
			"avril"		=> "04",
			"mai"		=> "05",
			"juin"		=> "06",
			"juillet"	=> "07",
			"aout"		=> "08",
			"septembre"	=> "09",
			"octobre"	=> "10",
			"novembre"	=> "11",
			"decembre"  => "12"
		);
		return new DateTime("$year-{$monthArray[$month]}-$day");
	}
	
	public static function getMonthArray(){
		return array(
			"01" => "Janvier",
			"02" => "Février",
			"03" => "Mars",
			"04" => "Avril",
			"05" => "Mai",
			"06" => "Juin",
			"07" => "Juillet",
			"08" => "Août",
			"09" => "Septembre",
			"10" => "Octobre",
			"11" => "Novembre",
			"12" => "Décembre"
		);
	}
	
	/**
	 * create a string from a DateTime
	 * @param year : 2010
	 * @param month : janvier
	 * @param day : 10
	 */
	public static function datetime2string($date){
		require_once("Gen/Str.php");
		$monthArray = self::getMonthArray();
		$month = Gen_Str::urlDasherize($monthArray[$date->format('m')]);
		return "{$date->format('d')}-$month-{$date->format('Y')}";
	}
	
	/**
	 * accept 10-janvier-2010 or janvier-2010 or 2010
	 * @param string $date
	 */
	public static function string2datetime($date){
		$dateArray= array();
		$month = self::getMonthRegExpr();
		
		if(preg_match("/(0[1-9]|[12][0-9]|3[01])-($month)-(20\d\d)/",$date,$dateArray)){
			return self::array2datetime($dateArray[3],$dateArray[2],$dateArray[1]);
		}
		else if(preg_match("/($month)-(20\d\d)/",$date,$dateArray)){
			return self::array2datetime($dateArray[2],$dateArray[1]);
		}
		else{
			return self::array2datetime($date);
		}
	}
	
	/**
	 * retrieve the string "janvier|fevrier...
	 */
	public static function getMonthRegExpr(){
		return "janvier|fevrier|mars|avril|mai|juin|juillet|aout|septembre|octobre|novembre|decembre";
	}
	
	public static function period($startDate, $endDate, $format = 'short', $timezone = true, $lang = null)
	{
		require_once('Gen/I18n.php');
		$lang = (Gen_I18n::getLocale()) ? Gen_I18n::getLocale() : self::DEFAULT_LANGUAGE;
		
		if(!array_key_exists($lang, self::$_date_messages)) $lang = self::DEFAULT_LANGUAGE;
		
		if (!($startDate instanceof DateTime)) {
			$startDate = new DateTime($startDate);
		}
		
		if (!($endDate instanceof DateTime)) {
			$endDate = new DateTime($endDate);
		}

		return vsprintf(self::$_date_messages[$lang]['period'], array(self::format($startDate, $format, $lang), self::format($endDate, $format, $lang)));
	}
	
	public static function smartDate($date)
	{
		require_once('Gen/I18n.php');
		$lang = (Gen_I18n::getLocale()) ? Gen_I18n::getLocale() : self::DEFAULT_LANGUAGE;
	
		if (!($date instanceof DateTime)) {
			$date = new DateTime($date);
		}
	
		$current_date = new DateTime();
		
		$diff = self::diffTime($current_date, $date);
		
		$seconds 	= round($diff);
		$minutes 	= round($diff / 60);
		$hours 		= round($diff / (60*60));
		$days 		= round($diff / (60*60*24));
		
		
		// Future
		if($days == -1) 	return vsprintf(self::getWallLabel('tomorrow', $lang), self::format($date, 'time', $lang));
		
		// Past
		if($seconds < 30) {
			return vsprintf(self::getWallLabel('now', $lang, $seconds), $seconds);
		}
		
		if($minutes < 1){
			return vsprintf(self::getWallLabel('seconds', $lang, $seconds), $seconds);
		}
		
		if($minutes < 60){
			return vsprintf(self::getWallLabel('minutes', $lang, $minutes), $minutes);
		}
			
		if($hours < 24){
			return vsprintf(self::getWallLabel('hours', $lang, $hours), $hours);
		}
		
		if($days < 7){

			if($days == 1) return vsprintf(self::getWallLabel('yesterday', $lang), self::format($date, 'time', false, $lang));
			
			return vsprintf(self::getWallLabel('days', $lang), self::format($date, 'smart_date', false, $lang));
		}
		
		if($days == 0) 	return vsprintf(self::getWallLabel('today', $lang), self::format($date, 'time', false, $lang));
		
		if($diff < 0) 	return vsprintf(self::getWallLabel('date', $lang), self::format($date, 'long', false, $lang));
		
		return vsprintf(self::getWallLabel('date', $lang), self::format($date, 'long', false, $lang));
	}

	public static function getMonth($number, $lang)
	{
		$months = self::getMonthArray();
		$month = $months[$number > 10 ? $number : "0".$number];
		return str_replace(self::$_months['fr'], self::$_months[$lang], $month);
	}
}
