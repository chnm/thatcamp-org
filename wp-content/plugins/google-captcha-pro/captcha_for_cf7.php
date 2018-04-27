<?php
/**
* This functions are used for adding captcha in Contact Form 7
**/

/* add shortcode handler */
if ( ! function_exists ( 'wpcf7_add_shortcode_bws_google_captcha_pro' ) ) {
	function wpcf7_add_shortcode_bws_google_captcha_pro() {
		if ( function_exists( 'wpcf7_add_form_tag' ) ) {
			wpcf7_add_form_tag( 'bwsgooglecaptcha', 'wpcf7_bws_google_captcha_pro_shortcode_handler', TRUE );
		} elseif ( function_exists( 'wpcf7_add_shortcode' ) ) { /* deprecated since CF7 v.4.6 */
			wpcf7_add_shortcode( 'bwsgooglecaptcha', 'wpcf7_bws_google_captcha_pro_shortcode_handler', TRUE );
		}
	}
}
/* display captcha */
if ( ! function_exists ( 'wpcf7_bws_google_captcha_pro_shortcode_handler' ) ) {
	function wpcf7_bws_google_captcha_pro_shortcode_handler( $tag ) {
		if ( class_exists( 'WPCF7_FormTag' ) ) {
			$tag = new WPCF7_FormTag( $tag );

			if ( empty( $tag->name ) )
				return '';

			$validation_error = wpcf7_get_validation_error( $tag->name );
			$class = wpcf7_form_controls_class( $tag->type );

			if ( $validation_error )
				$class .= ' wpcf7-not-valid';

			$atts = array();
			$atts['class'] = $tag->get_class_option( $class, $tag->name );
			$atts['id'] = $tag->get_option( 'id', 'id', true );
			$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );
			$atts['aria-required'] = 'true';
			$atts['type'] = 'text';
			$atts['name'] = $tag->name;
			$atts['value'] = '';

			$html = '<div ';
			if ( '' != $atts['id'] )
				$html .= 'id="' . $atts['id'] . '" ';
			$html .= 'class="wpcf7-form-control-wrap ' . $atts['class'] . '">' . gglcptch_display() .
				'<span class="wpcf7-form-control-wrap ' . $tag->name . '">' . $validation_error . '</span>' .
			'</div>';

			return $html;
		} elseif ( class_exists( 'WPCF7_Shortcode' ) ) { /* deprecated since CF7 v.4.6 */
			$tag = new WPCF7_Shortcode( $tag );

			if ( empty( $tag->name ) )
				return '';

			$validation_error = wpcf7_get_validation_error( $tag->name );
			$class = wpcf7_form_controls_class( $tag->type );

			if ( $validation_error )
				$class .= ' wpcf7-not-valid';

			$atts = array();
			$atts['class'] = $tag->get_class_option( $class, $tag->name );
			$atts['id'] = $tag->get_option( 'id', 'id', true );
			$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );
			$atts['aria-required'] = 'true';
			$atts['type'] = 'text';
			$atts['name'] = $tag->name;
			$atts['value'] = '';

			$html = '<div ';
			if ( '' != $atts['id'] )
				$html .= 'id="' . $atts['id'] . '" ';
			$html .= 'class="wpcf7-form-control-wrap ' . $atts['class'] . '">' . gglcptch_display() .
				'<span class="wpcf7-form-control-wrap ' . $tag->name . '">' . $validation_error . '</span>' .
			'</div>';

			return $html;
		}
	}
}

/* tag generator */
if ( ! function_exists ( 'wpcf7_add_tag_generator_bws_google_captcha_pro' ) ) {
	function wpcf7_add_tag_generator_bws_google_captcha_pro() {
		if ( ! function_exists( 'wpcf7_add_tag_generator' ) )
			return;
		$cf7_plugin_info = get_plugin_data( dirname( dirname( __FILE__ ) ) . "/contact-form-7/wp-contact-form-7.php" );
		if ( isset( $cf7_plugin_info ) && $cf7_plugin_info["Version"] >= '4.2' )
			wpcf7_add_tag_generator( 'bwsgooglecaptcha', __( 'BWS Google Captcha', 'google-captcha-pro' ), 'wpcf7-bwsgooglecaptcha', 'wpcf7_tg_pane_bws_google_captcha_pro_after_4_2' );
		elseif ( isset( $cf7_plugin_info ) && $cf7_plugin_info["Version"] >= '3.9' )
			wpcf7_add_tag_generator( 'bwsgooglecaptcha', __( 'BWS Google Captcha', 'google-captcha-pro' ), 'wpcf7-bwsgooglecaptcha', 'wpcf7_tg_pane_bws_google_captcha_pro_after_3_9' );
		else
			wpcf7_add_tag_generator( 'bwsgooglecaptcha', __( 'BWS Google Captcha', 'google-captcha-pro' ), 'wpcf7-bwsgooglecaptcha', 'wpcf7_tg_pane_bws_google_captcha_pro' );
	}
}

if ( ! function_exists ( 'wpcf7_tg_pane_bws_google_captcha_pro' ) ) {
	function wpcf7_tg_pane_bws_google_captcha_pro( &$contact_form ) { ?>
		<div id="wpcf7-bwsgooglecaptcha" class="hidden">
			<form action="">
				<table>
					<tr>
						<td>
							<?php _e( 'Id attribute', 'contact-form-7' ); ?><br />
							<input type="text" name="id" class="tg-id oneline" />
						</td>
						<td>
							<?php _e( 'Class attribute', 'contact-form-7' ); ?><br />
							<input type="text" name="class" class="tg-class oneline" />
						</td>
						<td>
							<?php _e( 'Name', 'contact-form-7' ); ?><br />
							<input type="text" name="name" class="tg-name oneline" />
						</td>
					</tr>
				</table>
				<div class="tg-tag">
					<?php _e( 'Copy this code and paste it into the form left.', 'contact-form-7' ); ?><br />
					<input type="text" name="bwsgooglecaptcha" class="tag" readonly="readonly" onfocus="this.select()" />
				</div>
			</form>
		</div>
	<?php }
}

