<?php

class Gen_Geo 
{
	const LOCAL_IP = '81.252.204.205';
	
	protected static $_country_code;
	
	protected static $_country_name;

	protected static $_region;
	
	protected static $_city;
	
	protected static $_timezone;
	
	protected static $_country;
	
	public static $_geoip = false;
	
	
    public static function getCountryCode()
    {
		if(self::$_country_code) return self::$_country_code;
		
		if(self::$_geoip) {
			require_once('Gen/Controller/Request.php');
			self::$_country_code = geoip_country_code_by_name(Gen_Controller_Request::getIp());
		} else {
			require_once(MS_LIB_DIR .'GeoIP/geoip.inc');
			$gi = geoip_open(MS_LIB_DIR .'GeoIP/GeoIP.dat', GEOIP_STANDARD);
			self::$_country_code = geoip_country_code_by_addr($gi, self::LOCAL_IP);
			geoip_close($gi);
		}
		
		return self::$_country_code;
	}
	
	public static function getCountryName()
    {
		if(self::$_country_name) return self::$_country_name;
		
		if(self::$_geoip) {
			require_once('Gen/Controller/Request.php');
			self::$_country_name = geoip_country_name_by_name(Gen_Controller_Request::getIp());	
		} else {
			require_once(MS_LIB_DIR .'GeoIP/geoip.inc');
			$gi = geoip_open(MS_LIB_DIR .'GeoIP/GeoIP.dat', GEOIP_STANDARD);
			self::$_country_name = geoip_country_name_by_addr($gi, self::LOCAL_IP);
			geoip_close($gi);
		}
		
		return self::$_country_name;
	}
	
	public static function getRegion()
    {
		if(self::$_region) return self::$_region;
		
		if(self::$_geoip) {
			require_once('Gen/Controller/Request.php');
			$region = geoip_region_by_name(Gen_Controller_Request::getIp()); 
			self::$_region['region'];
		} else {
			self::$_region = self::getCity()->region;
		}
		
		return self::$_region;
	}
				
    public static function getCity()
    {
		if(self::$_city) return self::$_city;
		
		if(self::$_geoip) {
			require_once('Gen/Controller/Request.php');
			self::$_city = geoip_record_by_name(Gen_Controller_Request::getIp());
		} else {
			require_once(MS_LIB_DIR .'GeoIP/geoipcity.inc');
			require_once(MS_LIB_DIR .'GeoIP/geoipregionvars.php');
			$gi = geoip_open(MS_LIB_DIR .'GeoIP/GeoIPCity.dat', GEOIP_STANDARD);
			self::$_city = geoip_record_by_addr($gi, self::LOCAL_IP);
			geoip_close($gi);
		}
		
		return self::$_city;
	}
	
    public static function getTimezone()
    {
		if(self::$_timezone) return self::$_timezone;
		
		$country = self::getCountryCode();

		if(self::$_geoip) {
			self::$_timezone = geoip_time_zone_by_country_and_region($country);
			
		} else {
			require_once(MS_LIB_DIR .'GeoIP/timezone.php');
			$region = self::getRegion();
			self::$_timezone = get_time_zone($country, $region);
		}
		
		return self::$_timezone;		
	}
}