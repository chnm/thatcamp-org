<?php
/**
 * Multi-level Navigation plugin core
 * 
 * @package    WordPress
 * @subpackage Multi-level Navigation
 */


/**
 * Multi-level Navigation core
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class MultiLevelNavigationCore {

	/**
	 * Class constructor
	 * 
	 * Adds methods to appropriate hooks
	 * 
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since Multi-level Navigation Plugin 2.3
	 * @access public
	 */
	public function __construct() {

		// Add actions
		add_action( 'wp_print_styles',  array( $this, 'print_styles'  ) );
		add_action( 'wp_print_scripts', array( $this, 'print_scripts' ) );
		add_action( 'wp_head',          array( $this, 'pixopoint_ad' ) );
		add_action( 'wp_head',          array( $this, 'print_inline_styles' ) );
		add_action( 'wp_head',          array( $this, 'print_ie_conditionals' ) );
		add_action( 'init',             array( $this, 'localization' ) );
		add_action( 'wp_head',          array( $this, 'print_inline_scripts' ) );

	}

	/**
	 * Add localization support
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since Multi-level Navigation Plugin 2.3
	 * @access public
	 */
	public function localization() {
		load_plugin_textdomain( 'pixopoint_mln', false, MULTILEVELNAVIGATION_DIR . 'languages/' );
	}

	/**
	 * Advertisement for PixoPoint
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since Multi-level Navigation Plugin 2.3
	 * @access public
	 */
	public function pixopoint_ad() {
		echo MULTILEVELNAVIGATION_AD;
	}

	/**
	 * Print styles to admin page
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since Multi-level Navigation Plugin 2.3
	 * @access public
	 */
	public function print_styles() {
		if ( get_mlnmenu_option( 'displaycss' ) != 'Inline' && get_mlnmenu_option( 'displaycss' ) != 'Disable' )
			wp_enqueue_style( 'multilevelnav_style', home_url( '/?mlnmenu=css' ) );
	}

	/**
	 * Print scripts to admin page
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since Multi-level Navigation Plugin 2.3
	 * @access public
	 */
	public function print_scripts() {
		if (
			get_mlnmenu_option( 'delay') != '0' || 
			get_mlnmenu_option( 'delay') != '0' || 
			get_mlnmenu_option( 'superfish_arrows') == 'on' || 
			get_mlnmenu_option( 'superfish_speed') != 'instant'
		) {
				wp_enqueue_script( 'superfish', MULTILEVELNAVIGATION_URL . '/scripts/superfish.js', array( 'jquery' ), '1.4.8' );
		}
		if ( get_mlnmenu_option( 'keyboard' ) == 'on' )
			wp_enqueue_script( 'sfkeyboard', MULTILEVELNAVIGATION_URL . '/scripts/suckerfish_keyboard.js', array( 'jquery' ), '1.0' );
	}

	/**
	 * Print IE conditionals to head
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since Multi-level Navigation Plugin 2.3
	 * @access public
	 */
	public function print_ie_conditionals() {
		echo '<!--[if lte IE 7]><script type="text/javascript" src="' . MULTILEVELNAVIGATION_URL . '/scripts/suckerfish_ie.js"></script><![endif]-->';
	}

	/**
	 * Print inline styles to head
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since Multi-level Navigation Plugin 2.3
	 * @access public
	 */
	public function print_inline_styles() {

		// Bail out if not set to display inline CSS
		if ( get_mlnmenu_option( 'displaycss') != 'Inline' )
			return;

		// Place CSS into variable
		$css = get_mlnmenu_option( 'css' ) . get_mlnmenu_option( '2_css' ); // Grab CSS from DB
		$css = str_replace( '../multi-level-navigation-plugin/', MULTILEVELNAVIGATION_URL . '/', $css ); // Add image URLs into place

		echo '
<!-- Inline CSS from Multi-level Navigation Plugin | this mode is activated via the admin page -->
<style type="text/css">' . $css . '</style>
';
	}

	/**
	 * Print inline scripts to head
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since Multi-level Navigation Plugin 2.3
	 * @access public
	 */
	public function print_inline_scripts() {
		if (
			get_mlnmenu_option( 'delay') != '0' || 
			get_mlnmenu_option( 'delay') != '0' || 
			get_mlnmenu_option( 'superfish_arrows') == 'on' || 
			get_mlnmenu_option( 'speed') != 'instant'
		) {
		echo '<script>';
		echo 'jQuery(document).ready(function() {
	jQuery("ul.sf-menu").superfish({
		animation:     {opacity:"show",height:"show"},  // fade-in and slide-down animation';
		echo '
		delay:        ' . get_mlnmenu_option( 'delay' ) . ',                            // delay on mouseout
		speed:        ';

		if ( get_mlnmenu_option( 'superfish_speed' ) == 'instant' )
			echo '1';
		else
			echo '"' . get_mlnmenu_option( 'superfish_speed' ) . '"';

		echo ',  // animation speed
		autoArrows:   "' . get_mlnmenu_option( 'superfish_arrows' ) . '",  // enable generation of arrow mark-up
		dropShadows:  "' . get_mlnmenu_option( 'superfish_shadows' ) . '"  // enable drop shadows
	});
});'; ?>