if ( ! function_exists ( 'wpcf7_tg_pane_bws_google_captcha_pro_after_3_9' ) ) {
	function wpcf7_tg_pane_bws_google_captcha_pro_after_3_9( $contact_form ) { ?>
		<div id="wpcf7-bwsgooglecaptcha" class="hidden">
			<form action="">
				<table>
					<tr>
						<td>
							<?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?><br />
							<input type="text" name="id" class="tg-id oneline" />
						</td>
						<td>
							<?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?><br />
							<input type="text" name="class" class="tg-class oneline" />
						</td>
						<td>
							<?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?><br />
							<input type="text" name="name" class="tg-name oneline" />
						</td>
					</tr>
				</table>
				<div class="tg-tag">
					<?php echo esc_html( __( "Copy this code and paste it into the form left.", 'contact-form-7' ) ); ?><br />
					<input type="text" name="bwsgooglecaptcha" class="tag" readonly="readonly" onfocus="this.select()" />
				</div>
			</form>
		</div>
	<?php }
}

if ( ! function_exists ( 'wpcf7_tg_pane_bws_google_captcha_pro_after_4_2' ) ) {
	function wpcf7_tg_pane_bws_google_captcha_pro_after_4_2( $contact_form, $args = '' ) {
		$args = wp_parse_args( $args, array() );
		$type = 'bwsgooglecaptcha'; ?>
		<div class="control-box">
			<fieldset>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?></label></th>
							<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?></label></th>
							<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
							<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>
		<div class="insert-box">
			<input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />
			<div class="submitbox">
				<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
			</div>
		</div>
	<?php }
}

/* validation for captcha */
if ( ! function_exists ( 'wpcf7_bws_google_captcha_pro_validation_filter' ) ) {
	function wpcf7_bws_google_captcha_pro_validation_filter( $result, $tag ) {
		if ( class_exists( 'WPCF7_FormTag' ) ) {
			$tag = new WPCF7_FormTag( $tag );
			$name = $tag->name;

			$contact_form = wpcf7_get_current_contact_form();

			if ( ! $contact_form )
				return $result;

			$tags = $contact_form->scan_form_tags( array( 'type' => 'bwsgooglecaptcha' ) );

			if ( ! empty( $tags ) ) {

				$gglcptch_check = gglcptch_check( 'cf7' );
				if ( ! $gglcptch_check['response'] ) {
					$result_reason = implode( "\n", $gglcptch_check['errors']->get_error_messages() );
				}

				if ( ! empty( $result_reason ) ) {
					if ( is_array( $result ) ) {
						$result['valid'] = FALSE;
						$result['reason'][ $name ] = $result_reason;
					} elseif ( is_object( $result ) ) {
						/* cf after v4.1 */
						$result->invalidate( $tag, $result_reason );
					}
				}
			}
			return $result;
		} elseif ( class_exists( 'WPCF7_Shortcode' ) ) {
			$tag = new WPCF7_Shortcode( $tag );
			$name = $tag->name;

			$contact_form = wpcf7_get_current_contact_form();

			if ( ! $contact_form )
				return $result;

			$tags = $contact_form->form_scan_shortcode( array( 'type' => 'bwsgooglecaptcha' ) );

			if ( ! empty( $tags ) ) {

				$gglcptch_check = gglcptch_check( 'cf7' );

				if ( ! $gglcptch_check['response'] ) {
					$result_reason = implode( "\n", $gglcptch_check['errors']->get_error_messages() );
				}

				if ( ! empty( $result_reason ) ) {
					if ( is_array( $result ) ) {
						$result['valid'] = FALSE;
						$result['reason'][ $name ] = $result_reason;
					} elseif ( is_object( $result ) ) {
						/* cf after v4.1 */
						$result->invalidate( $tag, $result_reason );
					}
				}
			}
			return $result;
		}
	}
}

/* add messages for Captha errors */
if ( ! function_exists ( 'wpcf7_bws_google_captcha_pro_messages' ) ) {
	function wpcf7_bws_google_captcha_pro_messages( $messages ) {
		global $cptchpr_options;
		return array_merge(
			$messages,
			array(
				'wrong_bwsgooglecaptcha'	=> array(
					'description'	=> __( 'Error: You have entered an incorrect reCAPTCHA value', 'google-captcha-pro' ),
					'default'		=> __( 'Error: You have entered an incorrect reCAPTCHA value', 'google-captcha-pro' )
				)
			)
		);
	}
}

/* add warning message */
if ( ! function_exists ( 'wpcf7_bws_google_captcha_pro_display_warning_message' ) ) {
	function wpcf7_bws_google_captcha_pro_display_warning_message() {
		if ( empty( $_GET['post'] ) || ! ( $contact_form = wpcf7_contact_form( $_GET['post'] ) ) )
			return;

		if ( method_exists( $contact_form, 'scan_form_tags' ) ) {
			$has_tags = ( bool )$contact_form->scan_form_tags( array( 'type' => array( 'bwsgooglecaptcha' ) ) );
		} else {
			$has_tags = ( bool )$contact_form->form_scan_shortcode( array( 'type' => array( 'bwsgooglecaptcha' ) ) );
		}

		if ( ! $has_tags )
			return;
	}
}