<?php

function thatcamp_edit_profile_url( $url ) {
	$path = 'profile.php';
	$scheme = 'admin';
	$active = get_active_blog_for_user( get_current_user_id() );
	if ( $active )
		$url = get_admin_url( $active->blog_id, $path, $scheme );
	else
		$url = user_admin_url( $path, $scheme );

	return $url;
}
add_filter( 'edit_profile_url', 'thatcamp_edit_profile_url' );

/**
 * Creates the 'THATCamp Setup' admin page
 */
function thatcamp_add_menu_page() {
	$hook = add_menu_page(
		'THATCamp Setup',
		'THATCamp Setup',
		'manage_options',
		'thatcamp_setup',
		'thatcamp_menu_page'
	);

	add_action( $hook, 'thatcamp_admin_scripts' );
}
add_action( 'admin_menu', 'thatcamp_add_menu_page', 999 );

/**
 * Renders the admin page
 */
function thatcamp_menu_page() {
	$group_id = thatcamp_get_blog_group( get_current_blog_id() );

	$start_date = thatcamp_get_camp_date( $group_id, 'mmddyy' );
	$end_date   = thatcamp_get_camp_date( $group_id, 'mmddyy', 'end' );

	$location = array(
		'country'  => thatcamp_get_location( $group_id, 'country' ),
		'state'    => thatcamp_get_location( $group_id, 'state' ),
		'province' => thatcamp_get_location( $group_id, 'province' ),
		'city'     => thatcamp_get_location( $group_id, 'city' ),
	);

	?>
	<form method="post">

	<div class="wrap">
		<h2>THATCamp Setup</h2>

		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="thatcamp_date">When will your THATCamp be held?</label>
				</th>

				<td>
					<input id="thatcamp_start_date" name="thatcamp_start_date" type="text" value="<?php echo esc_attr( $start_date ) ?>" />
					<p class="description">Start Date</p>
					<input id="thatcamp_end_date" name="thatcamp_end_date" type="text" value="<?php echo esc_attr( $end_date ) ?>" />
					<p class="description">End Date</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="thatcamp_country">Where will your THATCamp be held?</label>
				</th>

				<td>
					<?php thatcamp_country_picker( $location ) ?>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="thatcamp_organizers">Organizers</label>
				</th>

				<td>
					<ul id="organizer-list">
					<?php $users = new WP_User_Query( array( 'blog_id' => get_current_blog_id() ) ) ?>
					<?php foreach ( $users->results as $user ) : ?>
						<?php $is_organizer = get_user_meta( $user->ID, 'wp_' . get_current_blog_id() . '_is_organizer', true ) ?>
						<li><input name="thatcamp_organizers[]" value="<?php echo esc_attr( $user->ID ) ?>" <?php checked( 'yes', $is_organizer ) ?> type="checkbox"> <?php echo bp_core_get_userlink( $user->ID ) ?></li>
					<?php endforeach ?>
					</ul>

					<p class="description">Select all users who should be labeled as 'organizers' of your THATCamp.</p>
				</td>
			</tr>

			<?php if ( is_super_admin() ) : ?>
			<tr>
				<th scope="row">
					Additional signup info (visible to Network Admin only)
				</th>

				<td>
					<p>The following information was provided by the user when the THATCamp was registered</p>

					<ul>
						<li><strong>Chief Organizer's Name</strong> - <?php echo groups_get_groupmeta( $group_id, 'chief_organizers_name' ) ?></li>
						<li><strong>Email Address</strong> - <?php echo groups_get_groupmeta( $group_id, 'email_address' ) ?></li>
						<li><strong>Mailing Address</strong> - <?php echo wpautop( groups_get_groupmeta( $group_id, 'mailing_address' ) ) ?></li>
						<li><strong>Twitter Account</strong> - <?php echo groups_get_groupmeta( $group_id, 'twitter_account' ) ?></li>
						<li><strong>Additional Notes</strong> - <?php echo wpautop( groups_get_groupmeta( $group_id, 'additional_notes' ) ) ?></li>
					</ul>
				</td>
			</tr>
			<?php endif ?>

		</table>

		<br /><br />

		<?php wp_nonce_field( 'thatcamp_setup' ) ?>

		<input type="submit" class="button-primary" name="thatcamp_setup_submit" value="Save Changes" />
	</div>

	</form>
	<?php
}

/**
 * Catches form submits
 */
function thatcamp_admin_catch_submit() {
	global $plugin_page;

	if ( current_user_can( 'manage_options' ) && 'thatcamp_setup' == $plugin_page && ! empty( $_POST['thatcamp_setup_submit'] ) ) {
		check_admin_referer( 'thatcamp_setup' );

		// Fetch the group id, which we'll need throughout
		$group_id = thatcamp_get_blog_group( get_current_blog_id() );

		// Date
		$start_date = isset( $_POST['thatcamp_start_date'] ) ? strtotime( $_POST['thatcamp_start_date'] ) : '';
		groups_update_groupmeta( $group_id, 'thatcamp_start_date', $start_date );
		groups_update_groupmeta( $group_id, 'thatcamp_date', $start_date ); // backpat
		$end_date = isset( $_POST['thatcamp_end_date'] ) ? strtotime( $_POST['thatcamp_end_date'] ) : '';
		groups_update_groupmeta( $group_id, 'thatcamp_end_date', $end_date );

		// Location
		$country  = isset( $_POST['Country'] ) ? $_POST['Country'] : '';
		$state    = isset( $_POST['State'] ) ? $_POST['State'] : '';
		$province = isset( $_POST['Province'] ) ? $_POST['Province'] : '';
		$city     = isset( $_POST['City'] ) ? $_POST['City'] : '';

		foreach ( array( 'country', 'state', 'province', 'city' ) as $ltype ) {
			$lkey = 'thatcamp_' . $ltype;
			groups_update_groupmeta( $group_id, $lkey, $$ltype );
		}

		// Organizers
		$organizers = isset( $_POST['thatcamp_organizers'] ) ? $_POST['thatcamp_organizers'] : '';
		$organizers = wp_parse_id_list( $organizers );

		$org_key = 'wp_' . get_current_blog_id() . '_is_organizer';

		$existing = new WP_User_Query( array( 'meta_key' => $org_key, 'meta_value' => 'yes' ) );
		$existing_ids = array();

		if ( ! empty( $existing->results ) ) {
			$existing_ids = wp_list_pluck( $existing->results, 'ID' );
		}

		// Add passed organizers
		foreach ( $organizers as $org ) {
			update_user_meta( $org, $org_key, 'yes' );
		}

		// Remove others
		foreach ( $existing_ids as $existing_id ) {
			if ( ! in_array( $existing_id, $organizers ) ) {
				delete_user_meta( $existing_id, $org_key );
			}
		}

		wp_redirect( add_query_arg( array(
			'page' => 'thatcamp_setup',
			'settings-updated' => 'true',
		), admin_url( 'admin.php' ) ) );
	}
}
add_action( 'admin_init', 'thatcamp_admin_catch_submit' );

/**
 * Print success notice
 */
function thatcamp_admin_notice() {
	global $plugin_page;

	if ( 'thatcamp_setup' == $plugin_page && ! empty( $_GET['settings-updated'] ) ) {
		echo '<div class="updated settings-error"><p><strong>Settings saved.</strong></p></div>';
	}
}
add_action( 'admin_notices', 'thatcamp_admin_notice' );

/**
 * Enqueues scripts
 */
function thatcamp_admin_scripts() {
	wp_register_script( 'country-autocomplete', WP_CONTENT_URL . '/mu-plugins/js/country-autocomplete.js', array( 'jquery', 'jquery-ui-autocomplete' ) );
	wp_register_script( 'thatcamp-setup', WP_CONTENT_URL . '/mu-plugins/js/thatcamp-setup.js', array( 'jquery', 'jquery-ui-datepicker', 'country-autocomplete' ) );
	wp_enqueue_script( 'thatcamp-setup' );
}

