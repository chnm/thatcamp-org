<?php
/**
 * Contains the extending functionality
 * @since 1.32
 */
if ( ! function_exists( 'gglcptch_get_forms' ) ) {
	function gglcptch_get_forms() {
		global $gglcptch_options, $gglcptch_forms;

		$default_forms = array(
			'login_form'				=> array( 'form_name' => __( 'Login form', 'google-captcha' ) ),
			'registration_form'			=> array( 'form_name' => __( 'Registration form', 'google-captcha' ) ),
			'reset_pwd_form'			=> array( 'form_name' => __( 'Reset password form', 'google-captcha' ) ),
			'comments_form'				=> array( 'form_name' => __( 'Comments form', 'google-captcha' ) ),
			'contact_form'				=> array( 'form_name' => 'Contact Form' )
		);

		$custom_forms = apply_filters( 'gglcptch_add_custom_form', array() );
		$gglcptch_forms = array_merge( $default_forms, $custom_forms );

		foreach ( $gglcptch_forms as $form_slug => $form_data ) {
			$gglcptch_forms[ $form_slug ]['form_notice'] = gglcptch_get_form_notice( $form_slug );
		}

		$gglcptch_forms = apply_filters( 'gglcptch_forms', $gglcptch_forms );

		return $gglcptch_forms;
	}
}

if ( ! function_exists( 'gglcptch_get_sections' ) ) {
	function gglcptch_get_sections() {
		global $gglcptch_sections;

		$default_sections = array(
			'standard' => array(
				'name' => __( 'WordPress default', 'google-captcha' ),
				'forms' => array(
					'login_form',
					'registration_form',
					'reset_pwd_form',
					'comments_form'
				)
			),
			'external' => array(
				'name' => __( 'External Plugins', 'google-captcha' ),
				'forms' => array(
					'contact_form'
				)
			)
		);

		$custom_forms = apply_filters( 'gglcptch_add_custom_form', array() );

		$custom_sections = ( empty( $custom_forms ) ) ? array() : array( 'custom' => array( 'name' => __( 'Custom Forms', 'google-captcha' ), 'forms' => array_keys( $custom_forms ) ) );
		$gglcptch_sections = array_merge( $default_sections, $custom_sections );

		foreach ( $gglcptch_sections as $section_slug => $section_data ) {
			$gglcptch_sections[ $section_slug ]['section_notice'] = gglcptch_get_section_notice( $section_slug );
		}

		$gglcptch_sections = apply_filters( 'gglcptch_sections', $gglcptch_sections );

		return $gglcptch_sections;
	}
}

/* Add reCAPTCHA forms to the Limit Attempts plugin */
if ( ! function_exists( 'gglcptch_add_lmtttmpts_forms' ) ) {
	function gglcptch_add_lmtttmpts_forms( $forms = array() ) {
		if ( ! is_array( $forms ) ) {
			$forms = array();
		}

		$forms["gglcptch"] = array(
			'name'		=> __( 'Google Captcha Plugin', 'google-captcha' ),
			'forms'		=> array(),
		);

		$recaptcha_forms = gglcptch_get_forms();

		foreach ( $recaptcha_forms as $form_slug => $form_data ) {
			$forms["gglcptch"]["forms"]["{$form_slug}_recaptcha_check"] = $form_data;
			if ( empty( $form_data['form_notice'] ) ) {
				$forms["gglcptch"]["forms"]["{$form_slug}_recaptcha_check"]['form_notice'] = gglcptch_get_section_notice( $form_slug );
			}
		}

		return $forms;
	}
}

/**
 * Display section notice
 * @access public
 * @param  $section_slug	string
 * @return array    The action results
 */
if ( ! function_exists( 'gglcptch_get_section_notice' ) ) {
	function gglcptch_get_section_notice( $section_slug = '' ) {
		$section_notice = "";
		$plugins = array(
			/* example: */
			/* 'bbpress'			=> 'bbpress/bbpress.php' */
		);

		$is_network_admin = is_network_admin();

		if ( isset( $plugins[ $section_slug ] ) ) {
			$slug = explode( '/', $plugins[ $section_slug ] );
			$slug = $slug[0];
			$plugin_info = gglcptch_plugin_status( $plugins[ $section_slug ], get_plugins(), $is_network_admin );
			if ( 'activated' == $plugin_info['status'] ) {
				/* check required conditions */
			} elseif ( 'deactivated' == $plugin_info['status'] ) {
				$section_notice = '<a href="' . self_admin_url( 'plugins.php' ) . '">' . __( 'Activate', 'google-captcha' ) . '</a>';
			} elseif ( 'not_installed' == $plugin_info['status'] ) {
				$section_notice = sprintf( '<a href="http://wordpress.org/plugins/%s/" target="_blank">%s</a>', $slug, __( 'Install Now', 'google-captcha' ) );
			}
		}

		return apply_filters( 'gglcptch_section_notice', $section_notice, $section_slug );
	}
}

