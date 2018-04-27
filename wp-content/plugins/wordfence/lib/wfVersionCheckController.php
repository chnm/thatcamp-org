<?php

class wfVersionCheckController {
	const VERSION_COMPATIBLE = 'compatible';
	const VERSION_DEPRECATED = 'deprecated';
	const VERSION_UNSUPPORTED = 'unsupported';
	
	const PHP_DEPRECATING = '5.3.0'; //When greater than PHP_MINIMUM, will issue a discontinuing warning the first time we check it and find a version less than this (also applies to the other similar constant pairs)
	const PHP_MINIMUM = '5.2.0'; //The currently supported minimum
	
	const OPENSSL_DEPRECATING = '1.0.1'; 
	const OPENSSL_MINIMUM = '1.0.1';
	
	const WORDPRESS_DEPRECATING = '3.9.0';
	const WORDPRESS_MINIMUM = '3.9.0';
	
	public static function shared() {
		static $_shared = false;
		if ($_shared === false) {
			$_shared = new wfVersionCheckController();
		}
		return $_shared;
	}
	
	/**
	 * Returns whether or not all version checks are successful. If any check returns a value other than VERSION_COMPATIBLE, this returns false.
	 * 
	 * @return bool
	 */
	public function checkVersions() {
		return ($this->checkPHPVersion() == self::VERSION_COMPATIBLE) && ($this->checkOpenSSLVersion() == self::VERSION_COMPATIBLE) && ($this->checkWordPressVersion() == self::VERSION_COMPATIBLE);
	}
	