/**
* hoverIntent is similar to jQuery's built-in "hover" function except that
* instead of firing the onMouseOver event immediately, hoverIntent checks
* to see if the user's mouse has slowed down (beneath the sensitivity
* threshold) before firing the onMouseOver event.
*
* hoverIntent r5 // 2007.03.27 // jQuery 1.1.2+
* <http://cherne.net/brian/resources/jquery.hoverIntent.html>
*
* hoverIntent is currently available for use in all personal or commercial
* projects under both MIT and GPL licenses. This means that you can choose
* the license that best suits your project, and use it accordingly.
*
* // basic usage (just like .hover) receives onMouseOver and onMouseOut functions
* $("ul li").hoverIntent( showNav , hideNav );
*
* // advanced usage receives configuration object only
* $("ul li").hoverIntent({
*	sensitivity: 7, // number = sensitivity threshold (must be 1 or higher)
*	interval: 100,   // number = milliseconds of polling interval
*	over: showNav,  // function = onMouseOver callback (required)
*	timeout: 0,   // number = milliseconds delay before onMouseOut function call
*	out: hideNav    // function = onMouseOut callback (required)
* });
*
* @param  f  onMouseOver function || An object with configuration options
* @param  g  onMouseOut function  || Nothing (use configuration options object)
* @author    Brian Cherne <brian@cherne.net>
*/
(function($) {
	$.fn.hoverIntent = function(f,g) {
		// default configuration options
		var cfg = {
			sensitivity: <?php
				if ( get_mlnmenu_option( 'superfish_sensitivity' ) == 'high' )
					echo '10';
				if ( get_mlnmenu_option( 'superfish_sensitivity' ) == 'average' )
					echo '5';
				if ( get_mlnmenu_option( 'superfish_sensitivity' ) == 'low' )
					echo '1';
			?>,
			interval: <?php echo get_mlnmenu_option( 'superfish_delaymouseover' ); ?>,
			timeout: 0
		};
		// override configuration options with user supplied object
		cfg = $.extend(cfg, g ? { over: f, out: g } : f );

		// instantiate variables
		// cX, cY = current X and Y position of mouse, updated by mousemove event
		// pX, pY = previous X and Y position of mouse, set by mouseover and polling interval
		var cX, cY, pX, pY;

		// A private function for getting mouse position
		var track = function(ev) {
			cX = ev.pageX;
			cY = ev.pageY;
		};

		// A private function for comparing current and previous mouse position
		var compare = function(ev,ob) {
			ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
			// compare mouse positions to see if they've crossed the threshold
			if ( ( Math.abs(pX-cX) + Math.abs(pY-cY) ) < cfg.sensitivity ) {
				$(ob).unbind("mousemove",track);
				// set hoverIntent state to true (so mouseOut can be called)
				ob.hoverIntent_s = 1;
				return cfg.over.apply(ob,[ev]);
			} else {
				// set previous coordinates for next time
				pX = cX; pY = cY;
				// use self-calling timeout, guarantees intervals are spaced out properly (avoids JavaScript timer bugs)
				ob.hoverIntent_t = setTimeout( function(){compare(ev, ob);} , cfg.interval );
			}
		};

		// A private function for delaying the mouseOut function
		var delay = function(ev,ob) {
			ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
			ob.hoverIntent_s = 0;
			return cfg.out.apply(ob,[ev]);
		};

		// A private function for handling mouse 'hovering'
		var handleHover = function(e) {
			// next three lines copied from jQuery.hover, ignore children onMouseOver/onMouseOut
			var p = (e.type == "mouseover" ? e.fromElement : e.toElement) || e.relatedTarget;
			while ( p && p != this ) { try { p = p.parentNode; } catch(e) { p = this; } }
			if ( p == this ) { return false; }

			// copy objects to be passed into t (required for event object to be passed in IE)
			var ev = jQuery.extend({},e);
			var ob = this;

			// cancel hoverIntent timer if it exists
			if (ob.hoverIntent_t) { ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t); }

			// else e.type == "onmouseover"
			if (e.type == "mouseover") {
				// set "previous" X and Y position based on initial entry point
				pX = ev.pageX; pY = ev.pageY;
				// update "current" X and Y position based on mousemove
				$(ob).bind("mousemove",track);
				// start polling interval (self-calling timeout) to compare mouse coordinates over time
				if (ob.hoverIntent_s != 1) { ob.hoverIntent_t = setTimeout( function(){compare(ev,ob);} , cfg.interval );}

			// else e.type == "onmouseout"
			} else {
				// unbind expensive mousemove event
				$(ob).unbind("mousemove",track);
				// if hoverIntent state is true, then call the mouseOut function after the specified delay
				if (ob.hoverIntent_s == 1) { ob.hoverIntent_t = setTimeout( function(){delay(ev,ob);} , cfg.timeout );}
			}
		};

		// bind the function to the two event listeners
		return this.mouseover(handleHover).mouseout(handleHover);
	};
})(jQuery);
</script><?php
		
		
		}
	}

}
