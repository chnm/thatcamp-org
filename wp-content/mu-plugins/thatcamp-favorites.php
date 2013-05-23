<?php

/**
 * Extends BP's favorites functionality to work for THATCamp
 */

class THATCamp_Favorites {
	public function __construct() {
		add_filter( 'the_content', array( $this, 'add_button_to_the_content' ) );

		// Add per-activity "favoriter" data
		add_action( 'bp_activity_add_user_favorite', array( $this, 'add_activity_favoriter' ), 10, 2 );
		add_action( 'bp_activity_remove_user_favorite', array( $this, 'remove_activity_favoriter' ), 10, 2 );

		add_action( 'bp_actions', array( $this, 'catch_mark_favorite' ), 5 );
		add_action( 'bp_actions', array( $this, 'catch_unmark_favorite' ), 5 );

		// admin panels
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'network_admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * Wrapper that grabs the_content and throws a button on the end
	 */
	public function add_button_to_the_content( $content ) {
		if ( is_user_logged_in() && ! is_admin() ) {
			$content .= $this->get_favorite_button();
		}
		return $content;
	}

	/**
	 * Creates a favorite button
	 */
	public function get_favorite_button( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'user_id' => bp_loggedin_user_id(),
			'post_id' => get_the_ID(),
			'blog_id' => get_current_blog_id(),
		) );

		$activity_id = $this->get_activity_id_for_post( $r['post_id'], $r['blog_id'] );
		$afav_count = intval( bp_activity_get_meta( $activity_id, 'thatcamp_favoriters_count' ) );

		if ( 0 === $afav_count ) {
			$afav_gloss = 'No users have favorited this post yet.';
		} else if ( 1 === $afav_count ) {
			$afav_gloss = 'One user has favorited this post.';
		} else {
			$afav_gloss = sprintf( '%s users have favorited this post.', number_format_i18n( $afav_count ) );
		}

		if ( ! $this->user_has_favorited_activity( $r['user_id'], $activity_id ) ) {
			$url = add_query_arg( 'redirect_to', wp_guess_url(), wp_nonce_url( bp_get_root_domain() . '/' . bp_get_activity_root_slug() . '/favorite/' . $activity_id . '/', 'mark_favorite' ) );
			$class = 'button fav bp-secondary-action';
			$title = 'Mark as Favorite';
			$text = sprintf( 'Favorite (%s)', number_format_i18n( $afav_count ) );
		} else {
			$url = add_query_arg( 'redirect_to', wp_guess_url(), wp_nonce_url( bp_get_root_domain() . '/' . bp_get_activity_root_slug() . '/unfavorite/' . $activity_id . '/', 'unmark_favorite' ) );
			$class = 'button unfav bp-secondary-action';
			$title = 'Remove Favorite';
			$text = sprintf( 'Remove Favorite (%s)', number_format_i18n( $afav_count ) );
		}

