<?php

if ( !class_exists( 'Thatcamp_Registrations_Admin' ) ) :

class Thatcamp_Registrations_Admin {

	function thatcamp_registrations_admin() {
		add_action( 'admin_init', array ( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	function init() {
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
    			if (isset($_POST['user_account']) && $_POST['user_account'] == 1) {
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

                	<?php echo $registration->additional_information; ?>

                	<h3>Previous THATCamps</h3>
                	<?php echo $applicant->previous_thatcamps; ?>

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

		<h3>T-shirt size</h3>
		<?php echo $applicant->tshirt_size; ?>

		<h3>Dietary preferences</h3>
		<?php echo $applicant->dietary_preferences; ?>


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

            <?php
            $bootcampRegistrations = thatcamp_registrations_get_registrations(array('bootcamp' => '1'));
            $registrations = thatcamp_registrations_get_registrations();
            if ($registrations): ?>

                <p>There are <?php echo count($registrations); ?> total registrations.</p>
                <form action="" method="post">

                <table class="widefat fixed" cellspacing="0">
                <thead>
                <tr class="thead">
                    <th>Applicant Name</th>
                    <th>Applicant Email</th>
                    <th>Status</th>
                    <th>View</th>
                </tr>
                </thead>

                <tfoot>
                <tr class="thead">
                    <th>Applicant Name</th>
                    <th>Applicant Email</th>
                    <th>Status</th>
                    <th>View</th>
                </tr>
                </tfoot>

                <tbody id="users" class="list:user user-list">
                <?php foreach ( $registrations as $registration ): ?>
                    <tr>
                        <?php $applicant = thatcamp_registrations_get_applicant_info($registration); ?>
                        <td><?php echo $applicant->first_name; ?> <?php echo $applicant->last_name; ?></td>
                        <td><?php echo $applicant->user_email; ?></td>
                        <td><?php echo ucwords($registration->status); ?></td>
                        <td><a href="admin.php?page=thatcamp-registrations&amp;id=<?php echo $registration->id; ?>">View Full Registration</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                </table>
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
                'create_user_accounts'          =>  $_POST['create_user_accounts'],
                'require_login'                 =>  $_POST['require_login'],
                'pending_application_email'     =>  $_POST['pending_application_email'],
                'accepted_application_email'    =>  $_POST['accepted_application_email'],
                'rejected_application_email'    =>  $_POST['rejected_application_email']
                );

            update_option('thatcamp_registrations_options', $newOptions);

        }

        $options = get_option('thatcamp_registrations_options');

    ?>
        <div class="wrap">

            <h2><?php echo _e('Settings for THATCamp Registrations', 'thatcamp-registrations'); ?></h2>

            <form action="" method="post">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label for="open_registration"><?php _e( 'Open registration?', 'thatcamp-registrations' ) ?></label></th>
                        <td>
                            <select name="open_registration">
                                <option value="0"><?php _e('No'); ?> </option>
                                <option value="1"<?php if($options['open_registration'] == 1) { echo ' selected="selected"';} ?>><?php _e('Yes'); ?> </option>
                            </select>
                            <p class="description"><?php _e('Before you open registration, create a page called (for instance) &#8220;Register&#8221; and type &#x5b;thatcamp-registration&#x5d; on that page. When this setting is set to  &#8220;Yes&#8221;, the registration form will be generated on that page. ', 'thatcamp-registrations'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="create_user_accounts"><?php _e( 'Create user accounts after registering?', 'thatcamp-registrations' ) ?></label></th>
                        <td>
                            <select name="create_user_accounts">
                                <option value="1"<?php if($options['create_user_accounts'] == 1) { echo ' selected="selected"';} ?>><?php _e('Yes'); ?> </option>
                                <option value="0" <?php if($options['create_user_accounts'] == 0) { echo ' selected="selected"';} ?>><?php _e('No'); ?> </option>
                            </select>
                            <p class="description"><?php _e('If &#8220;Yes&#8221;, approving a registration will add the registrant to this site as a user with Author privileges. If &#8220;No&#8221, approving a registration will not add the registrant as a user to this site.', 'thatcamp-registrations'); ?></p>
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
                            <textarea name="pending_application_email" id="pending_application_email" rows="5" cols="50"><?php if( !empty($options['pending_application_email']) ) echo $options['pending_application_email']; ?></textarea>
                            <p class="description"><?php _e('This e-mail will be sent by the system from an automated account; you may therefore wish to include your own name and e-mail address in the message itself so that users may contact you. The e-mail will be composed in HTML format, so links and e-mail addresses will automatically be hyperlinked, and no additional HTML codes are necessary. If no text is entered, no e-mail will be sent.'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="accepted_application_email"><?php _e('Accepted registration email', 'thatcamp-registrations'); ?></label></th>
                        <td>
                            <textarea name="accepted_application_email" id="accepted_application_email" rows="5" cols="50"><?php if( !empty($options['accepted_application_email']) ) echo $options['accepted_application_email']; ?></textarea>
                             <p class="description"><?php _e('This e-mail will be sent by the system from an automated account; you may therefore wish to include your own name and e-mail address in the message itself so that users may contact you. The e-mail will be composed in HTML format, so links and e-mail addresses will automatically be hyperlinked, and no additional HTML codes are necessary. If no text is entered, no e-mail will be sent.'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="rejected_application_email"><?php _e('Rejected registration email', 'thatcamp-registrations'); ?></label></th>
                        <td>
                            <textarea name="rejected_application_email" id="rejected_application_email" rows="5" cols="50"><?php if( !empty($options['rejected_application_email']) ) echo $options['rejected_application_email']; ?></textarea>
                            <p class="description"><?php _e('This e-mail will be sent by the system from an automated account; you may therefore wish to include your own name and e-mail address in the message itself so that users may contact you. The e-mail will be composed in HTML format, so links and e-mail addresses will automatically be hyperlinked, and no additional HTML codes are necessary. If no text is entered, no e-mail will be sent.'); ?></p>

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
}

endif; // class exists

$thatcamp_registrations_admin = new Thatcamp_Registrations_Admin();
