<?php
/**
*	@author 	Ing. Israel Barragan C.  Email: ibarragan@behstant.com
*	@since 		10-Jul-2013
*	##########################################################################################
*	Comments:
*	This class handle functions or methos that will be helping the plugin Subscribe to Comments Reloaded
* 	Plugin URI: http://wordpress.org/extend/plugins/subscribe-to-comments-reloaded/
*	##########################################################################################
*	@version
*	##########################################################################################
*	1.0	|	10-Jul-2013	|	Creation of new class. Adding the method verifyXSS().
*	##########################################################################################
*/
class Helper {

	function __construct()
	{
	}

	function verifyXSS($value){
		$pattern = '~(<|<script>|</|</script>|(%3C|%3C/))~';
		$detected = false;
		if (preg_match($pattern, $value)) {
			$detected = true;
		}
		return $detected;
	}
}
?>