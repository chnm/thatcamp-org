<?php

add_action( 'show_user_profile', 'thatcamp_registration_profile_fields_form' );
add_action( 'edit_user_profile', 'thatcamp_registration_profile_fields_form' );
add_action( 'personal_options_update', 'thatcamp_registration_profile_fields_save' );
add_action( 'edit_user_profile_update', 'thatcamp_registration_profile_fields_save' );

function thatcamp_registration_profile_fields_form($user)
{

/*
Note that these profile fields have the same data as that collected in the registration form in thatcamp-registrations-public-registration.php, defined in the applicant_info array in thatcamp-registrations-functions.php, and displayed to site administrators in thatcamp-registrations-admin.php. If new data fields are added in any of those files, they should be added in the others.
*/

?>
<h3><?php _e("THATCamp Profile Information"); ?></h3>
<p>The THATCamp profile information you fill out below will be available on all the sites hosted on http://thatcamp.org for THATCamps that you've been to (not on the sites for THATCamps you haven't been to, and not on THATCamp sites hosted elsewhere, e.g., http://greatlakesthatcamp.org).</p>

<table class="form-table">

<!-- Previous THATCamps -->

  <tr>
    <th><label for="previous_thatcamps"><?php _e("Previous THATCamps"); ?></label></th>
    <td>
    <select name="previous_thatcamps" id="previous_thatcamps" value="<?php echo esc_attr( get_the_author_meta( 'previous_thatcamps', $user->ID ) ); ?>" /><br />
<option>Select an answer</option>
        <?php
        $previous_thatcamps = array('0', '1', 'More than one');

        foreach ($previous_thatcamps as $previous_thatcamp) {
                $value = $previous_thatcamp;
                if (get_the_author_meta( 'previous_thatcamps', $user->ID ) == $value) {
                    $selected = ' selected="selected"';
                } else {
                    $selected = '';
                }
                echo '<option value="'.$value.'"'.$selected.'>'. ucfirst($previous_thatcamp).'</option>';
        }
        ?>
    </select><br />
    <span class="description"><?php _e("How many THATCamps have you been to?"); ?></span>
    </td>
  </tr>

<!-- Title -->
  <tr>
    <th><label for="user_title"><?php _e("Position / Job title"); ?></label></th>
    <td>
    <input type="text" name="user_title" id="user_title" value="<?php echo esc_attr( get_the_author_meta( 'user_title', $user->ID ) ); ?>" /><br />
    <span class="description"><?php _e("Your position or job title, such as Assistant Professor, Instructional Technologist, Archivist, Software Engineer, or graduate student."); ?></span>
    </td>
  </tr>

<!-- Organization -->

  <tr>
    <th><label for="user_organization"><?php _e("Organization"); ?></label></th>
    <td>
    <input type="text" name="user_organization" id="user_organization" value="<?php echo esc_attr( get_the_author_meta( 'user_organization', $user->ID ) ); ?>" /><br />
    <span class="description"><?php _e("Your company or organization, such as George Mason University, New York Public Library, MITH, or Automattic."); ?></span>
    </td>
  </tr>

<!-- Twitter -->

    <tr>
    <th><label for="user_twitter"><?php _e("Twitter"); ?></label></th>
    <td>
    <input type="text" name="user_twitter" id="user_twitter" value="<?php echo esc_attr( get_the_author_meta( 'user_twitter', $user->ID ) ); ?>" /><br />
    <span class="description"><?php _e("Your twitter screenname, so others can follow all those awesome tweets you're tweeting."); ?></span>
    </td>
    </tr>

</table><br />
<?php
}

function thatcamp_registration_profile_fields_save($userId)
{
    if ( current_user_can( 'edit_user', $userId ) ) {
        update_usermeta( $userId, 'previous_thatcamps', $_POST['previous_thatcamps'] );
        update_usermeta( $userId, 'user_title', $_POST['user_title'] );
        update_usermeta( $userId, 'user_organization', $_POST['user_organization'] );
        update_usermeta( $userId, 'user_twitter', $_POST['user_twitter'] );
    }
    return false;
}

