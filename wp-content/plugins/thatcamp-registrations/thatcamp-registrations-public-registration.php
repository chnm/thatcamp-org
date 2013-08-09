<?php

if ( !class_exists( 'Thatcamp_Registrations_Public_Registration' ) ) :

class Thatcamp_Registrations_Public_Registration {

    private $options;
    private $current_user;

    function thatcamp_registrations_public_registration() {
        add_shortcode('thatcamp-registration', array($this, 'shortcode'));
        $this->options = get_option('thatcamp_registrations_options');
        $this->current_user = wp_get_current_user();
    }


    function shortcode($attr) {
        if (thatcamp_registrations_option('open_registration') == 1) {
            ob_start();
            $this->display_registration();
            $output_string = ob_get_contents();
            ob_end_clean();
            return $output_string;
        } else {
            return 'Registration is closed.';
        }
    }

    /**
     * Displays the registration information on the public site.
     *
     * @todo - Refactor most of the logic for checking whether to display the
     * user and registration forms.
     **/
    function display_registration() {
        $alerts = array();
        // Define some alerts if there are errors with the form.
        if ( !empty($_POST) ) {
            // Registration text is required.
            if ( empty( $_POST['application_text']) ) {
                $alerts['application_text'] = __('Please tell us why you want to come to THATCamp. What you write here will NOT be publicly displayed.', 'thatcamp-registrations');
            }

	    if ( ! empty( $_POST['tcppl-field'] ) ) {
	        $alerts['spammer'] = __( "It looks like you filled in the spammer field. No account for you!", 'thatcamp-registrations' );
	    }

            // User email is required.
            if (!is_user_logged_in()) {
                if ( empty( $_POST['first_name']) ) {
                    $alerts['application_text'] = __('You must add a first name.', 'thatcamp-registrations');
                }

                if ( empty( $_POST['last_name']) ) {
                    $alerts['application_text'] = __('You must add a last name.', 'thatcamp-registrations');
                }

                if ( empty( $_POST['user_email'] ) ) {
                    $alerts['user_email'] = __('You must add an email address.', 'thatcamp-registrations');
                }

		$email = $_POST['user_email'];
		$the_user = get_user_by( 'email', $email );
		$is_an_admin = is_a( $the_user, 'WP_User' ) && user_can( $the_user, 'manage_options' );
	        if ( $is_an_admin ) {
		    $alerts['user_email'] = __( 'You cannot register the email address of a site administrator.', 'thatcamp-registrations' );
		}

		if ( empty( $_POST['description'] ) ) {
			$alerts['description'] = __( 'You must provide a biography', 'thatcamp-registrations' );
		}
            }

            $userEmail = is_user_logged_in() ? $this->current_user->user_email : @$_POST['user_email'];

            if ($existingApp = thatcamp_registrations_get_registration_by_applicant_email($userEmail)) {
                $alerts['existing_application'] = __('You have already submitted the form with that email address.','thatcamp-registrations');
            }

        }

        // If user registration is required, and the user isn't logged in.
        if ( thatcamp_registrations_user_required() && !is_user_logged_in() ) {
            echo '<div>You must have a user account to complete the form. Please <a href="<?php echo wp_login_url( get_permalink() ); ?>" title="Login">log in</a>.</div>';
        }
        // If the currently authenticated user has submitted a registration.
        elseif (is_user_logged_in() && $existingApp = thatcamp_registrations_get_registration_by_user_id($this->current_user->ID)) {
            echo '<div>'.__('You have already submitted the form.','thatcamp-registrations').'</div>';

        }
        elseif ((!empty($_POST)) && empty($alerts)) {
            thatcamp_registrations_add_registration();
            echo '<p>The information you submitted has been saved.</p>';
        }
        else {

            if (!empty($alerts)) {
                foreach ($alerts as $alert) {
                    echo '<p style="background:#fc0; padding: 4px;">'.$alert.'</p>';
                }
            }

	    $login_link = add_query_arg( 'redirect_to', wp_guess_url(), wp_login_url() );

	    // Nudge the user to log in
	    if ( ! is_user_logged_in() ) {
		    echo "<h3>" . __( "Already have a THATCamp account?", 'thatcamp-registrations' ) . "</h3>";
		    echo "<p>" . sprintf( __( "If you've attended a THATCamp in the past, you probably already have an account on thatcamp.org. <a href='%s'>Log in</a> and we'll pre-fill some of your information for you.", 'thatcamp-registrations' ), $login_link ) . "</p>";
	    } else {
		    echo "<h3>" . __( "Welcome back!", 'thatcamp-registrations' ) . "</h3>";
		    echo "<p>" . sprintf( __( 'You are logged in as <strong>%1$s</strong>, using the the email address <strong>%2$s</strong>', 'thatcamp-registrations' ), $this->current_user->display_name, $this->current_user->user_email ) . "</p>";
	    }

            echo '<form method="post" action="">';

	    $this->_application_form();

	    // If user login is not required, display the user info form.
	    if ( !thatcamp_registrations_user_required() && !is_user_logged_in()) {
		    $this->_user_info_form();
            } elseif (is_user_logged_in()) {
                echo '<input type="hidden" name="user_id" value="'. $this->current_user->ID .'" />';
                echo '<input type="hidden" name="user_email" value="'. $this->current_user->user_email .'" />';
            }

            echo '<input type="submit" name="thatcamp_registrations_save_registration" value="'. __('Submit Registration', 'thatcamp-registrations') .'" />';
            echo '</form>';
        }
    }

