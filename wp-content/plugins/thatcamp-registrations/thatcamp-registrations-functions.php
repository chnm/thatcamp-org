<?php
/**
 * Returns the set of fields defined by the plugin
 *
 * @param string $type 'limited' returns only the fields the admin has
 *   whitelisted. 'all' returns all fields (mainly for Dashboard)
 */
function thatcamp_registrations_fields( $type = 'limited' ) {
	$fields = array(
		array(
			'id'          => 'first_name',
			'name'        => __( 'First Name', 'thatcamp-registrations' ),
			'required'    => true,
			'public'      => true,
		),
		array(
			'id'          => 'last_name',
			'name'        => __( 'Last Name', 'thatcamp-registrations' ),
			'required'    => true,
			'public'      => true,
		),
		array(
			'id'          => 'user_email',
			'name'        => __( 'Email', 'thatcamp-registrations' ),
			'required'    => true,
			'public'      => false,
		),
		array(
			'id'          => 'description',
			'name'        => __( 'Biography', 'thatcamp-registrations' ),
			'required'    => true,
			'explanation' => __( 'Tell us a little about yourself.', 'thatcamp-registrations' ),
			'type'        => 'textarea',
			'public'      => true,
		),
		array(
			'id'          => 'user_url',
			'name'        => __( 'Website', 'thatcamp-registrations' ),
			'explanation' => __( 'Example: thatcamp.org', 'thatcamp-registrations' ),
			'public'      => true,
		),
		array(
			'id'          => 'user_twitter',
			'name'        => __( 'Twitter Screenname', 'thatcamp-registrations' ),
			'explanation' => __( 'Example: @thatcamp', 'thatcamp-registrations' ),
			'public'      => true,
		),
		array(
			'id'          => 'user_title',
			'name'        => __( 'Position/Job Title', 'thatcamp-registrations' ),
			'explanation' => __( 'Examples: Assistant Professor, Instructional Technologist, Archivist, Software Engineer, Graduate student', 'thatcamp-registrations' ),
			'public'      => true,
		),
		array(
			'id'          => 'user_organization',
			'name'        => __( 'Organization', 'thatcamp-registrations' ),
			'explanation' => __( 'Examples: George Mason University, New York Public Library, Automattic', 'thatcamp-registrations' ),
			'public'      => true,
		),
		array(
			'id'          => 'discipline',
			'name'        => __( 'Discipline', 'thatcamp-registrations' ),
			'explanation' => __( 'e.g., Art History, English, Library Science', 'thatcamp-registrations' ),
			'public'      => true,
		),
		array(
			'id'          => 'days_attending',
			'name'        => __( 'Days Attending', 'thatcamp-registrations' ),
			'explanation' => __( 'Which days do you plan to attend this THATCamp?', 'thatcamp-registrations' ),
			'public'      => false,
			'type'        => 'select',
			'options'     => array(
				array(
					'value' => 'all',
					'text'  => __( 'All days', 'thatcamp-registrations' ),
				),
				array(
					'value' => 'first-day',
					'text'  => __( 'First day only', 'thatcamp-registrations' ),
				),
				array(
					'value' => 'second-day',
					'text'  => __( 'Second day only', 'thatcamp-registrations' ),
				),
			),
		),
		array(
			'id'          => 'technology_skill_level',
			'name'        => __( 'Technology Skill Level', 'thatcamp-registrations' ),
			'explanation' => __( 'I consider my technology skill level to be:', 'thatcamp-registrations' ),
			'public'      => false,
			'type'        => 'select',
			'options'     => array(
				array(
					'value' => '',
					'text'  => __( 'Select an answer', 'thatcamp-registrations' ),
				),
				array(
					'value' => 'beginner',
					'text'  => __( 'Beginner (interested in learning more)', 'thatcamp-registrations' ),
				),
				array(
					'value' => 'intermediate',
					'text'  => __( 'Intermediate (can get things up on the web)', 'thatcamp-registrations' ),
				),
				array(
					'value' => 'advanced',
					'text'  => __( 'Advanced (can code)', 'thatcamp-registrations' ),
				),
      ),
      'checked'    => false
		),
		array(
			'id'          => 'tshirt_size',
			'name'        => __( 'T-shirt Size', 'thatcamp-registrations' ),
			'public'      => false,
			'type'        => 'select',
			'options'     => array(
				array(
					'value' => '',
					'text'  => __( 'Select a t-shirt size', 'thatcamp-registrations' ),
				),
				array(
					'value' => 'mens_xs',
					'text'  => __( 'Men\'s XS - Chest 30-32', 'thatcamp-registrations' ),
				),
				array(
					'value' => 'mens_s',
					'text'  => __( 'Men\'s S - Chest 34-36', 'thatcamp-registrations' ),
				),
				array(
					'value' => 'mens_m',
					'text'  => __( 'Men\'s M - Chest 38-40', 'thatcamp-registrations' ),
				),
				array(
					'value' => 'mens_l',
					'text'  => __( 'Men\'s L - Chest 42-44', 'thatcamp-registrations' ),
				),
				array(
					'value' => 'mens_xl',
					'text'  => __( 'Men\'s XL - Chest 46-48', 'thatcamp-registrations' ),
				),
				array(
					'value' => 'mens_xxl',
					'text'  => __( 'Men\'s XXL - Chest 50-52', 'thatcamp-registrations' ),
				),
				array(
					'value' => 'mens_xxxl',
					'text'  => __( 'Men\'s XXXL - Chest 54-55', 'thatcamp-registrations' ),
				),
				array(
					'value' => 'womens_xs',
					'text'  => __( 'Women\'s XS - Chest 32-33', 'thatcamp-registrations' ),
				),
				array(
					'value' => 'womens_s',
					'text'  => __( 'Women\'s S - Chest 34-35', 'thatcamp-registrations' ),
				),
				array(
					'value' => 'womens_m',
					'text'  => __( 'Women\'s M - Chest 36-37', 'thatcamp-registrations' ),
				),
				array(
					'value' => 'womens_l',
					'text'  => __( 'Women\'s L - Chest 38-40', 'thatcamp-registrations' ),
				),
				array(
					'value' => 'womens_xl',
					'text'  => __( 'Women\'s XL - Chest 41-44', 'thatcamp-registrations' ),
				),
				array(
					'value' => 'womens_xxl',
					'text'  => __( 'Women\'s XXL - Chest 45-47', 'thatcamp-registrations' ),
				),
      ),
      'checked'   => false
		),
		array(
			'id'          => 'children',
			'name'        => __( 'Children', 'thatcamp-registrations' ),
			'explanation' => __( 'Kids are welcome! Let us know how many you plan to bring.', 'thatcamp-registrations' ),
      'public'      => false,
      'checked'     => false
		),
	);

	if ( 'limited' == $type ) {
		$limited = thatcamp_registrations_selected_fields();
		foreach ( $fields as $field_key => $field ) {
			if ( empty( $field['required'] ) && ! in_array( $field['id'], $limited ) ) {
				unset( $fields[ $field_key ] );
			}
		}
		$fields = array_values( $fields );
	}

	return $fields;
}