		return sprintf(
			'<a href="%s" class="%s" title="%s">%s</a> %s',
			$url,
			$class,
			$title,
			$text,
			$afav_gloss
		);
	}

	/**
	 * Given a post_id and a blog_id, return the corresponding activity_id
	 *
	 * Returns false if none is found
	 */
	public function get_activity_id_for_post( $post_id, $blog_id ) {
		// 'component' is not provided, to support groupblog futzing
		$activities = bp_activity_get( array(
			'filter' => array(
				'primary_id' => $blog_id,
				'secondary_id' => $post_id,
				'action' => 'new_blog_post',
			),
			'per_page' => 1,
		) );

		$activity_id = false;

		if ( ! empty( $activities ) ) {
			$activity_id = $activities['activities'][0]->id;
		}

		return $activity_id;
	}

	/**
	 * is this activity item in the user's list of favorites?
	 *
	 * should be in BP
	 */
	public function user_has_favorited_activity( $user_id, $activity_id ) {
		if ( ! $activity_id || ! $user_id ) {
			return false;
		}

		$user_favs = (array) bp_activity_get_user_favorites( $user_id );
		return in_array( $activity_id, $user_favs );
	}

	/**
	 * Add the user id to the list of users who have favorited this activity
	 *
	 * Also bump the count
	 */
	public function add_activity_favoriter( $activity_id, $user_id ) {
		$activity_favoriters = bp_activity_get_meta( $activity_id, 'thatcamp_favoriters' );
		if ( ! $activity_favoriters ) {
			$activity_favoriters = array();
		}
		$activity_favoriters[] = $user_id;

		$activity_favoriters = array_unique( $activity_favoriters );

		bp_activity_update_meta( $activity_id, 'thatcamp_favoriters', $activity_favoriters );
		bp_activity_update_meta( $activity_id, 'thatcamp_favoriters_count', count( $activity_favoriters ) );
	}

	/**
	 * Remove the use from the list of users who have favorited the activity
	 *
	 * Decrement the count
	 */
	public function remove_activity_favoriter( $activity_id, $user_id ) {
		$activity_favoriters = bp_activity_get_meta( $activity_id, 'thatcamp_favoriters' );
		if ( ! empty( $activity_favoriters ) ) {
			$afs = array_flip( $activity_favoriters );
			unset( $afs[ $user_id ] );
			$activity_favoriters = array_values( array_unique( array_flip( $afs ) ) );
		}

		bp_activity_update_meta( $activity_id, 'thatcamp_favoriters', $activity_favoriters );
		bp_activity_update_meta( $activity_id, 'thatcamp_favoriters_count', count( $activity_favoriters ) );
	}

	/**
	 * Screen function for grabbing a favorite request
	 *
	 * If redirect_to is found, it comes from a single blog post page, so
	 * we handle it. Otherwise fall through to the BP native function
	 */
	public function catch_mark_favorite() {

		// Let BP handle regular favoriting
		if ( ! isset( $_GET['redirect_to'] ) ) {
			return;
		}

		if ( !is_user_logged_in() || !bp_is_activity_component() || !bp_is_current_action( 'favorite' ) )
			return false;

		remove_action( 'bp_actions', 'bp_activity_action_mark_favorite' );

		// Check the nonce
		check_admin_referer( 'mark_favorite' );

		bp_activity_add_user_favorite( bp_action_variable( 0 ) );

		wp_redirect( stripslashes( urldecode( $_GET['redirect_to'] ) ) );
	}

	/**
	 * Screen function for grabbing an unfavorite request
	 *
	 * If redirect_to is found, it comes from a single blog post page, so
	 * we handle it. Otherwise fall through to the BP native function
	 */
	public function catch_unmark_favorite() {

		// Let BP handle regular favoriting
		if ( ! isset( $_GET['redirect_to'] ) ) {
			return;
		}

		if ( !is_user_logged_in() || !bp_is_activity_component() || !bp_is_current_action( 'unfavorite' ) )
			return false;

		remove_action( 'bp_actions', 'bp_activity_action_remove_favorite' );

		// Check the nonce
		check_admin_referer( 'unmark_favorite' );

		bp_activity_remove_user_favorite( bp_action_variable( 0 ) );

		wp_redirect( stripslashes( urldecode( $_GET['redirect_to'] ) ) );
	}

	public function admin_menu() {
		add_menu_page(
			'Favorites',
			'Favorites',
			'manage_options',
			'thatcamp-favorites',
			array( $this, 'admin_menu_cb' )
		);
	}

	public function resort_activities( $a, $b ) {
		$aloc = $this->aid_order[ $a->id ];
		$bloc = $this->aid_order[ $b->id ];

		if ( $aloc === $bloc ) {
			return 0;
		}

		return $aloc > $bloc ? 1 : -1;
	}

	public function admin_menu_cb() {
		$aids = $this->get_most_favorited_activities( is_network_admin() );

		$activities = bp_activity_get( array( 'in' => wp_list_pluck( $aids, 'activity_id' ) ) );

		// They get resorted so we have to muck with them, ugh
		$this->aid_order = array_flip( wp_list_pluck( $aids, 'activity_id' ) );
		uasort( $activities['activities'], array( $this, 'resort_activities' ) );

		$fav_counts = array();
		foreach ( $aids as $aid ) {
			$fav_counts[ $aid['activity_id'] ] = $aid['favorite_count'];
		}

		// add fav counts to the array
		foreach ( $activities['activities'] as &$a ) {
			$a->favorite_count = $fav_counts[ $a->id ];
		}

		?>
		<div class="wrap">
			<h2>Favorites</h2>

			<table class="widefat">

				<tr>
					<th>Post</th>
					<th>Author</th>

					<?php if ( is_network_admin() ) : ?>
						<th>Site</th>
					<?php endif ?>

					<th>Count</th>
				</tr>
			<?php foreach ( $activities['activities'] as $a ) : ?>
				<tr>
					<td>
						<?php $post = preg_replace( '/.*new post, (.*?<\/a>).*/', '\1', $a->action ) ?>
						<?php echo $post ?>
					</td>

					<td>
						<?php $author = preg_replace( '/(.*?<\/a>).*/', '\1', $a->action ) ?>
						<?php echo $author ?>
					</td>

					<?php if ( is_network_admin() ) : ?>
					<td>
						<?php $site = preg_replace( '/.*the site (.*)$/', '\1', $a->action ) ?>
						<?php echo $site ?>
					</td>
					<?php endif ?>

					<td>
						<?php echo number_format_i18n( $a->favorite_count ) ?>
					</td>
				</tr>
			<?php endforeach ?>
			</table>
		</div>
		<?php
	}

	public function get_most_favorited_activities( $is_network_admin = false ) {
		global $wpdb, $bp;

		$blog_filter = '';
		if ( ! $is_network_admin ) {
			// direct query to avoid overhead
			$this_blog_aids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$bp->activity->table_name} WHERE type = 'new_blog_post' AND item_id = %d", get_current_blog_id() ) );
			$blog_filter = 'activity_id IN (' . implode( ',', $this_blog_aids ) . ') AND ';
		}

		$favs = $wpdb->get_results( "SELECT activity_id, meta_value as favorite_count FROM {$bp->activity->table_name_meta} WHERE {$blog_filter} meta_key = 'thatcamp_favoriters_count' ORDER BY CONVERT(meta_value, SIGNED) DESC LIMIT 100", ARRAY_A );

		// add those that have no results
		$count = 100 - count( $favs );
		$blog_filter = str_replace( 'activity_id', 'id', $blog_filter );
		$nofavs = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$bp->activity->table_name} WHERE {$blog_filter} type = 'new_blog_post' AND id NOT IN (" . implode( ',', wp_list_pluck( $favs, 'activity_id' ) ) . ") ORDER BY id DESC LIMIT %d ", $count ) );

		foreach ( $nofavs as $nofav ) {
			$favs[] = array(
				'activity_id' => $nofav,
				'favorite_count' => '0',
			);
		}

		return $favs;
	}

}

function tcf_load() {
	buddypress()->thatcamp_favorites = new THATCamp_Favorites();
}
add_action( 'bp_init', 'tcf_load' );