	function _user_info_form() {
		$fields = thatcamp_registrations_fields();

		$public_fields = array();
		foreach ( $fields as $field ) {
			if ( ! empty( $field['public'] ) ) {
				$public_fields[] = '<strong>' . $field['name'] . '</strong>';
			}
		}
		$public_fields = implode( ', ', $public_fields );

		?>

		<fieldset>
			<legend><?php _e( 'Personal Information', 'thatcamp-registrations' ) ?></legend>

			<p class="explanation" style="margin: 1em 0 1em 0; color:crimson;"><?php printf( __( 'Please note that the following pieces of information may be displayed publicly on this website: %s. We will not display your e-mail address or your reasons for coming to THATCamp.', 'thatcamp-registrations' ), $public_fields ) ?></p>

			<?php foreach ( $fields as $field ) : ?>

				<?php $required = ! empty( $field['required'] ) ?>
				<?php $type = ! empty( $field['type'] ) ? $field['type'] : 'text' ?>

				<div>
					<?php /* LABEL */ ?>
					<label for="<?php echo esc_attr( $field['id'] ) ?>"><?php echo esc_html( $field['name'] ) ?><?php if ( $required ) : ?>* (required)<?php endif ?></label><br />

					<?php /* EXPLANATION */ ?>
					<?php if ( ! empty( $field['explanation'] ) ) : ?>
						<p class="explanation"><?php echo esc_html( $field['explanation'] ) ?></p>
					<?php endif ?>

					<?php /* INPUT */ ?>
					<?php if ( 'text' == $type ) : ?>

						<input type="text" name="<?php echo esc_attr( $field['id'] ) ?>" id="<?php echo esc_attr( $field['id'] ) ?>" class="textfield" />

					<?php elseif ( 'textarea' == $type ) : ?>

						<textarea cols="45" rows="8" name="<?php echo esc_attr( $field['id'] ) ?>" id="<?php echo esc_attr( $field['id'] ) ?>"></textarea>

					<?php elseif ( 'select' == $type ) : ?>

						<select name="<?php echo esc_attr( $field['id'] ) ?>" id="<?php echo esc_attr( $field['id'] ) ?>">
							<?php foreach ( $field['options'] as $option ) : ?>
								<option value="<?php echo esc_attr( $option['value'] ) ?>"><?php echo esc_attr( $option['text'] ) ?></option>
							<?php endforeach ?>
						</select>

					<?php endif ?>
				</div>

			<?php endforeach ?>
		</fieldset>

<p>&nbsp;</p>

	<style type="text/css">
		#tcppl { display: none; visibility: hidden; }
	</style>

	<div id="tcppl">
		<label for="tcppl-field"><?php _e( "This field should be left blank. It's a trap for spambots.", 'thatcamp-registrations' ) ?></label>
		<input type="text" id="tcppl-field" name="tcppl-field" />
	</div>
        <!-- Removed t-shirt size and dietary preferences fields. 10/17/2012 AF -->
    </fieldset>
    <?php
    }

    function _application_form() {
	?>
	<fieldset>
	<legend>Registration Information</legend>
	<div>
	<label for="application_text"><?php _e('Why do you want to come to THATCamp?', 'thatcamp-registrations'); ?>* (required)</label><br />
	<p class="explanation">
	<?php _e('In a few sentences, please tell us why you want to come to THATCamp. You might tell us what task
	you want to accomplish, what problem you want to solve, what new
	perspective you want to understand, what issue you want to discuss, or
	what skill you want to learn. Remember, though: no paper proposals!
	THATCamp is for working and talking with others, not for presenting to
	a silent audience.', 'thatcamp-registrations'); ?>
	</p>
	<textarea cols="45" rows="8" name="application_text"><?php echo @$_POST['application_text']; ?></textarea>
	</div>
	<input type="hidden" name="date" value="<?php echo current_time('mysql'); ?>">

	</fieldset>
	<?php
    }
}

endif; // class exists

$thatcamp_registrations_public_registration = new Thatcamp_Registrations_Public_Registration();
