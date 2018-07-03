<?php
require_once(dirname(__FILE__) . '/../vendor/autoload.php');

use GeoIp2\Database\Reader;

class wfWAFGeoIP2 {
	private $_reader;
	
	/**
	 * Returns the singleton wfGeoIP2.
	 *
	 * @return wfScanner
	 */
	public static function shared() {
		static $_geoip = null;
		if ($_geoip === null) {
			$_geoip = new wfWAFGeoIP2();
		}
		return $_geoip;
	}
	
	public function __construct() {
		$this->_reader = new Reader(__DIR__ . '/../lib/GeoLite2-Country.mmdb'); //Can throw, but we don't catch it because it means the installation is likely corrupt and needs fixed anyway
	}
	
	/**
	 * Returns the database version in use. This is the timestamp of when it was packaged.
	 *
	 * @return null|int
	 */
	public function version() {
		try {
			return $this->_reader->metadata()->buildEpoch;
		}
		catch (Exception $e) {
			//Fall through
		}
		return null;
	}
	
	/**
	 * Returns the country code for the IP if known.
	 *
	 * @param string $ip
	 * @return null|string
	 */
	public function countryCode($ip) {
		try {
			$record = $this->_reader->country($ip);
			return $record->country->isoCode;
		}
		catch (Exception $e) {
			//Fall through
		}
		return null;
	}
}