/**
 * Pull up the fields that the admin has opted to display on the registration
 * form
 *
 * We have a wrapper function here because the it should default to all fields,
 * when the admin has not yet saved anything
 */
function thatcamp_registrations_selected_fields() {
	$selected = get_option( 'thatcamp_registrations_selected_fields' );

	if ( '' == $selected ) {
    $fields = thatcamp_registrations_fields( 'all' );
    foreach ($fields as $key => $field) {
      if($field['checked'] === false) {
          unset($fields[$key]);
      }
    }
		$selected = wp_list_pluck( $fields, 'id' );
	}

	return $selected;
}

/**
 * Adds a single registration entry. This is a motley function.
 *
 * @param string The status of the registration record.
 **/
function thatcamp_registrations_add_registration($status = 'pending') {

    global $wpdb;
    $table = $wpdb->prefix . "thatcamp_registrations";

    $_POST = stripslashes_deep($_POST);

    // The user_id is set to the posted user ID, or null.
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;

    $applicant_info = array();

	$applicant_fields = wp_list_pluck( thatcamp_registrations_fields(), 'id' );

    foreach ( $applicant_fields as $field) {
        $applicant_info[$field] = isset($_POST[$field]) ? $_POST[$field] : null;
    }

    $date = isset($_POST['date']) ? $_POST['date'] : null;
    $applicationText = isset($_POST['application_text']) ? $_POST['application_text'] : null;

    // Lets serialize the applicant_info before putting it in the database.
    $applicant_info = maybe_serialize($applicant_info);
    $applicant_email = isset($_POST['user_email']) ? $_POST['user_email'] : null;

    // Check for an existing registration
    $user_exists = false;
    if ( ( ! is_null( $user_id ) && thatcamp_registrations_get_registration_by_user_id( $user_id ) ) ||
	   thatcamp_registrations_get_registration_by_applicant_email( $applicant_email ) ) {
	    $user_exists = true;
    }

    if ( $user_exists ) {
            return 'You have already submitted your registration.';
    } else {
        $reg_id = $wpdb->insert(
            $table,
            array(
                'applicant_info'            => $applicant_info,
                'applicant_email'           => $applicant_email,
                'application_text'          => $applicationText,
                'status'                    => $status,
                'date'                      => $date,
                'user_id'                   => $user_id
                )
            );
        thatcamp_registrations_send_applicant_email($applicant_email);

	thatcamp_registrations_send_admin_notification( $wpdb->insert_id );
    }
}