if ( ! function_exists( 'gglcptch_get_form_notice' ) ) {
	function gglcptch_get_form_notice( $form_slug = '' ) {
		global $wp_version, $gglcptch_plugin_info;
		$form_notice = "";

		$plugins = array(
			'contact_form'			=> array( 'contact-form-plugin/contact_form.php', 'contact-form-pro/contact_form_pro.php' )
		);

		if ( isset( $plugins[ $form_slug ] ) ) {
			$plugin_info = gglcptch_plugin_status( $plugins[ $form_slug ], get_plugins(), is_network_admin() );

			if ( 'activated' == $plugin_info['status'] ) {
				/* check required conditions */
			} elseif ( 'deactivated' == $plugin_info['status'] ) {
				$form_notice = '<a href="' . self_admin_url( 'plugins.php' ) . '">' . __( 'Activate', 'google-captcha' ) . '</a>';
			} elseif ( 'not_installed' == $plugin_info['status'] ) {
				if ( 'contact_form' == $form_slug ) {
					$form_notice = '<a href="https://bestwebsoft.com/products/wordpress/plugins/contact-form/?k=fa26df3911ebcd90c3e85117d6dd0ce0&pn=281&v=' . $gglcptch_plugin_info["Version"] . '&wp_v=' . $wp_version . '" target="_blank">' . __( 'Install Now', 'google-captcha' ) . '</a>';
				} else {
					$slug = explode( '/', $plugins[ $form_slug ] );
					$slug = $slug[0];
					$form_notice = sprintf( '<a href="http://wordpress.org/plugins/%s/" target="_blank">%s</a>', $slug, __( 'Install Now', 'google-captcha' ) );
				}
			}
		}
		return apply_filters( 'gglcptch_form_notice', $form_notice, $form_slug );
	}
}

if ( ! function_exists( 'gglcptch_add_actions' ) ) {
	function gglcptch_add_actions() {
		global $gglcptch_options, $wp_version, $gglcptch_ip_in_whitelist;

		$is_user_logged_in = is_user_logged_in();

		if ( ! empty( $gglcptch_options['login_form'] ) || ! empty( $gglcptch_options['reset_pwd_form'] ) || ! empty( $gglcptch_options['registration_form'] ) ) {
			add_action( 'login_enqueue_scripts', 'gglcptch_add_styles' );

			if ( gglcptch_is_recaptcha_required( 'login_form', $is_user_logged_in ) ) {
				add_action( 'login_form', 'gglcptch_login_display' );
				if ( ! $gglcptch_ip_in_whitelist ) {
					add_action( 'authenticate', 'gglcptch_login_check', 21, 1 );
				}
			}

			if ( gglcptch_is_recaptcha_required( 'registration_form', $is_user_logged_in ) ) {
				if ( ! is_multisite() ) {
					add_action( 'register_form', 'gglcptch_login_display', 99 );
					if ( ! $gglcptch_ip_in_whitelist ) {
						add_action( 'registration_errors', 'gglcptch_register_check', 10, 1 );
					}
				} else {
					add_action( 'signup_extra_fields', 'gglcptch_signup_display' );
					add_action( 'signup_blogform', 'gglcptch_signup_display' );
					if ( ! $gglcptch_ip_in_whitelist ) {
						add_filter( 'wpmu_validate_user_signup', 'gglcptch_signup_check', 10, 3 );
					}
				}
			}

			if ( gglcptch_is_recaptcha_required( 'reset_pwd_form', $is_user_logged_in ) ) {
				add_action( 'lostpassword_form', 'gglcptch_login_display' );

				if ( ! $gglcptch_ip_in_whitelist ) {
					add_action( 'allow_password_reset', 'gglcptch_lostpassword_check' );
				}
			}
		}

		/* Add Google Captcha to WP comments */
		if ( gglcptch_is_recaptcha_required( 'comments_form', $is_user_logged_in ) ) {
			add_action( 'comment_form_after_fields', 'gglcptch_commentform_display' );
			add_action( 'comment_form_logged_in_after', 'gglcptch_commentform_display' );
			if ( ! $gglcptch_ip_in_whitelist ) {
				add_action( 'pre_comment_on_post', 'gglcptch_commentform_check' );
			}
		}

		/* Add Google Captcha to Contact Form by BestWebSoft */
		if ( gglcptch_is_recaptcha_required( 'contact_form', $is_user_logged_in ) ) {
			add_filter( 'cntctfrm_display_captcha', 'gglcptch_display', 10, 1 );
			if ( ! $gglcptch_ip_in_whitelist ) {
				add_filter( 'cntctfrm_check_form', 'gglcptch_contact_form_check' );
			}
		}
	}
}

