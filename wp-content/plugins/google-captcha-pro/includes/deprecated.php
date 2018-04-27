<?php
/**
 * Contains the list of the deprecated functions
 * @since 1.26
 */

if ( ! function_exists( 'gglcptch_check_old_options' ) ) {
	function gglcptch_check_old_options( $is_network_admin ) {
		if ( $is_network_admin ) {
			if ( $old_options = get_site_option( 'gglcptchpr_options' ) ) {
				if ( isset( $old_options['gglcptch_network_apply'] ) ) {
					$old_options['network_apply'] = $old_options['gglcptch_network_apply'];
					$old_options['network_view'] = $old_options['gglcptch_network_view'];
					$old_options['network_change'] = $old_options['gglcptch_network_change'];
					unset(
						$old_options['gglcptch_network_apply'],
						$old_options['gglcptch_network_view'],
						$old_options['gglcptch_network_change']
					);
				}

				if ( ! get_site_option( 'gglcptch_options' ) )
					add_site_option( 'gglcptch_options', $old_options );
				else
					update_site_option( 'gglcptch_options', $old_options );
				delete_site_option( 'gglcptchpr_options' );
			}
		} else {
			if ( $old_options = get_option( 'gglcptchpr_options' ) ) {
				if ( ! get_option( 'gglcptch_options' ) )
					add_option( 'gglcptch_options', $old_options );
				else
					update_option( 'gglcptch_options', $old_options );
				delete_option( 'gglcptchpr_options' );
			}
		}
	}
}

/**
 * Adds information about deprecated functions to plugin settings
 * during its call
 * @see    gglcptchpr_display()
 * @param  string   $func   The function name
 * @return void
 */
if ( ! function_exists( 'gglcptch_detect_deprecated' ) ) {
	function gglcptch_detect_deprecated( $func ) {
		global $gglcptch_options;

		if ( empty( $gglcptch_options ) )
			$gglcptch_options = get_option( 'gglcptch_options' );
		if ( empty( $gglcptch_options['deprecated_usage'] ) )
			$gglcptch_options['deprecated_usage'] = array();
		if ( ! in_array( $func, $gglcptch_options['deprecated_usage'] ) ) {
			$gglcptch_options['deprecated_usage'][] = $func;
			update_option( 'gglcptch_options', $gglcptch_options );
		}

		if ( ! is_multisite() )
			return false;

		$site_options = get_site_option( 'gglcptch_options' );
		if ( empty( $site_options['deprecated_usage'] ) )
			$site_options['deprecated_usage'] = array();
		if ( ! in_array( $func, $site_options['deprecated_usage'] ) ) {
			$site_options['deprecated_usage'][] = $func;
			update_site_option( 'gglcptch_options', $site_options );
		}
	}
}

/**
 * Removes information about deprecated functions from plugin settings
 * after the click on the close cross in "deprecated function" message block
 * @see gglcptch_display_deprecated_function_message();
 */
if ( ! function_exists( 'gglcptch_remove_deprecated' ) ) {
	function gglcptch_remove_deprecated() {
		global $gglcptch_options;

		if ( empty( $gglcptch_options ) )
			$gglcptch_options = get_option( 'gglcptch_options' );

		if ( ! empty( $gglcptch_options['deprecated_usage'] ) ) {
			unset( $gglcptch_options['deprecated_usage'] );
			update_option( 'gglcptch_options', $gglcptch_options );
		}

		if ( ! is_multisite() )
			return false;

		$site_options = get_site_option( 'gglcptch_options' );
		if ( ! empty( $site_options['deprecated_usage'] ) ) {
			unset( $site_options['deprecated_usage'] );
			update_site_option( 'gglcptch_options', $site_options );
		}
	}
}

if ( ! function_exists( 'gglcptch_display_deprecated_function_message' ) ) {
	function gglcptch_display_deprecated_function_message() {
		global $gglcptch_options, $gglcptch_plugin_info;

		if ( empty( $gglcptch_options ) )
			$gglcptch_options = is_network_admin() ? get_site_option( 'gglcptch_options' ) : get_option( 'gglcptch_options' );

		if ( empty( $gglcptch_options['deprecated_usage'] ) )
			return '';

		if ( isset( $_GET['gglcptch_nonce'] ) &&  wp_verify_nonce( $_GET['gglcptch_nonce'], 'gglcptch_clean_deprecated' ) ) {
			gglcptch_remove_deprecated();
			return '';
		}

		$funcs = implode( ', ', $gglcptch_options['deprecated_usage'] );
		$link  = '<a href="http://support.bestwebsoft.com/hc/en-us/articles/202352499" target="_blank">' . __( 'the instruction', 'google-captcha-pro' ) . '</a>';
		$url = add_query_arg(
			array(
				'gglcptch_clean_deprecated' => '1',
				'gglcptch_nonce'            => wp_create_nonce( 'gglcptch_clean_deprecated' )
			),
			( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
		);
		$close_link = "<a href=\"{$url}\" class=\"close_icon notice-dismiss\"></a>";
		$message = sprintf( __( "Your site uses the function %1s from the %2s plugin that is deprecated since version %3s. Please replace it according to %4s. If you close this message it will appear in case if deprecated function would be called again.", 'google-captcha-pro' ), '<strong>`' . $funcs . '`</strong>', $gglcptch_plugin_info['Name'], '1.26', $link );
		return
			"<style>
				.gglcptch_deprecated_error {
					position: relative;
				}
				.gglcptch_deprecated_error a {
					text-decoration: none;
				}
			</style>
			<div class=\"gglcptch_deprecated_error error\"><p>{$message}</p>{$close_link}</div>";
	}
}

if ( ! function_exists( 'gglcptchpr_display' ) ) {
	function gglcptchpr_display( $content = false ) {
		gglcptch_detect_deprecated( __FUNCTION__ );
		return gglcptch_display( $content );
	}
}