	/**
	 * Does the same thing as checkVersions but also triggers display of the corresponding warnings.
	 * 
	 * @return bool
	 */
	public function checkVersionsAndWarn() {
		//PHP
		$php = $this->checkPHPVersion();
		if ($php == self::VERSION_DEPRECATED) {
			$this->_alertEmail(
				'phpVersionCheckDeprecationEmail_' . self::PHP_DEPRECATING,
				__('PHP version too old', 'wordfence'),
				sprintf(__('Your site is using a PHP version (%s) that will no longer be supported by Wordfence in an upcoming release and needs to be updated. We recommend using the newest version of PHP 7.x or 5.6 but will currently support PHP versions as old as %s. Version checks are run regularly, so if you have successfully updated, you can dismiss this notice or check that the update has taken effect later.', 'wordfence'), phpversion(), self::PHP_DEPRECATING) . ' ' . sprintf(__('Learn More: %s', 'wordfence'), wfSupportController::esc_supportURL(wfSupportController::ITEM_VERSION_PHP))
			);
			
			$this->_adminNotice(
				'phpVersionCheckDeprecationNotice_' . self::PHP_DEPRECATING,
				'phpVersionCheck',
				sprintf(__('<strong>WARNING: </strong> Your site is using a PHP version (%s) that will no longer be supported by Wordfence in an upcoming release and needs to be updated. We recommend using the newest version of PHP 7.x or 5.6 but will currently support PHP versions as old as %s. Version checks are run regularly, so if you have successfully updated, you can dismiss this notice or check that the update has taken effect later.', 'wordfence'), phpversion(), self::PHP_DEPRECATING) . ' <a href="' . wfSupportController::esc_supportURL(wfSupportController::ITEM_VERSION_PHP) . '" target="_blank" rel="noopener noreferrer">' . __('Learn More', 'wordfence') . '</a>'
			);
			
			return false;
		}
		else if ($php == self::VERSION_UNSUPPORTED) {
			$this->_alertEmail(
				'phpVersionCheckUnsupportedEmail_' . self::PHP_MINIMUM,
				__('PHP version too old', 'wordfence'),
				sprintf(__('Your site is using a PHP version (%s) that is no longer supported by Wordfence and needs to be updated. We recommend using the newest version of PHP 7.x or 5.6 but will currently support PHP versions as old as %s. Version checks are run regularly, so if you have successfully updated, you can dismiss this notice or check that the update has taken effect later.', 'wordfence'), phpversion(), self::PHP_DEPRECATING) . ' ' . sprintf(__('Learn More: %s', 'wordfence'), wfSupportController::esc_supportURL(wfSupportController::ITEM_VERSION_PHP))
			);
			
			$this->_adminNotice(
				'phpVersionCheckUnsupportedNotice_' . self::PHP_MINIMUM,
				'phpVersionCheck',
				sprintf(__('<strong>WARNING: </strong> Your site is using a PHP version (%s) that is no longer supported by Wordfence and needs to be updated. We recommend using the newest version of PHP 7.x or 5.6 but will currently support PHP versions as old as %s. Version checks are run regularly, so if you have successfully updated, you can dismiss this notice or check that the update has taken effect later.', 'wordfence'), phpversion(), self::PHP_DEPRECATING) . ' <a href="' . wfSupportController::esc_supportURL(wfSupportController::ITEM_VERSION_PHP) . '" target="_blank" rel="noopener noreferrer">' . __('Learn More', 'wordfence') . '</a>'
			);
			
			return false;
		}
		else {
			wfAdminNoticeQueue::removeAdminNotice(false, 'phpVersionCheck');
		}
		
		//OpenSSL
		$openssl = $this->checkOpenSSLVersion();
		if ($openssl == self::VERSION_DEPRECATED) {
			$this->_alertEmail(
				'opensslVersionCheckDeprecationEmail_' . self::OPENSSL_DEPRECATING,
				__('OpenSSL version too old', 'wordfence'),
				sprintf(__('Your site is using an OpenSSL version (%s) that will no longer be supported by Wordfence in an upcoming release and needs to be updated. We recommend using the newest version of OpenSSL but will currently support OpenSSL versions as old as %s. Version checks are run regularly, so if you have successfully updated, you can dismiss this notice or check that the update has taken effect later.', 'wordfence'), self::openssl_make_text_version(), self::OPENSSL_DEPRECATING) . ' ' . sprintf(__('Learn More: %s', 'wordfence'), wfSupportController::esc_supportURL(wfSupportController::ITEM_VERSION_OPENSSL))
			);
			
			$this->_adminNotice(
				'opensslVersionCheckDeprecationNotice_' . self::OPENSSL_DEPRECATING,
				'opensslVersionCheck',
				sprintf(__('<strong>WARNING: </strong> Your site is using an OpenSSL version (%s) that will no longer be supported by Wordfence in an upcoming release and needs to be updated. We recommend using the newest version of OpenSSL but will currently support OpenSSL versions as old as %s. Version checks are run regularly, so if you have successfully updated, you can dismiss this notice or check that the update has taken effect later.', 'wordfence'), self::openssl_make_text_version(), self::OPENSSL_DEPRECATING) . ' <a href="' . wfSupportController::esc_supportURL(wfSupportController::ITEM_VERSION_OPENSSL) . '" target="_blank" rel="noopener noreferrer">' . __('Learn More', 'wordfence') . '</a>'
			);
			
			return false;
		}
		else if ($openssl == self::VERSION_UNSUPPORTED) {
			$this->_alertEmail(
				'opensslVersionCheckUnsupportedEmail_' . self::PHP_MINIMUM,
				__('OpenSSL version too old', 'wordfence'),
				sprintf(__('Your site is using an OpenSSL version (%s) that is no longer supported by Wordfence and needs to be updated. We recommend using the newest version of OpenSSL but will currently support OpenSSL versions as old as %s. Version checks are run regularly, so if you have successfully updated, you can dismiss this notice or check that the update has taken effect later.', 'wordfence'), self::openssl_make_text_version(), self::OPENSSL_DEPRECATING) . ' ' . sprintf(__('Learn More: %s', 'wordfence'), wfSupportController::esc_supportURL(wfSupportController::ITEM_VERSION_OPENSSL))
			);
			
			$this->_adminNotice(
				'opensslVersionCheckUnsupportedNotice_' . self::PHP_MINIMUM,
				'opensslVersionCheck',
				sprintf(__('<strong>WARNING: </strong> Your site is using an OpenSSL version (%s) that is no longer supported by Wordfence and needs to be updated. We recommend using the newest version of OpenSSL but will currently support OpenSSL versions as old as %s. Version checks are run regularly, so if you have successfully updated, you can dismiss this notice or check that the update has taken effect later.', 'wordfence'), self::openssl_make_text_version(), self::OPENSSL_DEPRECATING) . ' <a href="' . wfSupportController::esc_supportURL(wfSupportController::ITEM_VERSION_OPENSSL) . '" target="_blank" rel="noopener noreferrer">' . __('Learn More', 'wordfence') . '</a>'
			);
			
			return false;
		}
		else {
			wfAdminNoticeQueue::removeAdminNotice(false, 'opensslVersionCheck');
		}
		
		//WordPress
		$wordpress = $this->checkWordPressVersion();
		if ($wordpress == self::VERSION_DEPRECATED) {
			require(ABSPATH . 'wp-includes/version.php'); /** @var string $wp_version */
			
			$this->_alertEmail(
				'wordpressVersionCheckDeprecationEmail_' . self::WORDPRESS_DEPRECATING,
				__('WordPress version too old', 'wordfence'),
				sprintf(__('Your site is using a WordPress version (%s) that will no longer be supported by Wordfence in an upcoming release and needs to be updated. We recommend using the newest version of WordPress but will currently support WordPress versions as old as %s. Version checks are run regularly, so if you have successfully updated, you can dismiss this notice or check that the update has taken effect later.', 'wordfence'), $wp_version, self::WORDPRESS_DEPRECATING) . ' ' . sprintf(__('Learn More: %s', 'wordfence'), wfSupportController::esc_supportURL(wfSupportController::ITEM_VERSION_WORDPRESS))
			);
			
			$this->_adminNotice(
				'wordpressVersionCheckDeprecationNotice_' . self::WORDPRESS_DEPRECATING,
				'wordpressVersionCheck',
				sprintf(__('<strong>WARNING: </strong> Your site is using a WordPress version (%s) that will no longer be supported by Wordfence in an upcoming release and needs to be updated. We recommend using the newest version of WordPress but will currently support WordPress versions as old as %s. Version checks are run regularly, so if you have successfully updated, you can dismiss this notice or check that the update has taken effect later.', 'wordfence'), $wp_version, self::WORDPRESS_DEPRECATING) . ' <a href="' . wfSupportController::esc_supportURL(wfSupportController::ITEM_VERSION_WORDPRESS) . '" target="_blank" rel="noopener noreferrer">' . __('Learn More', 'wordfence') . '</a>'
			);
			
			return false;
		}
		else if ($wordpress == self::VERSION_UNSUPPORTED) {
			require(ABSPATH . 'wp-includes/version.php'); /** @var string $wp_version */
			
			$this->_alertEmail(
				'wordpressVersionCheckUnsupportedEmail_' . self::WORDPRESS_MINIMUM,
				__('WordPress version too old', 'wordfence'),
				sprintf(__('Your site is using a WordPress version (%s) that is no longer supported by Wordfence and needs to be updated. We recommend using the newest version of WordPress but will currently support WordPress versions as old as %s. Version checks are run regularly, so if you have successfully updated, you can dismiss this notice or check that the update has taken effect later.', 'wordfence'), $wp_version, self::WORDPRESS_DEPRECATING) . ' ' . sprintf(__('Learn More: %s', 'wordfence'), wfSupportController::esc_supportURL(wfSupportController::ITEM_VERSION_WORDPRESS))
			);
			
			$this->_adminNotice(
				'wordpressVersionCheckUnsupportedNotice_' . self::WORDPRESS_MINIMUM,
				'wordpressVersionCheck',
				sprintf(__('<strong>WARNING: </strong> Your site is using a WordPress version (%s) that is no longer supported by Wordfence and needs to be updated. We recommend using the newest version of WordPress but will currently support WordPress versions as old as %s. Version checks are run regularly, so if you have successfully updated, you can dismiss this notice or check that the update has taken effect later.', 'wordfence'), $wp_version, self::WORDPRESS_DEPRECATING) . ' <a href="' . wfSupportController::esc_supportURL(wfSupportController::ITEM_VERSION_WORDPRESS) . '" target="_blank" rel="noopener noreferrer">' . __('Learn More', 'wordfence') . '</a>'
			);
			
			return false;
		}
		else {
			wfAdminNoticeQueue::removeAdminNotice(false, 'wordpressVersionCheck');
		}
		
		return true;
	}
	
