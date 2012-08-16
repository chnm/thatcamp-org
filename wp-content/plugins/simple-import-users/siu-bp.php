<?php

function ddiu_bp_profile_link( $message, $user_id ) {
	
	$profile_edit_url = bp_core_get_user_domain( $user_id ) . 'profile/edit/';
	
	return str_replace( '[EDITPROFILEURL]', $profile_edit_url, $message );	
	
}
add_filter( 'ddiu_bp_filter', 'ddiu_bp_profile_link', 10, 2 );

function ddiu_bp_email_content( $message ) {
	return $message . "Customize your profile at [EDITPROFILEURL].

";	
}
add_filter( 'ddiu_bp_filter_email_content', 'ddiu_bp_email_content' );

?>