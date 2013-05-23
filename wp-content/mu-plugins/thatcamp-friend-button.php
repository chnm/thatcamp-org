<?php

/**
 * Friend-related template tags to be used across the network
 */

function thatcamp_add_friend_button( $user_id = 0, $type = 'echo' ) {

	if ( is_user_logged_in() ) {
		$button = bp_get_add_friend_button( $user_id );

		// 'Cancel Friendship Request' is super long
		$button = str_replace( 'Cancel Friendship Request', 'Cancel Request', $button );
	} else {
		$button = bp_get_button( array(
			'id' => 'not_friends',
			'component' => 'friends',
			'must_be_logged_in' => false,
			'block_self' => true,
			'wrapper_class'     => 'disabled-button friendship-button not_friends',
			'wrapper_id'        => 'friendship-button-' . $user_id,
			'link_href'         => wp_login_url( wp_nonce_url( bp_loggedin_user_domain() . bp_get_friends_slug() . '/add-friend/' . $user_id . '/', 'friends_add_friend' ) ),
			'link_text'         => __( 'Add Friend', 'buddypress' ),
			'link_title'        => __( 'Add Friend', 'buddypress' ),
			'link_id'           => 'friend-' . $user_id,
			'link_rel'          => 'add',
			'link_class'        => 'friendship-button not_friends add',
		) );

		// Insert the tooltip
		$button = str_replace( '</div>', '<span>Log in to add friend</span></div>', $button );
	}

	if ( 'echo' === $type ) {
		echo $button;
	} else {
		return $button;
	}
}

/**
 * Always add our styles when using the proper theme
 *
 * Done inline to reduce overhead
 */
function thatcamp_add_styles() {
	if ( bp_is_root_blog() ) {
		return;
	}

	?>
<style type="text/css">
div.generic-button {
  margin-bottom: 1rem;
}
div.generic-button a {
  background: #668800 url('<?php echo WP_CONTENT_URL ?>/themes/thatcamp-karma/assets/images/thatcamp-greenbutton.jpg');
  border: 1px solid #668800;
  opacity: 1;
  -moz-border-radius: 3px;
  -webkit-border-radius: 3px;
  border-radius: 3px;
  color: #ffffff;
  cursor: pointer;
  font-size: .7rem;
  outline: none;
  padding: 4px 10px;
  text-align: center;
  text-decoration: none;
  line-height: 14px;
  text-decoration: -1px -1px 0px #668800;
}
div.generic-button a:hover {
  opacity: 0.9;
}
div.generic-button.disabled-button {
  position: relative;
}
div.generic-button.disabled-button a {
  opacity: 0.5;
}
div.generic-button.disabled-button span {
  margin-left: -999em;
  position: absolute;
}
div.generic-button.disabled-button:hover span {
  border-radius: 5px 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px;
  box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.1); -webkit-box-shadow: 5px 5px rgba(0, 0, 0, 0.1); -moz-box-shadow: 5px 5px rgba(0, 0, 0, 0.1);
  position: absolute; left: 1em; top: 2em; z-index: 99;
  margin-left: 0;
  background: #FFFFAA; border: 1px solid #FFAD33;
  padding: 4px 8px;
  white-space: nowrap;
}
</style>
	<?php
}
add_action( 'wp_head', 'thatcamp_add_styles' );
