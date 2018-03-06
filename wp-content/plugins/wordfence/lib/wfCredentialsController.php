<?php

class wfCredentialsController {
	public static function isLeakedPassword($login, $password) {
		$sha1 = strtoupper(hash('sha1', $password));
		$prefix = substr($sha1, 0, 5);
		
		$ssl_verify = (bool) wfConfig::get('ssl_verify');
		$args = array(
			'timeout'    => 5,
			'user-agent' => "Wordfence.com UA " . (defined('WORDFENCE_VERSION') ? WORDFENCE_VERSION : '[Unknown version]'),
			'sslverify'  => $ssl_verify,
			'headers'	 => array('Referer' => false),
		);
		
		if (!$ssl_verify) { // Some versions of cURL will complain that SSL verification is disabled but the CA bundle was supplied.
			$args['sslcertificates'] = false;
		}
		
		$response = wp_remote_get(sprintf(WORDFENCE_BREACH_URL_BASE_SEC . "%s.txt", $prefix), $args);
		
		if (!is_wp_error($response)) {
			$data = wp_remote_retrieve_body($response);
			$lines = explode("\n", $data);
			foreach ($lines as $l) {
				$components = explode(":", $l);
				$teshSHA1 = $prefix . strtoupper($components[0]);
				if (hash_equals($sha1, $teshSHA1)) {
					return true;
				}
			}
		}
		
		return false;
	}
}