/**
 * Returns registration records based on type
 *
 * @param array The parameters on which to retrieve registrations
 **/
function thatcamp_registrations_get_registrations($params = array()) {
    global $wpdb;
    $registrations_table = $wpdb->prefix . "thatcamp_registrations";

    $sql = "SELECT * FROM " . $registrations_table;

    if( isset($params['id']) && $id = $params['id']) {
        $sql .= " WHERE id=".$params['id'];
        if( isset($params['status']) && $status = $params['status']) {
            $sql .= " AND status = CONVERT( _utf8 '$status' USING latin1 )";
        }
    } elseif ( isset($params['status']) && $status = $params['status']) {
        $sql .= " WHERE status = CONVERT( _utf8 '$status' USING latin1 )";
    }

    $sql .= " ORDER BY date ASC ";

    // echo $sql; exit;
    $results = $wpdb->get_results($sql, OBJECT);
    return $results;
}

/**
 * Processes an array of registrations based on ID. Uses mainly in the admin.
 *
 * @param array The IDs of the registration records.
 * @param string The status for the registration records.
 **/
function thatcamp_registrations_process_registrations($ids = array(), $status) {
    global $wpdb;
    $table = $wpdb->prefix . "thatcamp_registrations";

    $idArray = array();
    foreach ($ids as $id) {
        $idArray['id'] = $id;
    }

    if ($status && !empty($idArray)) {
	// Update the database entry
        $wpdb->update(
            $table,
            array('status' => $status),
            $idArray
            );

	// Maybe create/associate WP accounts with the registration
	if ( $status == 'approved' ) {
		foreach ($ids as $id) {
			thatcamp_registrations_process_user($id);
		}
	} else if ( $status == 'rejected' ) {
		foreach ( $ids as $id ) {
			thatcamp_registrations_maybe_remove_wp_user( $id );
		}
	}

        // Notify the user of the change
        if ( 'approved' == $status || 'rejected' == $status ) {
        	// Don't send 'pending' emails - they are send by the registration process
        	// @see thatcamp_registrations_add_registration()
        	$registration_data = thatcamp_registrations_get_registration_by_id( $id );

        	if ( !empty( $registration_data->applicant_email ) ) {
			thatcamp_registrations_send_applicant_email( $registration_data->applicant_email, $status );
		}
        }
    }

    return;
}

/**
 * Process a single registration based on ID. Uses mainly in the admin.
 *
 * @param int The ID of the registration record.
 * @param string The status for the registration record.
 **/
function thatcamp_registrations_process_registration($id, $status) {
    if (isset($id) && isset($status)) {
        thatcamp_registrations_process_registrations(array($id), $status);
    }
    return;
}

/**
 * Processes a WP user when adding/updating a registration record. Only
 * should be used if we're creating users with approved registrations.
 *
 * @param integer|null The User ID
 * @param array The array of user information.
 * @param $registrationId
 * @return integer The User ID
 **/