/* Echo google captcha */
if ( ! function_exists( 'gglcptch_echo_recaptcha' ) ) {
	function gglcptch_echo_recaptcha( $content = '' ) {
		echo gglcptch_display( $content );
	}
}

/* Add google captcha to the login form */
if ( ! function_exists( 'gglcptch_login_display' ) ) {
	function gglcptch_login_display() {

		global $gglcptch_options;

		if ( isset( $gglcptch_options['recaptcha_version'] ) && in_array( $gglcptch_options['recaptcha_version'], array( 'v1', 'v2' ) ) ) {
			if ( 'v2' == $gglcptch_options['recaptcha_version'] ) {
				$from_width = 302;
			} else {
				$from_width = 320;
				if ( 'clean' == $gglcptch_options['theme'] ) {
					$from_width = 450;
				}
			} ?>
			<style type="text/css" media="screen">
				.login-action-login #loginform,
				.login-action-lostpassword #lostpasswordform,
				.login-action-register #registerform {
					width: <?php echo $from_width; ?>px !important;
				}
				#login_error,
				.message {
					width: <?php echo $from_width + 20; ?>px !important;
				}
				.login-action-login #loginform .gglcptch,
				.login-action-lostpassword #lostpasswordform .gglcptch,
				.login-action-register #registerform .gglcptch {
					margin-bottom: 10px;
				}
			</style>
		<?php }
		echo gglcptch_display();
		return true;
	}
}

/* Check google captcha in login form */
if ( ! function_exists( 'gglcptch_login_check' ) ) {
	function gglcptch_login_check( $user ) {

		if ( is_wp_error( $user ) )
			return $user;

		$gglcptch_check = gglcptch_check();

		/* reCAPTCHA is not configured */
		if ( ! $gglcptch_check['response'] && $gglcptch_check['reason'] == 'ERROR_NO_KEYS' ) {
			return $user;
		}

		$la_result = gglcptch_handle_by_limit_attempts( $gglcptch_check['response'], 'login_form' );

		if ( true !== $la_result ) {
			$user = new WP_Error();

			if ( is_wp_error( $la_result ) ) {
				$user = $la_result;
			} elseif ( is_string( $la_result ) ) {
				$user->add( 'gglcptch_la_error', $la_result );
			}

			if ( $gglcptch_check['reason'] == 'VERIFICATION_FAILED' ) {
				wp_clear_auth_cookie();
			}

			if ( ! $gglcptch_check['response'] ) {
				$error_message = sprintf( '<strong>%s</strong>:&nbsp;%s', __( 'Error', 'google-captcha' ), gglcptch_get_message() );
				$user->add( 'gglcptch_error', $error_message );
			}
		}

		return $user;
	}
}

/* Check google captcha in lostpassword form */
if ( ! function_exists( 'gglcptch_register_check' ) ) {
	function gglcptch_register_check( $allow ) {

		$gglcptch_check = gglcptch_check();

		if ( ! $gglcptch_check['response'] && $gglcptch_check['reason'] == 'ERROR_NO_KEYS' ) {
			return $allow;
		}

		$la_result = gglcptch_handle_by_limit_attempts( $gglcptch_check['response'], 'registration_form' );

		if ( true !== $la_result ) {
			if ( ! is_wp_error( $allow ) ) {
				$allow = new WP_Error();
			}

			if ( is_wp_error( $la_result ) ) {
				$allow = $la_result;
			} elseif ( is_string( $la_result ) ) {
				$allow->add( 'gglcptch_la_error', $la_result );
			}

			if ( ! $gglcptch_check['response'] ) {
				$error_message = sprintf( '<strong>%s</strong>:&nbsp;%s', __( 'Error', 'google-captcha' ), gglcptch_get_message() );
				$allow->add( 'gglcptch_error', $error_message );
			}
		}

		return $allow;
	}
}

/* Check google captcha in lostpassword form */
if ( ! function_exists( 'gglcptch_lostpassword_check' ) ) {
	function gglcptch_lostpassword_check( $allow ) {

		$gglcptch_check = gglcptch_check();

		if ( ! $gglcptch_check['response'] && $gglcptch_check['reason'] == 'ERROR_NO_KEYS' ) {
			return $allow;
		}

		$la_result = gglcptch_handle_by_limit_attempts( $gglcptch_check['response'], 'reset_pwd_form' );

		if ( true !== $la_result ) {
			if ( ! is_wp_error( $allow ) ) {
				$allow = new WP_Error();
			}

			if ( is_wp_error( $la_result ) ) {
				$allow = $la_result;
			} elseif ( is_string( $la_result ) ) {
				$allow->add( 'gglcptch_la_error', $la_result );
			}

			if ( ! $gglcptch_check['response'] ) {
				$error_message = sprintf( '<strong>%s</strong>:&nbsp;%s', __( 'Error', 'google-captcha' ), gglcptch_get_message() );
				$allow->add( 'gglcptch_error', $error_message );
			}
		}

		return $allow;
	}
}