	private function _alertEmail($checkKey, $title, $body) {
		if (!wfConfig::get($checkKey)) {
			wordfence::alert($title, $body, wfUtils::getIP());
			wfConfig::set($checkKey, true);
		}
	}
	
	private function _adminNotice($checkKey, $noticeKey, $message) {
		if (!wfConfig::get($checkKey)) {
			wfAdminNoticeQueue::addAdminNotice(wfAdminNotice::SEVERITY_CRITICAL, $message, $noticeKey);
			wfConfig::set($checkKey, true);
		}
	}
	
	/**
	 * Returns whether or not the PHP version meets our minimum requirement or is a version being deprecated.
	 * 
	 * @return string One of the VERSION_ constants.
	 */
	public function checkPHPVersion() {
		if (version_compare(phpversion(), self::PHP_DEPRECATING, '>=')) {
			return self::VERSION_COMPATIBLE;
		}
		
		if (self::PHP_DEPRECATING != self::PHP_MINIMUM && version_compare(phpversion(), self::PHP_MINIMUM, '>=')) {
			return self::VERSION_DEPRECATED;
		}
		
		return self::VERSION_UNSUPPORTED;
	}
	
	/**
	 * Returns whether or not the OpenSSL version meets our minimum requirement or is a version being deprecated.
	 *
	 * @return string One of the VERSION_ constants.
	 */
	public function checkOpenSSLVersion() {
		if (self::openssl_version_compare(self::OPENSSL_DEPRECATING) <= 0) {
			return self::VERSION_COMPATIBLE;
		}
		
		if (self::OPENSSL_DEPRECATING != self::OPENSSL_MINIMUM && self::openssl_version_compare(self::OPENSSL_MINIMUM) <= 0) {
			return self::VERSION_DEPRECATED;
		}
		
		return self::VERSION_UNSUPPORTED;
	}
	
