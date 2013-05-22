<?php

if ( !class_exists( 'Thatcamp_Registrations_Admin' ) ) :

class Thatcamp_Registrations_Admin {

	function thatcamp_registrations_admin() {
		add_action( 'admin_init', array ( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * Catch any incoming requests before the screen is rendered
	 */
	function init() {
		if ( isset( $_GET['page'] ) && 'thatcamp-registrations' == $_GET['page'] && current_user_can( 'manage_options' ) ) {
			if ( isset( $_GET['id'] )&& isset( $_GET['action'] ) && 'spam' == $_GET['action'] ) {
				check_admin_referer( 'tcspam' );
				self::delete_spam_registration( intval( $_GET['id'] ) );
			}

			if ( isset( $_POST['tcr_bulk_action'] ) && isset( $_POST['registration_ids'] ) ) {
				check_admin_referer( 'tcr_bulk_action' );
				self::process_bulk_action( $_POST['tcr_bulk_action'], $_POST['registration_ids'] );
			}

			if ( isset( $_GET['trc_csv'] ) ) {
				self::render_csv();
			}
		}

		do_action( 'thatcamp_registrations_admin_init' );
	}

    function admin_menu() {
    	if ( function_exists( 'add_menu_page' ) ) {
    		add_menu_page(__('THATCamp Registrations', 'thatcamp-registrations'), __('THATCamp Registrations', 'thatcamp-registrations'), 'manage_options', 'thatcamp-registrations', array($this, 'registrations_display'));
    		add_submenu_page( 'thatcamp-registrations', __('Settings for THATCamp registrations', 'thatcamp-registrations'), __('Settings', 'thatcamp-registrations'), 'manage_options', 'thatcamp-registrations-settings', array($this, 'settings_display'));
    	}
    }

    /**
     * Displays various panels for the admin registration. If there is an ID set
     * in the URL, it will display a single registration record based on that ID.
     * Otherwise, it will display all the registration records in a table.
     */
    function registrations_display() {
        // if id is set in the URL, we need to view the registration with that ID.
        if ( $id = @$_GET['id'] ) {
            $registration = thatcamp_registrations_get_registration_by_id($id);
            $applicant = thatcamp_registrations_get_applicant_info($registration);
            $applicantUser = 0;
            if (($userId = email_exists($applicant->user_email)) && is_user_member_of_blog($userId)) {
                $applicantUser = 1;
            }

            if (isset($_POST['update_status'])) {
    			thatcamp_registrations_process_registration($_GET['id'], $_POST['status']);

			// If this is an approval, and if the user_account flag is set to 1,
			// attempt to create a new WP user (or associate an existing one)
			// with this registration
    			if (
				isset( $_POST['user_account'] ) &&
				$_POST['user_account'] == 1 &&
				'approved' == $_POST['status']
			   ) {
				thatcamp_registrations_process_user($id);
    			}

			wp_redirect( get_admin_url() . 'admin.php?page=thatcamp-registrations&applicant_saved=1' );
	    }
        }
    ?>
    <style type="text/css" media="screen">
        #thatcamp-registrations-panel {
            background: #fff;
        	margin: 25px 15px 25px 15px;
        	padding: 20px;
        	-moz-border-radius: 6px;
        	-webkit-border-radius: 6px;
        	border-radius: 6px;
        	-moz-box-shadow: #ddd 0 -1px 10px;
        	-webkit-box-shadow: #ddd 0 -1px 10px;
        	-khtml-box-shadow: #ddd 0 -1px 10px;
        	box-shadow: #ddd 0 -1px 10px;
        	color: #555;
        	overflow: hidden;

        }

        #thatcamp-registrations-applicant-info th,
        #thatcamp-registrations-applicant-info td {
            border-bottom: 1px dotted #ddd;
            line-height: 2em;
        }
        #thatcamp-registrations-applicant-info th {
            width: 20%;
        }

        #thatcamp-registrations-list-link {
            display:block;
            float:right;
            width: 20%;
            background: #eee;
            color: #333;
            text-decoration:none;
            text-align:center;
            padding: 10px 20px;
            border:1px solid #ddd;
            -moz-border-radius: 6px;
        	-webkit-border-radius: 6px;
        	border-radius: 6px;
        }
        #thatcamp-registrations-list-link:link,
        #thatcamp-registrations-list-link:visited {
            color: #21759B;
        }
        #thatcamp-registrations-list-link:hover,
        #thatcamp-registrations-list-link:active {
            color: #D54E21;
            background: #f9f9f9;
        }

        h2, h3 {
        margin-bottom: .25em;
        }
    </style>
        <div class="wrap">
            <h2><?php echo _e('THATCamp Registrations'); ?></h2>
            <?php if ($id): ?>
            <div id="thatcamp-registrations-panel">
                <a id="thatcamp-registrations-list-link" href="admin.php?page=thatcamp-registrations">Back to registrations list</a>

                <h2>Registration from <?php echo $applicant->first_name; ?> <?php echo $applicant->last_name; ?> (<?php echo $applicant->user_email; ?>)</h2>

               <form action="admin.php?page=thatcamp-registrations&amp;id=<?php echo $id; ?>&amp;noheader=true" method="post">
                    <h3>Registration Status</h3>

                    <select name="status">
                        <option name="pending" id="pending" value="pending"<?php if($registration->status == "pending") { echo ' selected="selected"';} ?>><?php _e('Pending', 'thatcamp-registrations'); ?> </option>
                        <option name="approved" id="approved" value="approved"<?php if($registration->status == "approved") { echo ' selected="selected"';} ?>><?php _e('Approved', 'thatcamp-registrations'); ?> </option>
                        <option name="rejected" id="rejected" value="rejected"<?php if($registration->status == "rejected") { echo ' selected="selected"';} ?>><?php _e('Rejected', 'thatcamp-registrations'); ?> </option>
                    </select>

                    <input type="submit" name="update_status" value="Update Status">

                    <span style="display:none;">
                    <select name="user_account">
                        <option value="0">No</option>
                        <option value="1"<?php if($applicantUser == 1) { echo ' selected="selected"';} ?>Yes</option>
                    </select>
                    </span>

                    <h3>Applicant Site User Account</h3>
                    <p class="description"><?php if($applicantUser == 1) echo ('Applicant has a user account on this site.'); ?></p>
                    <p class="description"><?php if($applicantUser == 0) echo ('Applicant does not have a user account on this site. If your THATCamp Registrations settings allow it, changing the applicant\'s status to Approved will add the applicant to this site as a user.'); ?></p>
                </form>

                	<h3>Registration Text</h3>
                	<?php echo $registration->application_text; ?>

                	<?php if ( ! empty( $registration->additional_information ) ) : echo $registration->additional_information; endif; ?>

                	<h3>Biography</h3>
		<?php echo $applicant->description; ?>

		<h3>Website</h3>
		<?php echo $applicant->user_url; ?>

		<h3>Twitter</h3>
		<?php echo $applicant->user_twitter; ?>

		<h3>Title</h3>
		<?php echo $applicant->user_title; ?>

		<h3>Organization</h3>
		<?php echo $applicant->user_organization; ?>

		<h3><?php _e( 'Discipline', 'thatcamp-registrations' ) ?></h3>
		<?php echo $applicant->discipline; ?>

		<h3><?php _e( 'Technology Skill Level', 'thatcamp-registrations' ) ?></h3>
		<?php echo $applicant->technology_skill_level; ?>

		<h3><?php _e( 'Days Attending', 'thatcamp-registrations' ) ?></h3>
		<?php echo $applicant->days_attending ?>

		<h3><?php _e( 'T-shirt Size', 'thatcamp-registrations' ) ?></h3>
		<?php echo $applicant->tshirt_size ?>

