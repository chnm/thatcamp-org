<?php
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

    // Array of applicant info fields. May set up an option in plugin so admins can modify this list.
    $applicant_fields = array(
        'first_name',
        'last_name',
        'user_email',
        'user_url',
        'description',
        'previous_thatcamps',
        'user_title',
        'user_organization',
        'user_twitter',
        'tshirt_size',
        'dietary_preferences'
    );

    foreach ( $applicant_fields as $field) {
        $applicant_info[$field] = isset($_POST[$field]) ? $_POST[$field] : null;
    }

    $date = isset($_POST['date']) ? $_POST['date'] : null;
    $applicationText = isset($_POST['application_text']) ? $_POST['application_text'] : null;

    // Lets serialize the applicant_info before putting it in the database.
    $applicant_info = maybe_serialize($applicant_info);
    $applicant_email = isset($_POST['user_email']) ? $_POST['user_email'] : null;

    if (   $registration = thatcamp_registrations_get_registration_by_user_id($user_id)
        || $registration = thatcamp_registrations_get_registration_by_applicant_email($applicant_email) ) {
            return 'You have already submitted your registration.';
    } else {
        $wpdb->insert(
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
        $wpdb->update(
            $table,
            array('status' => $status),
            $idArray
            );
        if ($status == 'approved' && thatcamp_registrations_create_user_accounts()) {
            foreach ($ids as $id) {
                thatcamp_registrations_process_user($id);
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
		    add_existing_user_to_blog(array('user_id' => $userId, 'role' => $role));
	    }

            wp_new_user_notification($userId);

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
            add_user_to_blog($wpdb->blogid, $userId, $role);
            thatcamp_registrations_update_user_data($userId, $userInfo);
            wp_new_user_notification($userId, $randomPassword);
        }
    }

    return $userId;
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
    $sql = "SELECT * from " . $registrations_table . " WHERE id = " .$id;
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
    $sql = "SELECT * from " . $registrations_table . " WHERE applicant_email = '" .$applicant_email ."'";
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
    $sql = "SELECT * from " . $registrations_table . " WHERE user_id = " .$user_id;
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
        $wpdb->query("DELETE FROM " . $registrations_table . " WHERE id = '" . $id . "'");
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
 * @uses get_option()
 * @return array The array of options
 **/
function thatcamp_registrations_options()
{
    return get_option('thatcamp_registrations_options');
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
        return $options[$optionName];
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
        if (($record->user_id == 0 || $record->user_id == null) && !empty($record->applicant_info)) {
            return (object) maybe_unserialize($record->applicant_info);
        } else {
            return get_userdata($record->user_id);
        }
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
                $message = thatcamp_registrations_option('accepted_application_email');
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

        $subject = $subject . ': '.get_bloginfo('name');
        wp_mail($to, $subject, $message);

        return __('Email successfully sent!');
    }
    return false;
}

/**
 * Checks the option to create user accounts upon registration approval.
 *
 * @return boolean
 **/
function thatcamp_registrations_create_user_accounts()
{
    return (bool) thatcamp_registrations_option('create_user_accounts');
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