/**
 * Renders the country/state/city picker
 */
function thatcamp_country_picker( $args = array() ) {
	$r = wp_parse_args( $args, array(
		'country' => '',
		'state' => '',
		'province' => '',
		'city' => '',
	) );

	?>
  <style type="text/css" media="screen">
	.ui-autocomplete {
		padding: 0;
		list-style: none;
		background-color: #fff;
		width: 75%;
		border: 1px solid #B0BECA;
		max-height: 350px;
		overflow-y: scroll;
	}
	.ui-autocomplete .ui-menu-item a {
		border-top: 1px solid #B0BECA;
		display: block;
		padding: 4px 6px;
		color: #353D44;
		cursor: pointer;
	}
	.ui-autocomplete .ui-menu-item:first-child a {
		border-top: none;
	}
	.ui-autocomplete .ui-menu-item a.ui-state-hover {
		background-color: #D5E5F4;
		color: #161A1C;
	}
	.ui-helper-hidden-accessible {
		display: none;
	}
	.ui-state-focus {
		border: 1px solid #fbcb09;
		background: #fdf5ce;
	}
	.thatcamp-location-label {
		display: block;
		float: left;
		width: 90px;
	}
  </style>

  <div id="thatcamp-country">
    <span class="thatcamp-location-label">Country:</span>
    <select name="Country" id="country-selector" autofocus="autofocus" autocorrect="off" autocomplete="off">
      <option value="" selected="selected"></option>
      <option <?php selected( $r['country'], 'Afghanistan' ) ?> value="Afghanistan" data-alternative-spellings="AF افغانستان">Afghanistan</option>
      <option <?php selected( $r['country'], 'Åland Islands' ) ?> value="Åland Islands" data-alternative-spellings="AX Aaland Aland" data-relevancy-booster="0.5">Åland Islands</option>
      <option <?php selected( $r['country'], 'Albania' ) ?> value="Albania" data-alternative-spellings="AL">Albania</option>
      <option <?php selected( $r['country'], 'Algeria' ) ?> value="Algeria" data-alternative-spellings="DZ الجزائر">Algeria</option>
      <option <?php selected( $r['country'], 'American Samoa' ) ?> value="American Samoa" data-alternative-spellings="AS" data-relevancy-booster="0.5">American Samoa</option>
      <option <?php selected( $r['country'], 'Andorra' ) ?> value="Andorra" data-alternative-spellings="AD" data-relevancy-booster="0.5">Andorra</option>
      <option <?php selected( $r['country'], 'Angola' ) ?> value="Angola" data-alternative-spellings="AO">Angola</option>
      <option <?php selected( $r['country'], 'Anguilla' ) ?> value="Anguilla" data-alternative-spellings="AI" data-relevancy-booster="0.5">Anguilla</option>
      <option <?php selected( $r['country'], 'Antarctica' ) ?> value="Antarctica" data-alternative-spellings="AQ" data-relevancy-booster="0.5">Antarctica</option>
      <option <?php selected( $r['country'], 'Antigua And Barbuda' ) ?> value="Antigua And Barbuda" data-alternative-spellings="AG" data-relevancy-booster="0.5">Antigua And Barbuda</option>
      <option <?php selected( $r['country'], 'Argentina' ) ?> value="Argentina" data-alternative-spellings="AR">Argentina</option>
      <option <?php selected( $r['country'], 'Armenia' ) ?> value="Armenia" data-alternative-spellings="AM Հայաստան">Armenia</option>
      <option <?php selected( $r['country'], 'Aruba' ) ?> value="Aruba" data-alternative-spellings="AW" data-relevancy-booster="0.5">Aruba</option>
      <option <?php selected( $r['country'], 'Australia' ) ?> value="Australia" data-alternative-spellings="AU" data-relevancy-booster="1.5">Australia</option>
      <option <?php selected( $r['country'], 'Austria' ) ?> value="Austria" data-alternative-spellings="AT Österreich Osterreich Oesterreich ">Austria</option>
      <option <?php selected( $r['country'], 'Azerbaijan' ) ?> value="Azerbaijan" data-alternative-spellings="AZ">Azerbaijan</option>
      <option <?php selected( $r['country'], 'Bahamas' ) ?> value="Bahamas" data-alternative-spellings="BS">Bahamas</option>
      <option <?php selected( $r['country'], 'Bahrain' ) ?> value="Bahrain" data-alternative-spellings="BH البحرين">Bahrain</option>
      <option <?php selected( $r['country'], 'Bangladesh' ) ?> value="Bangladesh" data-alternative-spellings="BD বাংলাদেশ" data-relevancy-booster="2">Bangladesh</option>
      <option <?php selected( $r['country'], 'Barbados' ) ?> value="Barbados" data-alternative-spellings="BB">Barbados</option>
      <option <?php selected( $r['country'], 'Belarus' ) ?> value="Belarus" data-alternative-spellings="BY Беларусь">Belarus</option>
      <option <?php selected( $r['country'], 'Belgium' ) ?> value="Belgium" data-alternative-spellings="BE België Belgie Belgien Belgique" data-relevancy-booster="1.5">Belgium</option>
      <option <?php selected( $r['country'], 'Belize' ) ?> value="Belize" data-alternative-spellings="BZ">Belize</option>
      <option <?php selected( $r['country'], 'Benin' ) ?> value="Benin" data-alternative-spellings="BJ">Benin</option>
      <option <?php selected( $r['country'], 'Bermuda' ) ?> value="Bermuda" data-alternative-spellings="BM" data-relevancy-booster="0.5">Bermuda</option>
      <option <?php selected( $r['country'], 'Bhutan' ) ?> value="Bhutan" data-alternative-spellings="BT भूटान">Bhutan</option>
      <option <?php selected( $r['country'], 'Bolivia' ) ?> value="Bolivia" data-alternative-spellings="BO">Bolivia</option>
      <option <?php selected( $r['country'], 'Bonaire, Sint Eustatius and Saba' ) ?> value="Bonaire, Sint Eustatius and Saba" data-alternative-spellings="BQ">Bonaire, Sint Eustatius and Saba</option>
      <option <?php selected( $r['country'], 'Bosnia and Herzegovina' ) ?> value="Bosnia and Herzegovina" data-alternative-spellings="BA Босна и Херцеговина">Bosnia and Herzegovina</option>
      <option <?php selected( $r['country'], 'Botswana' ) ?> value="Botswana" data-alternative-spellings="BW">Botswana</option>
      <option <?php selected( $r['country'], 'Bouvet Island' ) ?> value="Bouvet Island" data-alternative-spellings="BV">Bouvet Island</option>
      <option <?php selected( $r['country'], 'Brazil' ) ?> value="Brazil" data-alternative-spellings="BR Brasil" data-relevancy-booster="2">Brazil</option>
      <option <?php selected( $r['country'], 'British Indian Ocean Territory' ) ?> value="British Indian Ocean Territory" data-alternative-spellings="IO">British Indian Ocean Territory</option>
      <option <?php selected( $r['country'], 'Brunei Darussalam' ) ?> value="Brunei Darussalam" data-alternative-spellings="BN">Brunei Darussalam</option>
      <option <?php selected( $r['country'], 'Bulgaria' ) ?> value="Bulgaria" data-alternative-spellings="BG България">Bulgaria</option>
      <option <?php selected( $r['country'], 'Burkina Faso' ) ?> value="Burkina Faso" data-alternative-spellings="BF">Burkina Faso</option>
      <option <?php selected( $r['country'], 'Burundi' ) ?> value="Burundi" data-alternative-spellings="BI">Burundi</option>
      <option <?php selected( $r['country'], 'Cambodia' ) ?> value="Cambodia" data-alternative-spellings="KH កម្ពុជា">Cambodia</option>
      <option <?php selected( $r['country'], 'Cameroon' ) ?> value="Cameroon" data-alternative-spellings="CM">Cameroon</option>
      <option <?php selected( $r['country'], 'Canada' ) ?> value="Canada" data-alternative-spellings="CA" data-relevancy-booster="2">Canada</option>
      <option <?php selected( $r['country'], 'Cape Verde' ) ?> value="Cape Verde" data-alternative-spellings="CV Cabo">Cape Verde</option>
      <option <?php selected( $r['country'], 'Cayman Islands' ) ?> value="Cayman Islands" data-alternative-spellings="KY" data-relevancy-booster="0.5">Cayman Islands</option>
      <option <?php selected( $r['country'], 'Central African Republic' ) ?> value="Central African Republic" data-alternative-spellings="CF">Central African Republic</option>
      <option <?php selected( $r['country'], 'Chad' ) ?> value="Chad" data-alternative-spellings="TD تشاد‎ Tchad">Chad</option>
      <option <?php selected( $r['country'], 'Chile' ) ?> value="Chile" data-alternative-spellings="CL">Chile</option>
      <option <?php selected( $r['country'], 'China' ) ?> value="China" data-relevancy-booster="3.5" data-alternative-spellings="CN Zhongguo Zhonghua Peoples Republic 中国/中华">China</option>
      <option <?php selected( $r['country'], 'Christmas Island' ) ?> value="Christmas Island" data-alternative-spellings="CX" data-relevancy-booster="0.5">Christmas Island</option>
      <option <?php selected( $r['country'], 'Cocos (Keeling) Islands' ) ?> value="Cocos (Keeling) Islands" data-alternative-spellings="CC" data-relevancy-booster="0.5">Cocos (Keeling) Islands</option>
      <option <?php selected( $r['country'], 'Colombia' ) ?> value="Colombia" data-alternative-spellings="CO">Colombia</option>
      <option <?php selected( $r['country'], 'Comoros' ) ?> value="Comoros" data-alternative-spellings="KM جزر القمر">Comoros</option>
      <option <?php selected( $r['country'], 'Congo' ) ?> value="Congo" data-alternative-spellings="CG">Congo</option>
      <option <?php selected( $r['country'], 'Congo, the Democratic Republic of the' ) ?> value="Congo, the Democratic Republic of the" data-alternative-spellings="CD Congo-Brazzaville Repubilika ya Kongo">Congo, the Democratic Republic of the</option>
      <option <?php selected( $r['country'], 'Cook Islands' ) ?> value="Cook Islands" data-alternative-spellings="CK" data-relevancy-booster="0.5">Cook Islands</option>
      <option <?php selected( $r['country'], 'Costa Rica' ) ?> value="Costa Rica" data-alternative-spellings="CR">Costa Rica</option>
      <option <?php selected( $r['country'], "Côte d'Ivoire" ) ?> value="Côte d'Ivoire" data-alternative-spellings="CI Cote dIvoire">Côte d'Ivoire</option>
      <option <?php selected( $r['country'], 'Croatia' ) ?> value="Croatia" data-alternative-spellings="HR Hrvatska">Croatia</option>
      <option <?php selected( $r['country'], 'Cuba' ) ?> value="Cuba" data-alternative-spellings="CU">Cuba</option>
      <option <?php selected( $r['country'], 'Curaçao' ) ?> value="Curaçao" data-alternative-spellings="CW Curacao">Curaçao</option>
      <option <?php selected( $r['country'], 'Cyprus' ) ?> value="Cyprus" data-alternative-spellings="CY Κύπρος Kýpros Kıbrıs">Cyprus</option>
      <option <?php selected( $r['country'], 'Czech Republic' ) ?> value="Czech Republic" data-alternative-spellings="CZ Česká Ceska">Czech Republic</option>
      <option <?php selected( $r['country'], 'Denmark' ) ?> value="Denmark" data-alternative-spellings="DK Danmark" data-relevancy-booster="1.5">Denmark</option>
      <option <?php selected( $r['country'], 'Djibouti' ) ?> value="Djibouti" data-alternative-spellings="DJ جيبوتي‎ Jabuuti Gabuuti">Djibouti</option>
      <option <?php selected( $r['country'], 'Dominica' ) ?> value="Dominica" data-alternative-spellings="DM Dominique" data-relevancy-booster="0.5">Dominica</option>
      <option <?php selected( $r['country'], 'Dominican Republic' ) ?> value="Dominican Republic" data-alternative-spellings="DO">Dominican Republic</option>
      <option <?php selected( $r['country'], 'Ecuador' ) ?> value="Ecuador" data-alternative-spellings="EC">Ecuador</option>
      <option <?php selected( $r['country'], 'Egypt' ) ?> value="Egypt" data-alternative-spellings="EG" data-relevancy-booster="1.5">Egypt</option>
      <option <?php selected( $r['country'], 'El Salvador' ) ?> value="El Salvador" data-alternative-spellings="SV">El Salvador</option>
      <option <?php selected( $r['country'], 'Equatorial Guinea' ) ?> value="Equatorial Guinea" data-alternative-spellings="GQ">Equatorial Guinea</option>
      <option <?php selected( $r['country'], 'Eritrea' ) ?> value="Eritrea" data-alternative-spellings="ER إرتريا ኤርትራ">Eritrea</option>
      <option <?php selected( $r['country'], 'Estonia' ) ?> value="Estonia" data-alternative-spellings="EE Eesti">Estonia</option>
      <option <?php selected( $r['country'], 'Ethiopia' ) ?> value="Ethiopia" data-alternative-spellings="ET ኢትዮጵያ">Ethiopia</option>
      <option <?php selected( $r['country'], 'Falkland Islands (Malvinas)' ) ?> value="Falkland Islands (Malvinas)" data-alternative-spellings="FK" data-relevancy-booster="0.5">Falkland Islands (Malvinas)</option>
      <option <?php selected( $r['country'], 'Faroe Islands' ) ?> value="Faroe Islands" data-alternative-spellings="FO Føroyar Færøerne" data-relevancy-booster="0.5">Faroe Islands</option>
      <option <?php selected( $r['country'], 'Fiji' ) ?> value="Fiji" data-alternative-spellings="FJ Viti फ़िजी">Fiji</option>
      <option <?php selected( $r['country'], 'Finland' ) ?> value="Finland" data-alternative-spellings="FI Suomi">Finland</option>
      <option <?php selected( $r['country'], 'France' ) ?> value="France" data-alternative-spellings="FR République française" data-relevancy-booster="2.5">France</option>
      <option <?php selected( $r['country'], 'French Guiana' ) ?> value="French Guiana" data-alternative-spellings="GF">French Guiana</option>
      <option <?php selected( $r['country'], 'French Polynesia' ) ?> value="French Polynesia" data-alternative-spellings="PF Polynésie française">French Polynesia</option>
      <option <?php selected( $r['country'], 'French Southern Territories' ) ?> value="French Southern Territories" data-alternative-spellings="TF">French Southern Territories</option>
      <option <?php selected( $r['country'], 'Gabon' ) ?> value="Gabon" data-alternative-spellings="GA République Gabonaise">Gabon</option>
      <option <?php selected( $r['country'], 'Gambia' ) ?> value="Gambia" data-alternative-spellings="GM">Gambia</option>
      <option <?php selected( $r['country'], 'Georgia' ) ?> value="Georgia" data-alternative-spellings="GE საქართველო">Georgia</option>
      <option <?php selected( $r['country'], 'Germany' ) ?> value="Germany" data-alternative-spellings="DE Bundesrepublik Deutschland" data-relevancy-booster="3">Germany</option>
      <option <?php selected( $r['country'], 'Ghana' ) ?> value="Ghana" data-alternative-spellings="GH">Ghana</option>
      <option <?php selected( $r['country'], 'Gibraltar' ) ?> value="Gibraltar" data-alternative-spellings="GI" data-relevancy-booster="0.5">Gibraltar</option>
      <option <?php selected( $r['country'], 'Greece' ) ?> value="Greece" data-alternative-spellings="GR Ελλάδα" data-relevancy-booster="1.5">Greece</option>
      <option <?php selected( $r['country'], 'Greenland' ) ?> value="Greenland" data-alternative-spellings="GL grønland" data-relevancy-booster="0.5">Greenland</option>
      <option <?php selected( $r['country'], 'Grenada' ) ?> value="Grenada" data-alternative-spellings="GD">Grenada</option>
      <option <?php selected( $r['country'], 'Guadeloupe' ) ?> value="Guadeloupe" data-alternative-spellings="GP">Guadeloupe</option>
      <option <?php selected( $r['country'], 'Guam' ) ?> value="Guam" data-alternative-spellings="GU">Guam</option>
      <option <?php selected( $r['country'], 'Guatemala' ) ?> value="Guatemala" data-alternative-spellings="GT">Guatemala</option>
      <option <?php selected( $r['country'], 'Guernsey' ) ?> value="Guernsey" data-alternative-spellings="GG" data-relevancy-booster="0.5">Guernsey</option>
      <option <?php selected( $r['country'], 'Guinea' ) ?> value="Guinea" data-alternative-spellings="GN">Guinea</option>
      <option <?php selected( $r['country'], 'Guinea-Bissau' ) ?> value="Guinea-Bissau" data-alternative-spellings="GW">Guinea-Bissau</option>
      <option <?php selected( $r['country'], 'Guyana' ) ?> value="Guyana" data-alternative-spellings="GY">Guyana</option>
      <option <?php selected( $r['country'], 'Haiti' ) ?> value="Haiti" data-alternative-spellings="HT">Haiti</option>
      <option <?php selected( $r['country'], 'Heard Island and McDonald Islands' ) ?> value="Heard Island and McDonald Islands" data-alternative-spellings="HM">Heard Island and McDonald Islands</option>
      <option <?php selected( $r['country'], 'Holy See (Vatican City State)' ) ?> value="Holy See (Vatican City State)" data-alternative-spellings="VA" data-relevancy-booster="0.5">Holy See (Vatican City State)</option>
      <option <?php selected( $r['country'], 'Honduras' ) ?> value="Honduras" data-alternative-spellings="HN">Honduras</option>
      <option <?php selected( $r['country'], 'Hong Kong' ) ?> value="Hong Kong" data-alternative-spellings="HK 香港">Hong Kong</option>
      <option <?php selected( $r['country'], 'Hungary' ) ?> value="Hungary" data-alternative-spellings="HU Magyarország">Hungary</option>
      <option <?php selected( $r['country'], 'Iceland' ) ?> value="Iceland" data-alternative-spellings="IS Island">Iceland</option>
      <option <?php selected( $r['country'], 'India' ) ?> value="India" data-alternative-spellings="IN भारत गणराज्य Hindustan" data-relevancy-booster="3">India</option>
      <option <?php selected( $r['country'], 'Indonesia' ) ?> value="Indonesia" data-alternative-spellings="ID" data-relevancy-booster="2">Indonesia</option>
      <option <?php selected( $r['country'], 'Iran, Islamic Republic of' ) ?> value="Iran, Islamic Republic of" data-alternative-spellings="IR ایران">Iran, Islamic Republic of</option>
      <option <?php selected( $r['country'], 'Iraq' ) ?> value="Iraq" data-alternative-spellings="IQ العراق‎">Iraq</option>
      <option <?php selected( $r['country'], 'Ireland' ) ?> value="Ireland" data-alternative-spellings="IE Éire" data-relevancy-booster="1.2">Ireland</option>
      <option <?php selected( $r['country'], 'Isle of Man' ) ?> value="Isle of Man" data-alternative-spellings="IM" data-relevancy-booster="0.5">Isle of Man</option>
      <option <?php selected( $r['country'], 'Israel' ) ?> value="Israel" data-alternative-spellings="IL إسرائيل ישראל">Israel</option>
      <option <?php selected( $r['country'], 'Italy' ) ?> value="Italy" data-alternative-spellings="IT Italia" data-relevancy-booster="2">Italy</option>
      <option <?php selected( $r['country'], 'Jamaica' ) ?> value="Jamaica" data-alternative-spellings="JM">Jamaica</option>
      <option <?php selected( $r['country'], 'Japan' ) ?> value="Japan" data-alternative-spellings="JP Nippon Nihon 日本" data-relevancy-booster="2.5">Japan</option>
      <option <?php selected( $r['country'], 'Jersey' ) ?> value="Jersey" data-alternative-spellings="JE" data-relevancy-booster="0.5">Jersey</option>
      <option <?php selected( $r['country'], 'Jordan' ) ?> value="Jordan" data-alternative-spellings="JO الأردن">Jordan</option>
      <option <?php selected( $r['country'], 'Kazakhstan' ) ?> value="Kazakhstan" data-alternative-spellings="KZ Қазақстан Казахстан">Kazakhstan</option>
      <option <?php selected( $r['country'], 'Kenya' ) ?> value="Kenya" data-alternative-spellings="KE">Kenya</option>
      <option <?php selected( $r['country'], 'Kiribati' ) ?> value="Kiribati" data-alternative-spellings="KI">Kiribati</option>
      <option <?php selected( $r['country'], "Korea, Democratic People's Republic of" ) ?> value="Korea, Democratic People's Republic of" data-alternative-spellings="KP North Korea">Korea, Democratic People's Republic of</option>
      <option <?php selected( $r['country'], 'Korea, Republic of' ) ?> value="Korea, Republic of" data-alternative-spellings="KR South Korea" data-relevancy-booster="1.5">Korea, Republic of</option>
      <option <?php selected( $r['country'], 'Kuwait' ) ?> value="Kuwait" data-alternative-spellings="KW الكويت">Kuwait</option>
      <option <?php selected( $r['country'], 'Kyrgyzstan' ) ?> value="Kyrgyzstan" data-alternative-spellings="KG Кыргызстан">Kyrgyzstan</option>
      <option <?php selected( $r['country'], "Lao People's Democratic Republic" ) ?> value="Lao People's Democratic Republic" data-alternative-spellings="LA">Lao People's Democratic Republic</option>
      <option <?php selected( $r['country'], 'Latvia' ) ?> value="Latvia" data-alternative-spellings="LV Latvija">Latvia</option>
      <option <?php selected( $r['country'], 'Lebanon' ) ?> value="Lebanon" data-alternative-spellings="LB لبنان">Lebanon</option>
      <option <?php selected( $r['country'], 'Lesotho' ) ?> value="Lesotho" data-alternative-spellings="LS">Lesotho</option>
      <option <?php selected( $r['country'], 'Liberia' ) ?> value="Liberia" data-alternative-spellings="LR">Liberia</option>
      <option <?php selected( $r['country'], 'Libyan Arab Jamahiriya' ) ?> value="Libyan Arab Jamahiriya" data-alternative-spellings="LY ليبيا">Libyan Arab Jamahiriya</option>
      <option <?php selected( $r['country'], 'Liechtenstein' ) ?> value="Liechtenstein" data-alternative-spellings="LI">Liechtenstein</option>
      <option <?php selected( $r['country'], 'Lithuania' ) ?> value="Lithuania" data-alternative-spellings="LT Lietuva">Lithuania</option>
      <option <?php selected( $r['country'], 'Luxembourg' ) ?> value="Luxembourg" data-alternative-spellings="LU">Luxembourg</option>
      <option <?php selected( $r['country'], 'Macao' ) ?> value="Macao" data-alternative-spellings="MO">Macao</option>
      <option <?php selected( $r['country'], 'Macedonia, The Former Yugoslav Republic Of' ) ?> value="Macedonia, The Former Yugoslav Republic Of" data-alternative-spellings="MK Македонија">Macedonia, The Former Yugoslav Republic Of</option>
      <option <?php selected( $r['country'], 'Madagascar' ) ?> value="Madagascar" data-alternative-spellings="MG Madagasikara">Madagascar</option>
      <option <?php selected( $r['country'], 'Malawi' ) ?> value="Malawi" data-alternative-spellings="MW">Malawi</option>
      <option <?php selected( $r['country'], 'Malaysia' ) ?> value="Malaysia" data-alternative-spellings="MY">Malaysia</option>
      <option <?php selected( $r['country'], 'Maldives' ) ?> value="Maldives" data-alternative-spellings="MV">Maldives</option>
      <option <?php selected( $r['country'], 'Mali' ) ?> value="Mali" data-alternative-spellings="ML">Mali</option>
      <option <?php selected( $r['country'], 'Malta' ) ?> value="Malta" data-alternative-spellings="MT">Malta</option>
      <option <?php selected( $r['country'], 'Marshall Islands' ) ?> value="Marshall Islands" data-alternative-spellings="MH" data-relevancy-booster="0.5">Marshall Islands</option>
      <option <?php selected( $r['country'], 'Martinique' ) ?> value="Martinique" data-alternative-spellings="MQ">Martinique</option>
      <option <?php selected( $r['country'], 'Mauritania' ) ?> value="Mauritania" data-alternative-spellings="MR الموريتانية">Mauritania</option>
      <option <?php selected( $r['country'], 'Mauritius' ) ?> value="Mauritius" data-alternative-spellings="MU">Mauritius</option>
      <option <?php selected( $r['country'], 'Mayotte' ) ?> value="Mayotte" data-alternative-spellings="YT">Mayotte</option>
      <option <?php selected( $r['country'], 'Mexico' ) ?> value="Mexico" data-alternative-spellings="MX Mexicanos" data-relevancy-booster="1.5">Mexico</option>
      <option <?php selected( $r['country'], 'Micronesia, Federated States of' ) ?> value="Micronesia, Federated States of" data-alternative-spellings="FM">Micronesia, Federated States of</option>
      <option <?php selected( $r['country'], 'Moldova, Republic of' ) ?> value="Moldova, Republic of" data-alternative-spellings="MD">Moldova, Republic of</option>
      <option <?php selected( $r['country'], 'Monaco' ) ?> value="Monaco" data-alternative-spellings="MC">Monaco</option>
      <option <?php selected( $r['country'], 'Mongolia' ) ?> value="Mongolia" data-alternative-spellings="MN Mongγol ulus Монгол улс">Mongolia</option>
      <option <?php selected( $r['country'], 'Montenegro' ) ?> value="Montenegro" data-alternative-spellings="ME">Montenegro</option>
      <option <?php selected( $r['country'], 'Montserrat' ) ?> value="Montserrat" data-alternative-spellings="MS" data-relevancy-booster="0.5">Montserrat</option>
      <option <?php selected( $r['country'], 'Morocco' ) ?> value="Morocco" data-alternative-spellings="MA المغرب">Morocco</option>
      <option <?php selected( $r['country'], 'Mozambique' ) ?> value="Mozambique" data-alternative-spellings="MZ Moçambique">Mozambique</option>
      <option <?php selected( $r['country'], 'Myanmar' ) ?> value="Myanmar" data-alternative-spellings="MM">Myanmar</option>
      <option <?php selected( $r['country'], 'Namibia' ) ?> value="Namibia" data-alternative-spellings="NA Namibië">Namibia</option>
      <option <?php selected( $r['country'], 'Nauru' ) ?> value="Nauru" data-alternative-spellings="NR Naoero" data-relevancy-booster="0.5">Nauru</option>
      <option <?php selected( $r['country'], 'Nepal' ) ?> value="Nepal" data-alternative-spellings="NP नेपाल">Nepal</option>
      <option <?php selected( $r['country'], 'Netherlands' ) ?> value="Netherlands" data-alternative-spellings="NL Holland Nederland" data-relevancy-booster="1.5">Netherlands</option>
      <option <?php selected( $r['country'], 'New Caledonia' ) ?> value="New Caledonia" data-alternative-spellings="NC" data-relevancy-booster="0.5">New Caledonia</option>
      <option <?php selected( $r['country'], 'New Zealand' ) ?> value="New Zealand" data-alternative-spellings="NZ Aotearoa">New Zealand</option>
      <option <?php selected( $r['country'], 'Nicaragua' ) ?> value="Nicaragua" data-alternative-spellings="NI">Nicaragua</option>
      <option <?php selected( $r['country'], 'Niger' ) ?> value="Niger" data-alternative-spellings="NE Nijar">Niger</option>
      <option <?php selected( $r['country'], 'Nigeria' ) ?> value="Nigeria" data-alternative-spellings="NG Nijeriya Naíjíríà" data-relevancy-booster="1.5">Nigeria</option>
      <option <?php selected( $r['country'], 'Niue' ) ?> value="Niue" data-alternative-spellings="NU" data-relevancy-booster="0.5">Niue</option>
      <option <?php selected( $r['country'], 'Norfolk Island' ) ?> value="Norfolk Island" data-alternative-spellings="NF" data-relevancy-booster="0.5">Norfolk Island</option>
      <option <?php selected( $r['country'], 'Northern Mariana Islands' ) ?> value="Northern Mariana Islands" data-alternative-spellings="MP" data-relevancy-booster="0.5">Northern Mariana Islands</option>
      <option <?php selected( $r['country'], 'Norway' ) ?> value="Norway" data-alternative-spellings="NO Norge Noreg" data-relevancy-booster="1.5">Norway</option>
      <option <?php selected( $r['country'], 'Oman' ) ?> value="Oman" data-alternative-spellings="OM عمان">Oman</option>
      <option <?php selected( $r['country'], 'Pakistan' ) ?> value="Pakistan" data-alternative-spellings="PK پاکستان" data-relevancy-booster="2">Pakistan</option>
      <option <?php selected( $r['country'], 'Palau' ) ?> value="Palau" data-alternative-spellings="PW" data-relevancy-booster="0.5">Palau</option>
      <option <?php selected( $r['country'], 'Palestinian Territory, Occupied' ) ?> value="Palestinian Territory, Occupied" data-alternative-spellings="PS فلسطين">Palestinian Territory, Occupied</option>
      <option <?php selected( $r['country'], 'Panama' ) ?> value="Panama" data-alternative-spellings="PA">Panama</option>
      <option <?php selected( $r['country'], 'Papua New Guinea' ) ?> value="Papua New Guinea" data-alternative-spellings="PG">Papua New Guinea</option>
      <option <?php selected( $r['country'], 'Paraguay' ) ?> value="Paraguay" data-alternative-spellings="PY">Paraguay</option>
      <option <?php selected( $r['country'], 'Peru' ) ?> value="Peru" data-alternative-spellings="PE">Peru</option>
      <option <?php selected( $r['country'], 'Philippines' ) ?> value="Philippines" data-alternative-spellings="PH Pilipinas" data-relevancy-booster="1.5">Philippines</option>
      <option <?php selected( $r['country'], 'Pitcairn' ) ?> value="Pitcairn" data-alternative-spellings="PN" data-relevancy-booster="0.5">Pitcairn</option>
      <option <?php selected( $r['country'], 'Poland' ) ?> value="Poland" data-alternative-spellings="PL Polska" data-relevancy-booster="1.25">Poland</option>
      <option <?php selected( $r['country'], 'Portugal' ) ?> value="Portugal" data-alternative-spellings="PT Portuguesa" data-relevancy-booster="1.5">Portugal</option>
      <option <?php selected( $r['country'], 'Puerto Rico' ) ?> value="Puerto Rico" data-alternative-spellings="PR">Puerto Rico</option>
      <option <?php selected( $r['country'], 'Qatar' ) ?> value="Qatar" data-alternative-spellings="QA قطر">Qatar</option>
      <option <?php selected( $r['country'], 'Réunion' ) ?> value="Réunion" data-alternative-spellings="RE Reunion">Réunion</option>
      <option <?php selected( $r['country'], 'Romania' ) ?> value="Romania" data-alternative-spellings="RO Rumania Roumania România">Romania</option>
      <option <?php selected( $r['country'], 'Russian Federation' ) ?> value="Russian Federation" data-alternative-spellings="RU Rossiya Российская Россия" data-relevancy-booster="2.5">Russian Federation</option>
      <option <?php selected( $r['country'], 'Rwanda' ) ?> value="Rwanda" data-alternative-spellings="RW">Rwanda</option>
      <option <?php selected( $r['country'], 'Saint Barthélemy' ) ?> value="Saint Barthélemy" data-alternative-spellings="BL St. Barthelemy">Saint Barthélemy</option>
      <option <?php selected( $r['country'], 'Saint Helena' ) ?> value="Saint Helena" data-alternative-spellings="SH St.">Saint Helena</option>
      <option <?php selected( $r['country'], 'Saint Kitts and Nevis' ) ?> value="Saint Kitts and Nevis" data-alternative-spellings="KN St.">Saint Kitts and Nevis</option>
      <option <?php selected( $r['country'], 'Saint Lucia' ) ?> value="Saint Lucia" data-alternative-spellings="LC St.">Saint Lucia</option>
      <option <?php selected( $r['country'], 'Saint Martin (French Part)' ) ?> value="Saint Martin (French Part)" data-alternative-spellings="MF St.">Saint Martin (French Part)</option>
      <option <?php selected( $r['country'], 'Saint Pierre and Miquelon' ) ?> value="Saint Pierre and Miquelon" data-alternative-spellings="PM St.">Saint Pierre and Miquelon</option>
      <option <?php selected( $r['country'], 'Saint Vincent and the Grenadines' ) ?> value="Saint Vincent and the Grenadines" data-alternative-spellings="VC St.">Saint Vincent and the Grenadines</option>
      <option <?php selected( $r['country'], 'Samoa' ) ?> value="Samoa" data-alternative-spellings="WS">Samoa</option>
      <option <?php selected( $r['country'], 'San Marino' ) ?> value="San Marino" data-alternative-spellings="SM">San Marino</option>
      <option <?php selected( $r['country'], 'Sao Tome and Principe' ) ?> value="Sao Tome and Principe" data-alternative-spellings="ST">Sao Tome and Principe</option>
      <option <?php selected( $r['country'], 'Saudi Arabia' ) ?> value="Saudi Arabia" data-alternative-spellings="SA السعودية">Saudi Arabia</option>
      <option <?php selected( $r['country'], 'Senegal' ) ?> value="Senegal" data-alternative-spellings="SN Sénégal">Senegal</option>
      <option <?php selected( $r['country'], 'Serbia' ) ?> value="Serbia" data-alternative-spellings="RS Србија Srbija">Serbia</option>
      <option <?php selected( $r['country'], 'Seychelles' ) ?> value="Seychelles" data-alternative-spellings="SC" data-relevancy-booster="0.5">Seychelles</option>
      <option <?php selected( $r['country'], 'Sierra Leone' ) ?> value="Sierra Leone" data-alternative-spellings="SL">Sierra Leone</option>
      <option <?php selected( $r['country'], 'Singapore' ) ?> value="Singapore" data-alternative-spellings="SG Singapura  சிங்கப்பூர் குடியரசு 新加坡共和国">Singapore</option>
      <option <?php selected( $r['country'], 'Sint Maarten (Dutch Part)' ) ?> value="Sint Maarten (Dutch Part)" data-alternative-spellings="SX">Sint Maarten (Dutch Part)</option>
      <option <?php selected( $r['country'], 'Slovakia' ) ?> value="Slovakia" data-alternative-spellings="SK Slovenská Slovensko">Slovakia</option>
      <option <?php selected( $r['country'], 'Slovenia' ) ?> value="Slovenia" data-alternative-spellings="SI Slovenija">Slovenia</option>
      <option <?php selected( $r['country'], 'Solomon Islands' ) ?> value="Solomon Islands" data-alternative-spellings="SB">Solomon Islands</option>
      <option <?php selected( $r['country'], 'Somalia' ) ?> value="Somalia" data-alternative-spellings="SO الصومال">Somalia</option>
      <option <?php selected( $r['country'], 'South Africa' ) ?> value="South Africa" data-alternative-spellings="ZA RSA Suid-Afrika">South Africa</option>
      <option <?php selected( $r['country'], 'South Georgia and the South Sandwich Islands' ) ?> value="South Georgia and the South Sandwich Islands" data-alternative-spellings="GS">South Georgia and the South Sandwich Islands</option>
      <option <?php selected( $r['country'], 'South Sudan' ) ?> value="South Sudan" data-alternative-spellings="SS">South Sudan</option>
      <option <?php selected( $r['country'], 'Spain' ) ?> value="Spain" data-alternative-spellings="ES España" data-relevancy-booster="2">Spain</option>
      <option <?php selected( $r['country'], 'Sri Lanka' ) ?> value="Sri Lanka" data-alternative-spellings="LK ශ්‍රී ලංකා இலங்கை Ceylon">Sri Lanka</option>
      <option <?php selected( $r['country'], 'Sudan' ) ?> value="Sudan" data-alternative-spellings="SD السودان">Sudan</option>
      <option <?php selected( $r['country'], 'Suriname' ) ?> value="Suriname" data-alternative-spellings="SR शर्नम् Sarnam Sranangron">Suriname</option>
      <option <?php selected( $r['country'], 'Svalbard and Jan Mayen' ) ?> value="Svalbard and Jan Mayen" data-alternative-spellings="SJ" data-relevancy-booster="0.5">Svalbard and Jan Mayen</option>
      <option <?php selected( $r['country'], 'Swaziland' ) ?> value="Swaziland" data-alternative-spellings="SZ weSwatini Swatini Ngwane">Swaziland</option>
      <option <?php selected( $r['country'], 'Sweden' ) ?> value="Sweden" data-alternative-spellings="SE Sverige" data-relevancy-booster="1.5">Sweden</option>
      <option <?php selected( $r['country'], 'Switzerland' ) ?> value="Switzerland" data-alternative-spellings="CH Swiss Confederation Schweiz Suisse Svizzera Svizra" data-relevancy-booster="1.5">Switzerland</option>
      <option <?php selected( $r['country'], 'Syrian Arab Republic' ) ?> value="Syrian Arab Republic" data-alternative-spellings="SY Syria سورية">Syrian Arab Republic</option>
      <option <?php selected( $r['country'], 'Taiwan, Province of China' ) ?> value="Taiwan, Province of China" data-alternative-spellings="TW 台灣 臺灣">Taiwan, Province of China</option>
      <option <?php selected( $r['country'], 'Tajikistan' ) ?> value="Tajikistan" data-alternative-spellings="TJ Тоҷикистон Toçikiston">Tajikistan</option>
      <option <?php selected( $r['country'], 'Tanzania, United Republic of' ) ?> value="Tanzania, United Republic of" data-alternative-spellings="TZ">Tanzania, United Republic of</option>
      <option <?php selected( $r['country'], 'Thailand' ) ?> value="Thailand" data-alternative-spellings="TH ประเทศไทย Prathet Thai">Thailand</option>
      <option <?php selected( $r['country'], 'Timor-Leste' ) ?> value="Timor-Leste" data-alternative-spellings="TL">Timor-Leste</option>
      <option <?php selected( $r['country'], 'Togo' ) ?> value="Togo" data-alternative-spellings="TG Togolese">Togo</option>
      <option <?php selected( $r['country'], 'Tokelau' ) ?> value="Tokelau" data-alternative-spellings="TK" data-relevancy-booster="0.5">Tokelau</option>
      <option <?php selected( $r['country'], 'Tonga' ) ?> value="Tonga" data-alternative-spellings="TO">Tonga</option>
      <option <?php selected( $r['country'], 'Trinidad and Tobago' ) ?> value="Trinidad and Tobago" data-alternative-spellings="TT">Trinidad and Tobago</option>
      <option <?php selected( $r['country'], 'Tunisia' ) ?> value="Tunisia" data-alternative-spellings="TN تونس">Tunisia</option>
      <option <?php selected( $r['country'], 'Turkey' ) ?> value="Turkey" data-alternative-spellings="TR Türkiye Turkiye">Turkey</option>
      <option <?php selected( $r['country'], 'Turkmenistan' ) ?> value="Turkmenistan" data-alternative-spellings="TM Türkmenistan">Turkmenistan</option>
      <option <?php selected( $r['country'], 'Turks and Caicos Islands' ) ?> value="Turks and Caicos Islands" data-alternative-spellings="TC" data-relevancy-booster="0.5">Turks and Caicos Islands</option>
      <option <?php selected( $r['country'], 'Tuvalu' ) ?> value="Tuvalu" data-alternative-spellings="TV" data-relevancy-booster="0.5">Tuvalu</option>
      <option <?php selected( $r['country'], 'Uganda' ) ?> value="Uganda" data-alternative-spellings="UG">Uganda</option>
      <option <?php selected( $r['country'], 'Ukraine' ) ?> value="Ukraine" data-alternative-spellings="UA Ukrayina Україна">Ukraine</option>
      <option <?php selected( $r['country'], 'United Arab Emirates' ) ?> value="United Arab Emirates" data-alternative-spellings="AE UAE الإمارات">United Arab Emirates</option>
      <option <?php selected( $r['country'], 'United Kingdom' ) ?> value="United Kingdom" data-alternative-spellings="GB Great Britain England UK Wales Scotland Northern Ireland" data-relevancy-booster="2.5">United Kingdom</option>
      <option <?php selected( $r['country'], 'United States' ) ?> value="United States" data-relevancy-booster="3.5" data-alternative-spellings="US USA United States of America">United States</option>
      <option <?php selected( $r['country'], 'United States Minor Outlying Islands' ) ?> value="United States Minor Outlying Islands" data-alternative-spellings="UM">United States Minor Outlying Islands</option>
      <option <?php selected( $r['country'], 'Uruguay' ) ?> value="Uruguay" data-alternative-spellings="UY">Uruguay</option>
      <option <?php selected( $r['country'], 'Uzbekistan' ) ?> value="Uzbekistan" data-alternative-spellings="UZ Ўзбекистон O'zbekstan O‘zbekiston">Uzbekistan</option>
      <option <?php selected( $r['country'], 'Vanuatu' ) ?> value="Vanuatu" data-alternative-spellings="VU">Vanuatu</option>
      <option <?php selected( $r['country'], 'Venezuela' ) ?> value="Venezuela" data-alternative-spellings="VE">Venezuela</option>
      <option <?php selected( $r['country'], 'Vietnam' ) ?> value="Vietnam" data-alternative-spellings="VN Việt Nam" data-relevancy-booster="1.5">Vietnam</option>
      <option <?php selected( $r['country'], 'Virgin Islands, British' ) ?> value="Virgin Islands, British" data-alternative-spellings="VG" data-relevancy-booster="0.5">Virgin Islands, British</option>
      <option <?php selected( $r['country'], 'Virgin Islands, U.S.' ) ?> value="Virgin Islands, U.S." data-alternative-spellings="VI" data-relevancy-booster="0.5">Virgin Islands, U.S.</option>
      <option <?php selected( $r['country'], 'Wallis and Futuna' ) ?> value="Wallis and Futuna" data-alternative-spellings="WF" data-relevancy-booster="0.5">Wallis and Futuna</option>
      <option <?php selected( $r['country'], 'Western Sahara' ) ?> value="Western Sahara" data-alternative-spellings="EH لصحراء الغربية">Western Sahara</option>
      <option <?php selected( $r['country'], 'Yemen' ) ?> value="Yemen" data-alternative-spellings="YE اليمن">Yemen</option>
      <option <?php selected( $r['country'], 'Zambia' ) ?> value="Zambia" data-alternative-spellings="ZM">Zambia</option>
      <option <?php selected( $r['country'], 'Zimbabwe' ) ?> value="Zimbabwe" data-alternative-spellings="ZW">Zimbabwe</option>
    </select>
  </div>

  <div id="thatcamp-state">
    <span class="thatcamp-location-label">State:</span>
    <select name="State" id="state-selector" autofocus="autofocus" autocorrect="off" autocomplete="off">
      <option value="" selected="selected"></option>
      <option <?php selected( $r['state'], 'Alabama' ) ?> value="Alabama" data-alternative-spellings="AL">Alabama</option>
      <option <?php selected( $r['state'], 'Alaska' ) ?> value="Alaska" data-alternative-spellings="AK">Alaska</option>
      <option <?php selected( $r['state'], 'Arizona' ) ?> value="Arizona" data-alternative-spellings="AZ">Arizona</option>
      <option <?php selected( $r['state'], 'Arkansas' ) ?> value="Arkansas" data-alternative-spellings="AR">Arkansas</option>
      <option <?php selected( $r['state'], 'California' ) ?> value="California" data-alternative-spellings="CA">California</option>
      <option <?php selected( $r['state'], 'Colorado' ) ?> value="Colorado" data-alternative-spellings="CO">Colorado</option>
      <option <?php selected( $r['state'], 'Connecticut' ) ?> value="Connecticut" data-alternative-spellings="CT">Connecticut</option>
      <option <?php selected( $r['state'], 'Delaware' ) ?> value="Delaware" data-alternative-spellings="DE">Delaware</option>
      <option <?php selected( $r['state'], 'District of Columbia' ) ?> value="District of Columbia" data-alternative-spellings="DC">District of Columbia</option>
      <option <?php selected( $r['state'], 'Florida' ) ?> value="Florida" data-alternative-spellings="FL">Florida</option>
      <option <?php selected( $r['state'], 'Georgia' ) ?> value="Georgia" data-alternative-spellings="GA">Georgia</option>
      <option <?php selected( $r['state'], 'Guam' ) ?> value="Guam" data-alternative-spellings="GU">Guam</option>
      <option <?php selected( $r['state'], 'Hawaii' ) ?> value="Hawaii" data-alternative-spellings="HI">Hawaii</option>
      <option <?php selected( $r['state'], 'Idaho' ) ?> value="Idaho" data-alternative-spellings="ID">Idaho</option>
      <option <?php selected( $r['state'], 'Illinois' ) ?> value="Illinois" data-alternative-spellings="IL">Illinois</option>
      <option <?php selected( $r['state'], 'Iowa' ) ?> value="Iowa" data-alternative-spellings="IA">Iowa</option>
      <option <?php selected( $r['state'], 'Kansas' ) ?> value="Kansas" data-alternative-spellings="KS">Kansas</option>
      <option <?php selected( $r['state'], 'Kentucky' ) ?> value="Kentucky" data-alternative-spellings="KY">Kentucky</option>
      <option <?php selected( $r['state'], 'Louisiana' ) ?> value="Louisiana" data-alternative-spellings="LA">Louisiana</option>
      <option <?php selected( $r['state'], 'Maine' ) ?> value="Maine" data-alternative-spellings="ME">Maine</option>
      <option <?php selected( $r['state'], 'Maryland' ) ?> value="Maryland" data-alternative-spellings="MD">Maryland</option>
      <option <?php selected( $r['state'], 'Massachusetts' ) ?> value="Massachusetts" data-alternative-spellings="MA">Massachusetts</option>
      <option <?php selected( $r['state'], 'Michigan' ) ?> value="Michigan" data-alternative-spellings="MI">Michigan</option>
      <option <?php selected( $r['state'], 'Minnesota' ) ?> value="Minnesota" data-alternative-spellings="MN">Minnesota</option>
      <option <?php selected( $r['state'], 'Mississippi' ) ?> value="Mississippi" data-alternative-spellings="MS">Mississippi</option>
      <option <?php selected( $r['state'], 'Missouri' ) ?> value="Missouri" data-alternative-spellings="MO">Missouri</option>
      <option <?php selected( $r['state'], 'Montana' ) ?> value="Montana" data-alternative-spellings="MT">Montana</option>
      <option <?php selected( $r['state'], 'Nebraska' ) ?> value="Nebraska" data-alternative-spellings="NE">Nebraska</option>
      <option <?php selected( $r['state'], 'Nevada' ) ?> value="Nevada" data-alternative-spellings="NV">Nevada</option>
      <option <?php selected( $r['state'], 'New Hampshire' ) ?> value="New Hampshire" data-alternative-spellings="NH">New Hampshire</option>
      <option <?php selected( $r['state'], 'New Jersey' ) ?> value="New Jersey" data-alternative-spellings="NJ">New Jersey</option>
      <option <?php selected( $r['state'], 'New Mexico' ) ?> value="New Mexico" data-alternative-spellings="NM">New Mexico</option>
      <option <?php selected( $r['state'], 'New York' ) ?> value="New York" data-alternative-spellings="NY">New York</option>
      <option <?php selected( $r['state'], 'North Carolina' ) ?> value="North Carolina" data-alternative-spellings="NC">North Carolina</option>
      <option <?php selected( $r['state'], 'North Dakta' ) ?> value="North Dakta" data-alternative-spellings="ND">North Dakta</option>
      <option <?php selected( $r['state'], 'Ohio' ) ?> value="Ohio" data-alternative-spellings="OH">Ohio</option>
      <option <?php selected( $r['state'], 'Oklahoma' ) ?> value="Oklahoma" data-alternative-spellings="OK">Oklahoma</option>
      <option <?php selected( $r['state'], 'Oregon' ) ?> value="Oregon" data-alternative-spellings="OR">Oregon</option>
      <option <?php selected( $r['state'], 'Pennsylvania' ) ?> value="Pennsylvania" data-alternative-spellings="PA">Pennsylvania</option>
      <option <?php selected( $r['state'], 'Puerto Rico' ) ?> value="Puerto Rico" data-alternative-spellings="PR">Puerto Rico</option>
      <option <?php selected( $r['state'], 'Rhode Island' ) ?> value="Rhode Island" data-alternative-spellings="RI">Rhode Island</option>
      <option <?php selected( $r['state'], 'South Carolina' ) ?> value="South Carolina" data-alternative-spellings="SC">South Carolina</option>
      <option <?php selected( $r['state'], 'South Dakota' ) ?> value="South Dakota" data-alternative-spellings="SD">South Dakota</option>
      <option <?php selected( $r['state'], 'Tennessee' ) ?> value="Tennessee" data-alternative-spellings="TN">Tennessee</option>
      <option <?php selected( $r['state'], 'Texas' ) ?> value="Texas" data-alternative-spellings="TX">Texas</option>
      <option <?php selected( $r['state'], 'Utah' ) ?> value="Utah" data-alternative-spellings="UT">Utah</option>
      <option <?php selected( $r['state'], 'Vermont' ) ?> value="Vermont" data-alternative-spellings="VT">Vermont</option>
      <option <?php selected( $r['state'], 'Virginia' ) ?> value="Virginia" data-alternative-spellings="VA">Virginia</option>
      <option <?php selected( $r['state'], 'Washington' ) ?> value="Washington" data-alternative-spellings="WA">Washington</option>
      <option <?php selected( $r['state'], 'West Virginia' ) ?> value="West Virginia" data-alternative-spellings="WV">West Virginia</option>
      <option <?php selected( $r['state'], 'Wisconsin' ) ?> value="Wisconsin" data-alternative-spellings="WI">Wisconsin</option>
      <option <?php selected( $r['state'], 'Wyoming' ) ?> value="Wyoming" data-alternative-spellings="WY">Wyoming</option>
    </select>
  </div>

  <div id="thatcamp-province">
    <span class="thatcamp-location-label">Province:</span>
    <select name="Province" id="province-selector" autofocus="autofocus" autocorrect="off" autocomplete="off">
      <option value="" selected="selected"></option>
      <option <?php selected( $r['province'], 'Alberta' ) ?> value="Alberta" data-alternative-spellings="AB">Alberta</option>
      <option <?php selected( $r['province'], 'British Columbia' ) ?> value="British Columbia" data-alternative-spellings="BC">British Columbia</option>
      <option <?php selected( $r['province'], 'Manitoba' ) ?> value="Manitoba" data-alternative-spellings="MB">Manitoba</option>
      <option <?php selected( $r['province'], 'New Brunswick' ) ?> value="New Brunswick" data-alternative-spellings="NB Nouveau Brunswick">New Brunswick</option>
      <option <?php selected( $r['province'], 'Newfoundland and Labrador' ) ?> value="Newfoundland and Labrador" data-alternative-spellings="NL">Newfoundland and Labrador</option>
      <option <?php selected( $r['province'], 'Northwest Territories' ) ?> value="Northwest Territories" data-alternative-spellings="NT">Northwest Territories</option>
      <option <?php selected( $r['province'], 'Nova Scotia' ) ?> value="Nova Scotia" data-alternative-spellings="NS Nouvelle Ecosse">Nova Scotia</option>
      <option <?php selected( $r['province'], 'Nunavut' ) ?> value="Nunavut" data-alternative-spellings="NU">Nunavut</option>
      <option <?php selected( $r['province'], 'Ontario' ) ?> value="Ontario" data-alternative-spellings="ON">Ontario</option>
      <option <?php selected( $r['province'], 'Prince Edward Island' ) ?> value="Prince Edward Island" data-alternative-spellings="PE PEI">Prince Edward Island</option>
      <option <?php selected( $r['province'], 'Québec' ) ?> value="Québec" data-alternative-spellings="Quebec QC">Québec</option>
      <option <?php selected( $r['province'], 'Saskatchewan' ) ?> value="Saskatchewan" data-alternative-spellings="SK">Saskatchewan</option>
      <option <?php selected( $r['province'], 'Yukon' ) ?> value="Yukon" data-alternative-spellings="YT">Yukon</option>
    </select>
  </div>

  <div id="thatcamp-city">
    <span class="thatcamp-location-label">City:</span>
    <input name="City" id="city-input" value="<?php echo esc_attr( $r['city'] ) ?>" />
  </div>

	<?php
}