function thatcamp_registrations_process_user($registrationId = null, $role = 'author') {
    global $wpdb;

    /**
     * If the Registration ID is set, it means we already have a registration
     * record! Booyah. We'll use the user_id and application_info colums from
     * that record to process the user.
     */

    if ($registration = thatcamp_registrations_get_registration_by_id($registrationId)) {

        $userInfo = maybe_unserialize($registration->applicant_info);
        $userId = $registration->user_id ? $registration->user_id : email_exists($registration->applicant_email);

        // If we have a valid a User ID, we're dealing with an existing user.
        if ($userId) {

	    // Don't allow Administrators to be demoted. See #24
	    $wp_user = new WP_User( $userId );
	    if ( ! is_a( $wp_user, 'WP_User' ) || ! in_array( 'administrator', $wp_user->roles ) ) {
		if ( is_multisite() ) {
			add_existing_user_to_blog(array('user_id' => $userId, 'role' => $role));
		} else {
			$wp_user->set_role( $role );
		}

		thatcamp_registrations_existing_user_welcome_email( $userId );
	    }

        }
        // We're probably dealing with a new user. Lets create one and associate it to our blog.
        else {
            $randomPassword = wp_generate_password( 12, false );
            $userEmail = $registration->applicant_email;

            // Get a sanitized and unique username
            $uarray = split( '@', $userEmail );
            $userName = sanitize_user( $uarray[0], true ); // Use strict to get rid of nastiness
	    $unique_user_name = apply_filters( 'pre_user_login', $userName );

            // Guarantee unique usernames
            while ( username_exists( $unique_user_name ) ) {
            	$append = isset( $append ) ? $append + 1 : 1;
            	$unique_user_name = $userName . $append;
            }

            $userInfo['user_login'] = $unique_user_name;
            $userInfo['user_email'] = $userEmail;
            $userInfo['user_pass']  = $randomPassword;
            $userId = wp_insert_user( $userInfo );

	    if ( is_multisite() ) {
		    add_user_to_blog($wpdb->blogid, $userId, $role);
	    } else {
		    $wp_user = new WP_User( $userId );
		    $wp_user->set_role( $role );
	    }

            thatcamp_registrations_update_user_data($userId, $userInfo);
            wp_new_user_notification($userId, $randomPassword);
        }
    }

    return $userId;
}

/**
 * Remove the WP user associated with a registration from the current blog
 */
function thatcamp_registrations_maybe_remove_wp_user( $registration_id ) {

	$registration = thatcamp_registrations_get_registration_by_id( $registration_id );

	if ( $registration ) {
		$userId = $registration->user_id ? $registration->user_id : email_exists($registration->applicant_email);
		if ( $userId ) {
			if ( is_multisite() ) {
				remove_user_from_blog( $userId, get_current_blog_id() );
			} else {
				$user = new WP_User( $userId );
				$user->set_role( 'subscriber' );
			}
		}
	}
}

/**
 * Updates the user data.
 *
 **/
function thatcamp_registrations_update_user_data($userId, $params)
{
    if ( isset( $userId ) && $userData = get_userdata($userId) ) {
        foreach ($params as $key => $value) {
            update_user_meta( $userId, $key, $value );
        }
    }
}

/**
 * Gets registration record by ID.
 *
 **/
function thatcamp_registrations_get_registration_by_id($id)
{
    global $wpdb;
    $registrations_table = $wpdb->prefix . "thatcamp_registrations";
    $sql = $wpdb->prepare( "SELECT * from " . $registrations_table . " WHERE id = %d", $id );
    return $wpdb->get_row($sql, OBJECT);
}

/**
 * Gets registration record by applicant_email.
 *
 **/
function thatcamp_registrations_get_registration_by_applicant_email($applicant_email)
{
    global $wpdb;
    $registrations_table = $wpdb->prefix . "thatcamp_registrations";
    $sql = $wpdb->prepare( "SELECT * from " . $registrations_table . " WHERE applicant_email = %s", $applicant_email );
    return $wpdb->get_row($sql, OBJECT);
}

