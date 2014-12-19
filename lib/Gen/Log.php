<?php
class Gen_Log
{	
	protected static $_logs = array();
	
	protected static $_startTime;
	
	protected static $_previousTime;
	
	protected static $_stopTime;
	
	protected static $_count = 0;
	
	public static $_debug = false;
	
	public static function getLogs()
	{
		return self::$_logs;
	}
	
	public static function start()
	{
		if (!isset(self::$_startTime)) {
			self::$_startTime = microtime(true);
			self::$_previousTime = self::$_startTime;
			self::log('Logger Start', 'Gen_Log::start');
		}
		return self::$_startTime;
	}
	
	public static function stop()
	{
		if (!isset(self::$_stopTime)) {
			self::$_stopTime = microtime(true);
			self::log('Logger Stop', 'Logger::stop');
		}
		return self::$_stopTime;
	}
	
	public static function log($message, $src, $level = 'info')
	{
		if (!self::$_debug) {
			return false;
		}
		$start = self::start();
		$time = microtime(true);

		if (isset(self::$_logs[self::$_count])) {
			self::$_logs[self::$_count]['duration'] = $time - self::$_previousTime;
			self::$_count++;
		}
		
		self::$_logs[self::$_count] = array(
			'message' => $message,
			'src' => (string) $src,
			'level' => (string) $level,
			'elapsed' => $time - $start,
			'duration' => 0
		);
		
		self::$_previousTime = $time;
		return $time;
	}
	
	public static function render()
	{
		$str = '<div class="block">';
		$str.= '<p><b>Exec. Time</b>: ' . round(self::stop() - self::start(), 4) . ' s<p>';
		
		$str.= '<div class="box">';
		
		foreach (self::$_logs as $log) {
			$message = $log['message'];
			if($message instanceof Gen_Entity) {
				$message = $message->toArray();
			}
			if (is_object($message) || is_array($message)) {
				$message = '<pre>' . print_r($message, true) .'</pre>';
			}
			if(is_bool($message)) {
				$message = $message ? 'TRUE' : 'FALSE';
			}
			$str .= '<div class="block br-small">'
			      . '<span class="debug-'.$log['level'].'"><b>'.$log['level'].'</b></span> - '
				  . '<span style="width: 100px;"><b>'.$log['src'].'</b></span> - '
				  . '<span style="width: 100px;"><b>'.$log['elapsed'].'</b> s</span> - '
				  . '<span style="width: 100px;"><b>'.round($log['duration']*1000, 2).'</b> ms</span>'
				  . '</div>'
				  . '<div class="block br-small">'.$message.'</div>';
				  
		}
		return $str.'</tbody></table>';
	}
	
	public static function out($var, $label = null)
	{
		if(is_bool($var)) {
				$var = $var ? 'TRUE' : 'FALSE';
		}
		$str = '<p>';
		if($label) {
			$str .= '<strong>'.$label.'</strong>';
		}
		$str .= '<pre>' . print_r($var, true) .'</pre>';
		return $str.'</p>'."\n\n";
	}
}