</div>
            <?php
            // Otherwise, we need to view the list of registrations.
            else:

            ?>

            <?php
            /*
            Get list of registrations. Sort by:

            1. All registrations
            2. Pending registrations
            3. Approved registrations
            4. Rejected registrations

            List needs a bulk action to change status of checked registrations.

            */ ?>

            <?php
            $options = get_option('thatcamp_registrations_options');
            if ( empty($options)): ?>
            <div class="updated">
                <p><?php _e('You have not updated your THATCamp Registrations settings.'); ?> <a href="admin.php?page=thatcamp-registrations-settings"><?php _e('Update your settings.'); ?></a></p>
            </div>
            <?php endif; ?>

	    <?php if ( ! empty( $_GET['success'] ) ) : ?>
		<div class="updated">
		<?php
			switch ( $_GET['success'] ) {
				case 'approved' :
					$message = __( 'Successfully approved!', 'thatcamp-registrations' );
					break;

				case 'pending' :
					$message = __( 'Successfully marked as pending!', 'thatcamp-registrations' );
					break;

				case 'rejected' :
					$message = __( 'Successfully rejected!', 'thatcamp-registrations' );
					break;

				case 'spammed' :
					$message = __( 'Successfully spammed!', 'thatcamp-registrations' );
					break;
			}
		?>

		<p><?php echo $message ?></p>
		</div>
	    <?php endif ?>

            <?php
            $bootcampRegistrations = thatcamp_registrations_get_registrations(array('bootcamp' => '1'));
            $registrations = thatcamp_registrations_get_registrations();
            if ($registrations): ?>

                <p>There are <?php echo count($registrations); ?> total registrations.</p>
                <form action="" method="post">

		<div class="tablenav top">
			<div class="alignleft actions">
				<select name="tcr_bulk_action">
					<option selected="selected" value=""><?php _e( 'Bulk Actions', 'thatcamp-registrations' ) ?></option>
					<option value="mark_approved"><?php _e( 'Mark Approved', 'thatcamp-registrations' ) ?></option>
					<option value="mark_pending"><?php _e( 'Mark Pending', 'thatcamp-registrations' ) ?></option>
					<option value="mark_rejected"><?php _e( 'Mark Rejected', 'thatcamp-registrations' ) ?></option>
					<option value="mark_spam"><?php _e( 'Spam', 'thatcamp-registrations' ) ?></option>
				</select>

				<input type="submit" value="Apply" class="button-secondary action" id="doaction" name="">
			</div>

			<div class="alignright actions">
				<a class="button-secondary action" href="<?php echo add_query_arg( 'trc_csv', '1' ) ?>"><?php _e( 'Export to CSV' ) ?></a>
			</div>
		</div>

                <table class="widefat fixed" cellspacing="0">
                <thead>
                <tr class="thead">
		    <th id="cb" class="manage-column column-cb check-column" scope="col"><input type="checkbox" /></th>
                    <th>Applicant Name</th>
                    <th>Applicant Email</th>
                    <th>Status</th>
                    <th>View</th>
                    <th>Mark Spam</th>
                </tr>
                </thead>

                <tfoot>
                <tr class="thead">
		    <th id="cb" class="manage-column column-cb check-column" scope="col"><input type="checkbox" /></th>
                    <th>Applicant Name</th>
                    <th>Applicant Email</th>
                    <th>Status</th>
                    <th>View</th>
                    <th>Spam</th>
                </tr>
                </tfoot>

                <tbody id="users" class="list:user user-list">
                <?php foreach ( $registrations as $registration ): ?>
                    <tr>
                        <?php $applicant = thatcamp_registrations_get_applicant_info($registration); ?>
			<th class="check-column"><input type="checkbox" name="registration_ids[]" value="<?php echo intval( $registration->id ) ?>" /></th>
                        <td><?php echo $applicant->first_name; ?> <?php echo $applicant->last_name; ?></td>
                        <td><?php echo $applicant->user_email; ?></td>
                        <td><?php echo ucwords($registration->status); ?></td>
                        <td><a href="admin.php?page=thatcamp-registrations&amp;id=<?php echo $registration->id; ?>">View Full Registration</a></td>
                        <td><a href="<?php echo wp_nonce_url( add_query_arg( array( 'id' => $registration->id, 'page' => 'thatcamp-registrations', 'action' => 'spam' ), 'admin.php' ), 'tcspam' ) ?>" class="spam" onclick="return confirm('Are you sure you want to delete this registration as spam? There is no undo.');">Spam</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                </table>

		<?php wp_nonce_field( 'tcr_bulk_action' ) ?>
                </form>
                <?php else: ?>
                    <p>You don't have any registrations yet.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php
    }

    function settings_display() {

        if ( isset($_POST['thatcamp_registrations_save_changes']) ) {

            $newOptions = array(
                'open_registration'             =>  $_POST['open_registration'],
                'pending_application_email'     =>  $_POST['pending_application_email'],
                'approved_application_email'    =>  $_POST['approved_application_email'],
                'rejected_application_email'    =>  $_POST['rejected_application_email']
                );

	    $admin_notify_emails = $_POST['admin_notify_emails'];
	    $admin_notify_emails = array_map( 'trim', explode( "\n", $admin_notify_emails ) );
	    $newOptions['admin_notify_emails'] = $admin_notify_emails;

            update_option('thatcamp_registrations_options', $newOptions);

	    $fields = isset( $_POST['fields'] ) ? $_POST['fields'] : array();
	    update_option( 'thatcamp_registrations_selected_fields', $fields );
        }

        $options = thatcamp_registrations_options();

    ?>
        <div class="wrap">

            <h2><?php echo _e('Settings for THATCamp Registrations', 'thatcamp-registrations'); ?></h2>
            <h3><?php echo _e('To enable registration, create a registration page and type the shortcode [thatcamp-registration] on the page.') ?></h3>

            <form action="" method="post">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label for="open_registration"><?php _e( 'Open registration?', 'thatcamp-registrations' ) ?></label></th>
                        <td>
                            <select name="open_registration">
                                <option value="0"><?php _e('No'); ?> </option>
                                <option value="1"<?php if($options['open_registration'] == 1) { echo ' selected="selected"';} ?>><?php _e('Yes'); ?> </option>
                            </select>
                            <p class="description"><?php _e('Before you open registration, create a page called (for instance) &#8220;Register&#8221; and type the shortcode &#x5b;thatcamp-registration&#x5d; on the page. When you open registrations, the registration form will be generated on that page. ', 'thatcamp-registrations'); ?></p>
                        </td>
                    </tr>

		    <tr>
                        <th scope="row"><label for="admin_notify_emails"><?php _e( 'Application notification addresses', 'thatcamp-registrations' ) ?></label></th>
                        <td>
                            <textarea name="admin_notify_emails"><?php thatcamp_registrations_application_notification_emails_textarea() ?></textarea>

                            <p class="description"><?php _e('These addresses will receive a notification whenever a new registration is submitted. One per line.', 'thatcamp-registrations'); ?></p>
                        </td>
                    </tr>

                    <?php /* Removing require login before registering because no one needs that. ?>
                    <tr valign="top">
                        <th scope="row"><label for="require_login"><?php _e( 'Require login before registering?', 'thatcamp-registrations' ) ?></label></th>
                        <td>
                            <select name="require_login">
                                <option value="0"><?php _e('No'); ?> </option>
                                <option value="1"<?php if($options['require_login'] == 1) { echo ' selected="selected"';} ?>><?php _e('Yes'); ?> </option>
                            </select>
                            <p class="description"><?php _e('If &#8220;Yes&#8221; users will be required to log in before completing the registration form.'); ?></p>
                        </td>
                    </tr>
                    <?php */ ?>


                    <?php /* Removing auto-approve field until this feature works. ?>
                    <tr valign="top">
                        <th scope="row"><label for="auto_approve_applications"><?php _e('Automatically approve registrations', 'thatcamp-registrations'); ?></label></th>
                        <td>
                            <input type="text" name="auto_approve_applications" value="<?php echo $options['auto_approve_applications']; ?>" />
                            <p class="description"><?php _e('If you wish THATCamp Registrations to automatically approve a certain number of registrations, fill in that number here. If left blank, or set to 0, no registrations will be automatically approved.', 'thatcamp-registrations'); ?></p>
                        </td>
                    </tr>
                    <?php */ ?>
                    <tr valign="top">
                        <th scope="row"><label for="pending_application_email"><?php _e('Pending registration email', 'thatcamp-registrations'); ?></label></th>
                        <td>
                            <textarea name="pending_application_email" id="pending_application_email" rows="5" cols="50"><?php if( !empty($options['pending_application_email']) ) echo stripslashes( $options['pending_application_email'] ); ?></textarea>
                            <p class="description"><?php _e('This e-mail will be sent by the system from an automated account; you may therefore wish to include your own name and e-mail address in the message itself so that users may contact you. The e-mail will be composed in HTML format, so links and e-mail addresses will automatically be hyperlinked, and no additional HTML codes are necessary. If no text is entered, no e-mail will be sent.'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="approved_application_email"><?php _e('Approved registration email', 'thatcamp-registrations'); ?></label></th>
                        <td>
                            <textarea name="approved_application_email" id="approved_application_email" rows="5" cols="50"><?php if( !empty($options['approved_application_email']) ) echo stripslashes( $options['approved_application_email'] ); ?></textarea>
                             <p class="description"><?php _e('This e-mail will be sent by the system from an automated account; you may therefore wish to include your own name and e-mail address in the message itself so that users may contact you. The e-mail will be composed in HTML format, so links and e-mail addresses will automatically be hyperlinked, and no additional HTML codes are necessary. If no text is entered, no e-mail will be sent.'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="rejected_application_email"><?php _e('Rejected registration email', 'thatcamp-registrations'); ?></label></th>
                        <td>
                            <textarea name="rejected_application_email" id="rejected_application_email" rows="5" cols="50"><?php if( !empty($options['rejected_application_email']) ) echo stripslashes( $options['rejected_application_email'] ); ?></textarea>
                            <p class="description"><?php _e('This e-mail will be sent by the system from an automated account; you may therefore wish to include your own name and e-mail address in the message itself so that users may contact you. The e-mail will be composed in HTML format, so links and e-mail addresses will automatically be hyperlinked, and no additional HTML codes are necessary. If no text is entered, no e-mail will be sent.'); ?></p>

                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e( 'Registration fields', 'thatcamp-registrations' ) ?></th>
                        <td>
				<?php $fields = thatcamp_registrations_fields( 'all' ) ?>
				<?php $selected = thatcamp_registrations_selected_fields() ?>
				<?php foreach ( $fields as $field ) : ?>
					<?php
					$checked  = ! empty( $field['required'] ) || in_array( $field['id'], $selected ) ? ' checked="checked"' : '';
					$disabled = ! empty( $field['required'] ) ? ' disabled="disabled"' : '';
					?>

					<input type="checkbox" name="fields[]" value="<?php echo esc_attr( $field['id'] ) ?>"<?php echo $checked ?> <?php echo $disabled ?> /> <?php echo esc_html( $field['name'] ) ?> <?php if ( $disabled ) : ?><em><?php _e( '(required)', 'thatcamp-registrations' ) ?></em><?php endif ?><br />
				<?php endforeach ?>
			</td>
		    </tr>

                    <tr valign="top">
                        <th scope="row"></th>
                        <td>
                            <input type="submit" name="thatcamp_registrations_save_changes" class="button-primary" value="<?php _e('Save Changes'); ?>" />
                        </td>
                    </tr>
                </table>
                <br />
            </form>
        </div>
    <?php
    }

	function delete_spam_registration( $reg_id ) {
		thatcamp_registrations_delete_registration( $reg_id );
		$redirect_to = remove_query_arg( array( 'id', 'action', '_wpnonce' ) );
		$redirect_to = add_query_arg( 'success', 'spammed', $redirect_to );
		wp_safe_redirect( $redirect_to );
	}

	function process_bulk_action( $action, $reg_ids ) {
		$reg_ids = wp_parse_id_list( $reg_ids );

		foreach ( $reg_ids as $reg_id ) {
			switch( $action ) {
				case 'mark_approved' :
					$status = 'approved';
					thatcamp_registrations_process_registration( $reg_id, $status );
					break;

				case 'mark_pending' :
					$status = 'pending';
					thatcamp_registrations_process_registration( $reg_id, $status );
					break;

				case 'mark_rejected' :
					$status = 'rejected';
					thatcamp_registrations_process_registration( $reg_id, $status );
					break;

				case 'mark_spam' :
					$status = 'spammed';
					thatcamp_registrations_delete_registration( $reg_id );
					break;
			}
		}

		$redirect_to = remove_query_arg( array( 'id', 'action', '_wpnonce', 'success' ) );
		$redirect_to = add_query_arg( 'success', $status, $redirect_to );
		wp_safe_redirect( $redirect_to );
	}

	function render_csv() {
		$registrations = thatcamp_registrations_get_registrations();

		$ud	      = wp_upload_dir();
		$csv_dir      = trailingslashit( $ud['basedir'] ) . trailingslashit( 'thatcamp-registrations' );
		$csv_basename = 'registrations-' . date( 'Ymd' ) . '.csv';

		if ( ! is_dir( $csv_dir ) ) {
			mkdir( $csv_dir );
		}

		$csv_path = $csv_dir . $csv_basename;
		$fp = fopen( $csv_path, 'w' );

		// Build an array that will represent the proper column headers
		$cols = array(
			array( 'ukey' => 'id', 'title' => 'Registration ID #' ),
			array( 'ukey' => 'date', 'title' => 'Date' ),
			array( 'ukey' => 'user_id', 'title' => 'WP User ID' ),
			array( 'ukey' => 'user_email', 'title' => 'Email' ),
			array( 'ukey' => 'first_name', 'title' => 'First Name' ),
			array( 'ukey' => 'last_name', 'title' => 'Last Name' ),
			array( 'ukey' => 'user_url', 'title' => 'URL' ),
			array( 'ukey' => 'description', 'title' => 'Description' ),
			array( 'ukey' => 'user_title', 'title' => 'Title' ),
			array( 'ukey' => 'user_organization', 'title' => 'Organization' ),
			array( 'ukey' => 'user_twitter', 'title' => 'Twitter' ),
			array( 'ukey' => 'discipline', 'title' => __( 'Discipline', 'thatcamp-registrations' ) ),
			array( 'ukey' => 'technology_skill_level', 'title' => __( 'Technology Skill Level', 'thatcamp-registrations' ) ),
			array( 'ukey' => 'tshirt_size', 'title' => __( 'T-shirt Size', 'thatcamp-registrations' ) ),
			array( 'ukey' => 'application_text', 'title' => 'Application Text' ),
			array( 'ukey' => 'status', 'title' => 'Status' ),
		);

		// Column headers
		$headers = wp_list_pluck( $cols, 'title' );
		fputcsv( $fp, $headers );

		foreach ( $registrations as $reg ) {
			$reg_array = array();
			foreach ( $reg as $rkey => $rvalue ) {
				// applicant_info should be exploded out
				if ( $rkey == 'applicant_info' ) {
					$applicant_info = maybe_unserialize( $rvalue );
					foreach ( $applicant_info as $info_key => $info_value ) {
						$reg_array[ $info_key ] = $info_value;
					}
				} else {
					$reg_array[ $rkey ] = $rvalue;
				}
			}

			// Create a clean array
			$reg_array_clean = array();
			foreach ( $cols as $col ) {
				$user_col_value = isset( $reg_array[ $col['ukey'] ] ) ? $reg_array[ $col['ukey'] ] : '';
				$reg_array_clean[ $col['ukey'] ] = $user_col_value;
			}
			fputcsv( $fp, $reg_array_clean );
		}
		fclose( $fp );

		header("Content-type: application/force-download");
		header('Content-Disposition: inline; filename="' . $csv_path . '"');
		header("Content-Transfer-Encoding: Binary");
		header("Content-length: ".filesize($csv_path));
		header('Content-Type: application/excel');
		header('Content-Disposition: attachment; filename="'.$csv_basename.'"');
		readfile($csv_path);
		exit;
	}
}

endif; // class exists

$thatcamp_registrations_admin = new Thatcamp_Registrations_Admin();
