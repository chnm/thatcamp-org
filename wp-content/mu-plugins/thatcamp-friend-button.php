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
			'link_href'         => wp_login_url( '/?add-friend=' . $user_id . '&aredirect=' . urlencode( wp_guess_url() ) ),
			'link_text'         => __( 'Add Friend', 'buddypress' ),
			'link_title'        => __( 'Add Friend', 'buddypress' ),
			'link_id'           => 'friend-' . $user_id,
			'link_rel'          => 'add',
			'link_class'        => 'friendship-button not_friends add',
		) );


		// Insert the tooltip
		$button = str_replace( '</div>', '<span>Log in to befriend author</span></div>', $button );
	}

	// Replace Title

	if ( is_home() || is_single() ) {
		$button = str_replace( 'Add Friend', 'Befriend Author', $button );
	} else {
		$button = str_replace( 'Add Friend', 'Befriend', $button );
	}

	if ( 'echo' === $type ) {
		echo $button;
	} else {
		return $button;
	}
}

function thatcamp_catch_login_redirect_friend_requests() {
	if ( is_user_logged_in() && ! empty( $_GET['add-friend'] ) && ! empty( $_GET['aredirect'] ) ) {
                global $my_transposh_plugin;
                $redirect_to = bp_loggedin_user_domain() . bp_get_friends_slug() . '/add-friend/' . intval( $_GET['add-friend'] ) . '/';
                $redirect_to = add_query_arg( '_wpnonce', wp_create_nonce( 'friends_add_friend' ), $redirect_to );
//		$redirect_to = wp_nonce_url( bp_loggedin_user_domain() . bp_get_friends_slug() . '/add-friend/' . intval( $_GET['add-friend'] ) . '/', 'friends_add_friend' );
		$redirect_to = add_query_arg( 'bredirect', $_GET['aredirect'], $redirect_to );

		wp_redirect( $redirect_to );
	}
}
add_action( 'bp_actions', 'thatcamp_catch_login_redirect_friend_requests', 1 );

function thatcamp_kill_transposh_hackery( $uri ) {
        global $wp_filter;

        if ( ! empty( $_GET['bredirect'] ) ) {
                foreach ( (array) array_keys( $wp_filter['bp_uri'][10] ) as $filter ) {
                        if ( false !== strpos( $filter, 'bp_uri_filter' ) ) {
                                unset( $wp_filter['bp_uri'][10][ $filter ] );
                        }
                }
        }

        return $uri;
}
add_filter( 'bp_uri', 'thatcamp_kill_transposh_hackery', 1 );

/**
 * Special case for the redirect chain after logging in
 */
function thatcamp_hijack_post_add_friend_redirect( $redirect ) {
	if ( ! empty( $_GET['bredirect'] ) ) {
		$redirect = urldecode( $_GET['bredirect'] );
	}
	return $redirect;
}
add_action( 'wp_redirect', 'thatcamp_hijack_post_add_friend_redirect' );

function thatcamp_whitelist_subdomain_redirects( $hosts ) {
	$p = parse_url( wp_get_referer() );
	if ( ! empty( $p['host'] ) && 'thatcamp.org' === substr( $p['host'], -12 ) ) {
		$hosts[] = $p['host'];
	}
	return $hosts;
}
add_filter( 'allowed_redirect_hosts', 'thatcamp_whitelist_subdomain_redirects' );

/**
 * Always add our styles when using the proper theme
 *
 * Done inline to reduce overhead
 */
function thatcamp_add_styles() {
	//if ( bp_is_root_blog() ) {
	//	return;
	//}

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
