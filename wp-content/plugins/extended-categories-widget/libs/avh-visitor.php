<?php
if (! defined('AVH_FRAMEWORK'))
	die('You are not allowed to call this page directly.');
if (! class_exists('AVH_Visitor')) {

	final class AVH_Visitor
	{

		/**
		 * Get the user's IP
		 *
		 * @return string
		 */
		public static function getUserIp ()
		{
			$ip = array ();
			foreach (array ( 'HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 
							'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' ) as $key) {
				if (array_key_exists($key, $_SERVER) === true) {
					foreach (explode(',', $_SERVER[$key]) as $visitors_ip) {
						$ip[] = str_replace(' ', '', $visitors_ip);
					}
				}
			}
			// If for some strange reason we don't get an IP we return imemdiately with 0.0.0.0
			if (empty($ip)) {
				return '0.0.0.0';
			}
			$ip = array_values(array_unique($ip));
			$return = null;
			// In PHP 5.3 and up the function filter_var can be used, much quicker as the regular expression check
			if (AVH_Common::isPHP('5.3')) {
				foreach ($ip as $i) {
					if (filter_var($i, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE)) {
						$return = $i;
						break;
					}
				}
			} else {
				$dec_octet = '(?:\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])';
				$ip4_address = $dec_octet . '.' . $dec_octet . '.' . $dec_octet . '.' . $dec_octet;
				$match = array ();
				foreach ($ip as $i) {
					if (preg_match('/^' . $ip4_address . '$/', $i, $match)) {
						if (preg_match('/^(127\.|10\.|192\.168\.|172\.((1[6-9])|(2[0-9])|(3[0-1]))\.)/', $i)) {
							continue;
						} else {
							$return = $i;
							break;
						}
					}
				}
			}
			if (null === $return) {
				$return = '0.0.0.0';
			}
			return $return;
		}
	}
}