/**
 * Gets registration record by user_id.
 *
 * @param int $user_id The User ID.
 * @return object Registration record.
 **/
function thatcamp_registrations_get_registration_by_user_id($user_id)
{
    global $wpdb;
    $registrations_table = $wpdb->prefix . "thatcamp_registrations";
    $sql = $wpdb->prepare( "SELECT * from " . $registrations_table . " WHERE user_id = %d", $user_id );
    return $wpdb->get_row($sql, OBJECT);
}

/**
 * Deletes a registration record by ID.
 *
 **/
function thatcamp_registrations_delete_registration($id)
{
    global $wpdb;
    $registrations_table = $wpdb->prefix . "thatcamp_registrations";
    if($id) {
        $wpdb->query( $wpdb->prepare( "DELETE FROM " . $registrations_table . " WHERE id = %d", $id ) );
    }
}

/**
 * Creates a user from a registration record.
 *
 * @param integer $registrationId
 **/
function thatcamp_registrations_create_user($registrationId)
{
    if ($applicant = thatcamp_registrations_get_registration_applicant($registrationId)) {

        // if ( !is_int($applicant) ) {
            return $applicant;
        // }
    }
}

/**
 * Returns the value for thatcamp_registrations_options
 *
 * Also provides some defaults
 *
 * @uses get_option()
 * @return array The array of options
 **/
function thatcamp_registrations_options()
{
	$options = get_option('thatcamp_registrations_options');

	if ( ! is_array( $options ) ) {
		$options = array();
	}

	if ( empty( $options['open_registration'] ) ) {
		$options['open_registration'] = 0;
	}

	// We do an isset check here, because we want to allow for null values
	if ( ! isset( $options['admin_notify_emails'] ) ) {
		$options['admin_notify_emails'] = array( get_option( 'admin_email' ) );
	}

	if ( empty( $options['pending_application_email'] ) ) {
		$options['pending_application_email'] = sprintf( __( 'Your registration form for %1$s has been received. You will receive another e-mail when your registration has been approved. Please contact %2$s with any questions.', 'thatcamp-registrations' ),
			get_option( 'blogname' ),
			get_option( 'admin_email' )
		);
	}

	if ( empty( $options['approved_application_email'] ) ) {
		$options['approved_application_email'] = sprintf( __( 'Your registration for %1$s has been approved! We\'ll see you at %1$s. You should receive a separate e-mail with your login and password information. Please log in at %2$s and update your profile. It\'s never too early to begin proposing ideas for sessions, either -- see %3$s or %4$s for more information on that. Contact %5$s with any questions.', 'thatcamp-registrations' ),
			get_option( 'blogname' ),
			wp_login_url(),
			home_url( 'propose' ),
			home_url( 'proposals' ),
			get_option( 'admin_email' )
		);
	}

	if ( empty( $options['rejected_application_email'] ) ) {
		$options['rejected_application_email'] = '';
	}

	return $options;
}

/**
 * Returns the value for a single THATCamp Registrations option.
 *
 * @uses thatcamp_registrations_options()
 * @param string The name of the option
 * @return string
 **/
function thatcamp_registrations_option($optionName)
{
    if (isset($optionName)) {
        $options = thatcamp_registrations_options();
        return isset( $options[ $optionName ] ) ? $options[ $optionName ] : '';
    }
    return false;
}

function thatcamp_registrations_get_applicant_info($registration)
{
    global $wpdb;

    if ($registration) {
        $registrations_table = $wpdb->prefix . "thatcamp_registrations";
        $sql = "SELECT * from " . $registrations_table . " WHERE id = " .$registration->id;
        $record = $wpdb->get_row($sql, OBJECT);

        $applicantInfo = array();
        if (!empty($record->applicant_info)) {
            $applicantInfo = (array) maybe_unserialize($record->applicant_info);
        }

        if ($userData = get_userdata($record->user_id)) {
            // Set an array of custom user fields, since they're acquired by magic methods on WP_User.
            $profileFields = array(
                'first_name' => $userData->first_name,
                'last_name' => $userData->last_name,
                'user_url' => $userData->user_url,
                'user_twitter' => $userData->user_twitter,
                'user_title' => $userData->user_title,
                'user_organization' => $userData->user_organization,
                'description' => $userData->description,
                'discipline' => $userData->discipline
            );

            // Merge applicant info from registration with user data. User data overrides.
            $applicantInfo = array_merge($applicantInfo, $profileFields);
        }

        return (object) $applicantInfo;
    }
}