	/**
	 * Returns whether or not the WordPress version meets our minimum requirement or is a version being deprecated.
	 *
	 * @return string One of the VERSION_ constants.
	 */
	public function checkWordPressVersion() {
		require(ABSPATH . 'wp-includes/version.php'); /** @var string $wp_version */
		
		if (version_compare($wp_version, self::WORDPRESS_DEPRECATING, '>=')) {
			return self::VERSION_COMPATIBLE;
		}
		
		if (self::WORDPRESS_DEPRECATING != self::WORDPRESS_MINIMUM && version_compare($wp_version, self::WORDPRESS_MINIMUM, '>=')) {
			return self::VERSION_DEPRECATED;
		}
		
		return self::VERSION_UNSUPPORTED;
	}
	
	/**
	 * Utility Functions
	 */
	
	/**
	 * Returns whether or not the OpenSSL version is before, after, or equal to the equivalent text version string.
	 *
	 * @param string $compareVersion
	 * @param int $openSSLVersion A version number in the format OpenSSL uses.
	 * @return bool|int Returns -1 if $compareVersion is earlier, 0 if equal, 1 if later, and false if not a valid version string.
	 */
	public static function openssl_version_compare($compareVersion, $openSSLVersion = OPENSSL_VERSION_NUMBER) {
		if (preg_match('/^(\d+)\.(\d+)\.(\d+)([a-z]?)/i', $compareVersion, $matches)) {
			$primary = 0; $major = 0; $minor = 0; $fixLetterIndex = 0;
			if (isset($matches[1])) { $primary = (int) $matches[1]; }
			if (isset($matches[2])) { $major = (int) $matches[2]; }
			if (isset($matches[3])) { $minor = (int) $matches[3]; }
			if (isset($matches[4])) { $fixLetterIndex = strpos('abcdefghijklmnopqrstuvwxyz', strtolower($matches[1])) + 1; }
			
			$compareOpenSSLVersion = self::openssl_make_number_version($primary, $major, $minor, $fixLetterIndex, 0);
			if ($compareOpenSSLVersion < $openSSLVersion) { return -1; }
			else if ($compareOpenSSLVersion == $openSSLVersion) { return 0; }
			return 1;
		}
		
		return false;
	}
	
	/**
	 * Builds a number that can be compared to OPENSSL_VERSION_NUMBER from the parameters given. This is a modified
	 * version of the macro in the OpenSSL source.
	 *
	 * @param int $primary The '1' in 1.0.2g.
	 * @param int $major The '0' in 1.0.2g.
	 * @param int $minor The '2' in 1.0.2g.
	 * @param int $fixLetterIndex The 'g' in 1.0.2g.
	 * @param int $patch
	 * @return int
	 */
	public static function openssl_make_number_version($primary, $major, $minor, $fixLetterIndex = 0, $patch = 0) {
		return ((($primary & 0xff) << 28) + (($major & 0xff) << 20) + (($minor & 0xff) << 12) + (($fixLetterIndex & 0xff) << 4) + $patch);
	}
	
	/**
	 * Builds a text version of the OpenSSL version from a number-formatted one.
	 * 
	 * @param int $number
	 * @return string
	 */
	public static function openssl_make_text_version($number = OPENSSL_VERSION_NUMBER) {
		$primary = (($number >> 28) & 0xff);
		$major = (($number >> 20) & 0xff);
		$minor = (($number >> 12) & 0xff);
		$fix = (($number >> 4) & 0xff);
		$patch = ($number & 0xf); //Not currently handled -- would be values like alpha, beta, rc1
		
		$alphabet = str_split('abcdefghijklmnopqrstuvwxyz');
		$fixLetters = '';
		while ($fix > 26) {
			$fixLetters .= 'z';
			$fix -= 26;
		}
		if (array_key_exists($fix - 1, $alphabet)) {
			$fixLetters .= $alphabet[$fix - 1];
		}
		
		$version = "{$primary}.{$major}.{$minor}{$fixLetters}";
		return $version;
	}
}