/* Add google captcha to the multisite login form */
if ( ! function_exists( 'gglcptch_signup_display' ) ) {
	function gglcptch_signup_display( $errors ) {
		if ( $error_message = $errors->get_error_message( 'gglcptch_error' ) ) {
			printf( '<p class="error gglcptch_error">%s</p>', $error_message );
		}
		echo gglcptch_display();
	}
}

/* Check google captcha in multisite login form */
if ( ! function_exists( 'gglcptch_signup_check' ) ) {
	function gglcptch_signup_check( $result ) {
		global $current_user;

		if ( is_admin() && ! defined( 'DOING_AJAX' ) && ! empty( $current_user->data->ID ) ) {
			return $result;
		}

		$gglcptch_check = gglcptch_check();

		if ( ! $gglcptch_check['response'] && $gglcptch_check['reason'] == 'ERROR_NO_KEYS' ) {
			return $result;
		}

		$errors = $result['errors'];

		$la_result = gglcptch_handle_by_limit_attempts( $gglcptch_check['response'], 'registration_form' );

		if ( true !== $la_result ) {
			if ( is_wp_error( $la_result ) ) {
				$la_result = $la_result->get_error_message();
				$errors->add( 'gglcptch_la_error', $la_result );
			} elseif ( is_string( $la_result ) ) {
				$errors->add( 'gglcptch_la_error', $la_result );
			}

			if ( ! $gglcptch_check['response'] ) {
				$error_message = sprintf( '<strong>%s</strong>:&nbsp;%s', __( 'Error', 'google-captcha' ), gglcptch_get_message() );
				$errors->add( 'gglcptch_error', $error_message );
			}

			$result['errors'] = $errors;
		}

		return $result;
	}
}

/* Add google captcha to the comment form */
if ( ! function_exists( 'gglcptch_commentform_display' ) ) {
	function gglcptch_commentform_display() {
		if ( gglcptch_is_hidden_for_role() ) {
			return;
		}
		echo gglcptch_display();
		return true;
	}
}

/* Check JS enabled for comment form  */
if ( ! function_exists( 'gglcptch_commentform_check' ) ) {
	function gglcptch_commentform_check() {

		$gglcptch_check = gglcptch_check();

		if ( ! $gglcptch_check['response'] && $gglcptch_check['reason'] == 'ERROR_NO_KEYS' ) {
			return;
		}

		$la_result = gglcptch_handle_by_limit_attempts( $gglcptch_check['response'], 'comments_form' );

		if ( true !== $la_result ) {
			$error_message = '';
			if ( is_wp_error( $la_result ) ) {
				$la_result = $la_result->get_error_message();
				$error_message .= $la_result . "<br />";
			} elseif ( is_string( $la_result ) ) {
				$error_message .= $la_result . "<br />";
			}

			if ( ! $gglcptch_check['response'] ) {
				$error_message .= gglcptch_get_message() . "<br />";
			}

			$error_message = sprintf(
				'<strong>%s</strong>:&nbsp;%s&nbsp;%s',
				__( 'Error', 'google-captcha' ),
				$error_message,
				__( 'Click the BACK button on your browser and try again.', 'google-captcha' )
			);
			wp_die( $error_message );
		}
		return;
	}
}

/* Check google captcha in BWS Contact Form */
if ( ! function_exists( 'gglcptch_contact_form_check' ) ) {
	function gglcptch_contact_form_check( $allow = true ) {
		if ( ! $allow || is_string( $allow ) || is_wp_error( $allow ) ) {
			return $allow;
		}

		$gglcptch_check = gglcptch_check();

		if ( ! $gglcptch_check['response'] && $gglcptch_check['reason'] == 'ERROR_NO_KEYS' ) {
			return true;
		}

		$la_result = gglcptch_handle_by_limit_attempts( $gglcptch_check['response'], 'contact_form' );

		if ( true !== $la_result ) {
			$allow = new WP_Error();

			if ( is_wp_error( $la_result ) ) {
				$allow = $la_result;
			} elseif ( is_string( $la_result ) ) {
				$allow->add( 'gglcptch_la_error', $la_result );
			}

			if ( ! $gglcptch_check['response'] ) {
				$error_message = sprintf( '<strong>%s</strong>:&nbsp;%s', __( 'Error', 'google-captcha' ), gglcptch_get_message() );
				$allow->add( 'gglcptch_error', $error_message );
			}

			return $allow;
		} else {
			return true;
		}
	}
}