/**
 * Send a notification email to a THATCamp Registrations applicant
 *
 * @param string The applicant email address.
 * @param string The status of the registration. Options are 'pending',
 * 'approved', and 'rejected'. Default is pending.
 */
function thatcamp_registrations_send_applicant_email($to, $status = "pending")
{
    if (is_email($to)) {
        switch ($status) {
            case 'approved':
                $subject = __('Registration Approved', 'thatcamp-registrations');
                $message = thatcamp_registrations_option('approved_application_email');
            break;

            case 'rejected':
                $subject = __('Registration Rejected', 'thatcamp-registrations');
                $message = thatcamp_registrations_option('rejected_application_email');
            break;
            case 'pending':
            default:
                $subject = __('Registration Pending', 'thatcamp-registrations');
                $message = thatcamp_registrations_option('pending_application_email');
            break;
        }

	$subject = $subject . ': ' . get_bloginfo( 'name' );
	wp_mail( $to, $subject, stripslashes( $message ) );

        return __('Email successfully sent!');
    }
    return false;
}

/**
 * Sends an email to existing users when being added to a blog
 *
 * We do this in place of using wp_new_user_notification(), which doesn't work
 * well for our purpose
 *
 * @param int $user_id
 * @return bool
 */
function thatcamp_registrations_existing_user_welcome_email( $user_id ) {
	$user = new WP_User( $user_id );

	if ( is_a( $user, 'WP_User' ) ) {
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
		$subject  = sprintf( __( '[%s] Your username and password', 'thatcamp-registrations' ), $blogname );

		$content  = sprintf( __( 'Username: %s', 'thatcamp-registrations' ), $user->user_login );
		$content .= "\n\r";
		$content .= sprintf( __( 'Forgot your password? %s', 'thatcamp-registrations' ), add_query_arg( 'action', 'lostpassword', wp_login_url() ) );
		$content .= "\n\r";
		$content .= sprintf( __( 'Log in: %s', 'thatcamp-registrations' ), wp_login_url() );

		return wp_mail( $user->user_email, $subject, stripslashes( $content ) );
	}

	return false;
}

/**
 * Checks to see if user authentication is required.
 *
 * @return boolean
 **/
function thatcamp_registrations_user_required()
{
    return (bool) thatcamp_registrations_option('require_login');
}

/**
 * Checks if registration is open.
 *
 * @return boolean
 */
function thatcamp_registrations_registration_is_open()
{
    return (bool) thatcamp_registrations_option('open_registration');
}

/**
 * Generates a random string for a token
 **/
function thatcamp_registrations_generate_token()
{
    return sha1(microtime() . mt_rand(1, 100000));
}

/**
 * Pulls up the emails that are to be notified about new registrations, and
 * formats them as they should appear in a textarea
 */
function thatcamp_registrations_application_notification_emails_textarea() {
	$emails = thatcamp_registrations_option( 'admin_notify_emails' );
	echo esc_attr( implode( "\n", (array) $emails ) );
}

/**
 * For a new registration, send email notifications to admins who have requested them
 */
function thatcamp_registrations_send_admin_notification( $reg_id ) {
	$emails = thatcamp_registrations_option( 'admin_notify_emails' );
	if ( ! empty( $emails ) ) {
		$registration = thatcamp_registrations_get_registrations( array( 'id' => $reg_id ) );
		if ( ! empty( $registration ) ) {
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$subject  = sprintf( __( 'New registration at %s', 'thatcamp-registrations' ), $blogname );
			$content  = sprintf( __( 'You have received a new registration at %1$s. To view this registration, visit %2$s', 'thatcamp-registrations' ),
				$blogname,
				admin_url( '?page=thatcamp-registrations&id=' . intval( $reg_id ) )
			);

			foreach ( $emails as $email ) {
				wp_mail( $email, $subject, stripslashes( $content ) );
			}
		}
	}
}
