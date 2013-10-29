<?php
if (!function_exists('AA_is_wpmu')):
	/**
	* Helper function which checks whether we are running wpmu
	*
	* @return bool true if we are on a wordpress mu system, false otherwise.
	*/
	function AA_is_wpmu() {
		// Wordpress 3.0+
		if (function_exists( 'is_multisite' )) {
			return is_multisite();
		}
		// Old WordpressMU versions
		else {
			global $wpmu_version, $wp_version;
			return (bool) ( (isset($wpmu_version)) || (strpos($wp_version, 'wordpress-mu')) );
		}
	}
endif;


if ( ! function_exists( 'AA_is_version' ) ) :
	/**
	* Helper function which checks the wordpress version
	* @param $version needed to be >= to
	* @return bool true if later site worpress version is >=, false otherwise.
	*/
    function AA_is_version( $version = '3.1' ) {
        global $wp_version;
        if ( version_compare( $wp_version, $version, '>=' ) ) {
            return false;
        }
        return true;
    }
endif;

if ( !function_exists( 'AA_is_bp' ) ):
	/**
	* Helper function which checks whether we are running buddypress
	*
	* @return bool true if we are on a system running buddypress, false otherwise.
	*/
	function AA_is_bp() {
		return defined( 'BP_VERSION' )
		|| defined( 'BP_CORE_VERSION' ); // buddypress versions < 1.0 (?)
	}
endif;

if ( !function_exists( 'AA_is_bbpress' ) ):
	/**
	* Helper function which checks whether we are running buddypress
	*
	* @return bool true if we are on a system running buddypress, false otherwise.
	*/
	function AA_is_bbpress() {
		if( function_exists( 'is_bbpress' ) ){
			return is_bbpress();
		}
		if ( is_admin() && is_plugin_active( 'bbpress/bbpress.php' ) ) {
	    	return true;
		}
		return false;
	}
endif;



if ( !function_exists( 'AA_in_arrayi' ) ):
	/**
	* Case insensitive version of in_array().
	*
	* @see http://us2.php.net/manual/en/function.in-array.php#88844
	* @return bool true if $needle is in $haystack, false otherwise.
	*/
	function AA_in_arrayi( $needle, $haystack ) {
		foreach( $haystack as $value )
		if( strtolower( $value ) == strtolower( $needle ) )
			return true;
		return false;
	}
endif;
if ( !function_exists( 'AA_array_in_array' ) ):
	/**
	* A simple function to type less when wanting to check if any one of many
	* values is in a single array.
	*
	* @see http://us2.php.net/manual/en/function.in-array.php#75263
	* @uses in_arrayi()
	* @return bool true if at least one value is in both arrays, false otherwise.
	*/
	function AA_array_in_array( $needle, $haystack ) {
	//Make sure $needle is an array for foreach
	if( !is_array( $needle ) ) $needle = array( $needle );
	//For each value in $needle, return TRUE if in $haystack
		foreach( $needle as $pin )
			if( AA_in_arrayi( $pin, $haystack ) ) 
				return TRUE;
			//Return FALSE if none of the values from $needle are found in $haystack
	return FALSE;
	}
endif;

if ( !function_exists( 'AA_atrim' ) ):
	/**
	* A simple implementation of "array_slice()" which preserves array keys.
	*
	* @see http://www.php.net/manual/en/function.array-slice.php#75865
	* @param $a array
	* @param $num number of items which to limit the array to
	* @return array with $num
	*/
	function AA_atrim( $a, $num ) {
		while( sizeof( $a ) > $num ) array_pop( $a );
			return $a;
	}
endif;

if ( !function_exists( 'AA_is_super_admin' ) ):
	/**
	* Implementation of the function "AA_is_super_admin" if it doesn't not exists.
	* Needed for support of wordpress versions < 3.0.
	*
	* @param $user_id (optional) user id; defaults to current user
	* @return boolean true if the supplied user is super admin (site admin)
	* @see http://core.trac.wordpress.org/changeset/12645
	*/
	function AA_is_super_admin( $user_id = '' ) {
		$user_login = '';
		if ( !empty( $user_id ) ) {
			$user_info = get_userdata( $user_id );
			if ( empty( $user_info->user_login ) )
				return false;
			$user_login = $user_info->user_login;
		}
		return is_site_admin( $user_login );
	}
endif;

if ( !function_exists( 'AA_donateButton' ) ):
	/**
	* Donation button code
	*
	* @param string $type Optional. Size / type of output, defaults is small
	* @param boolean $print Optional. To return a string or echo/print the output, default is false 
	*
	*/
	function AA_donateButton( $type = 'small', $print = false ) {

		if(	!$print ){
			ob_start();
		}

		switch ( $type ){
			case 'small': 
			?>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
					">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
			<?php 
			break;
			case 'large': 
			?>dd
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
					">
					<input  type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
			<?php 
			break;
			case 'link': 
			?>			
				<input onclick="window.open('https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=MZTZ5S8MGF75C&lc=CA&item_name=Author%20Avatars%20Plugin%20Support&item_number=authoravatars&currency_code=CAD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted','mywindow','width=400,height=200,toolbar=yes,location=yes,directories=yes,status=yes,menubar=yes,scrollbars=yes,copyhistory=yes,resizable=yes'); return false;" type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<?php 
		}

		if(	!$print ){
			return ob_get_clean();
		}
	}
endif;
?>