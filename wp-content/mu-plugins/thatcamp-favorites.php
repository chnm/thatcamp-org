<?php

/**
 * Extends BP's favorites functionality to work for THATCamp
 */

class THATCamp_Favorites {
	public function __construct() {
		add_filter( 'get_the_excerpt', array( $this, 'unhook_duplicate_excerpt_button' ), 1 );
		add_filter( 'the_content', array( $this, 'add_button_to_the_content' ), 9999999 );
		add_filter( 'the_excerpt', array( $this, 'add_button_to_the_content' ), 9999999 );

		// Add per-activity "favoriter" data
		add_action( 'bp_activity_add_user_favorite', array( $this, 'add_activity_favoriter' ), 10, 2 );
		add_action( 'bp_activity_remove_user_favorite', array( $this, 'remove_activity_favoriter' ), 10, 2 );

		add_action( 'bp_actions', array( $this, 'catch_mark_favorite' ), 5 );
		add_action( 'bp_actions', array( $this, 'catch_unmark_favorite' ), 5 );

		// admin panels
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'network_admin_menu', array( $this, 'admin_menu' ) );

		// css
		add_action( 'wp_print_scripts', array( $this, 'print_styles' ) );
	}

	/**
	 * When content is displayed via excerpt, don't let the button show twice
	 *
	 * the_excerpt() evenually runs its stuff through the 'the_content'
	 * filters (see wp_trim_excerpt()). We don't want double buttons, so
	 * we take this opportunity - at get_the_excerpt, when we know an
	 * excerpt is in process - to unhook the the_content button adder.
	 */
	public function unhook_duplicate_excerpt_button( $excerpt ) {
		remove_filter( 'the_content', array( $this, 'add_button_to_the_content' ), 9999999 );
		return $excerpt;
	}

	/**
	 * Wrapper that grabs the_content and throws a button on the end
	 */
	public function add_button_to_the_content( $content ) {
		if ( is_user_logged_in() && ! is_admin() ) {
			$content .= $this->get_favorite_button();
		}
		add_filter( 'the_content', array( $this, 'add_button_to_the_content' ), 9999999 );
		return $content;
	}
	
	
	public function get_fed_favorite_button($id = 0){
		if ($id == 0){
			$id = get_the_ID();
		}
		
		$blog_id = get_post_meta($id, 'blogid', true);
		if (empty($blog_id)){
			$button = $this->get_favorite_button(); 	
		} else {
		$url = get_post_meta($id, 'permalink', true); 
			switch_to_blog($blog_id);
			$blog_post_id = url_to_postid( $url );
			$args = array(
				'blog_id' => $blog_id,
				'post_id' => $blog_post_id 
			);
			
			$button = $this->get_favorite_button($args); 
			restore_current_blog();
		}
		
		return $button;
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

                // If there's no activity for this blog post, don't create a
                // button. should only happen for Private blogs
                if ( ! $activity_id ) {
                        return '';
                }

		$afav_count = intval( bp_activity_get_meta( $activity_id, 'thatcamp_favoriters_count' ) );

		if ( 0 === $afav_count ) {
			$afav_gloss = 'No users have favorited this post yet.';
		} else if ( 1 === $afav_count ) {
			$afav_gloss = 'One user has favorited this post.';
		} else {
			$afav_gloss = sprintf( '%s users have favorited this post.', number_format_i18n( $afav_count ) );
		}

                // Can't use the regular functions for building URL because it
                // breaks parse_url() for some reason
		if ( ! $this->user_has_favorited_activity( $r['user_id'], $activity_id ) ) {
                        $url_base = bp_get_root_domain() . '/' . bp_get_activity_root_slug() . '/favorite/' . $activity_id . '/';
                        $url_base = add_query_arg( 'redirect_to', urlencode( wp_guess_url() ), $url_base );
                        $nonce = wp_create_nonce( 'mark_favorite' );
                        $url = add_query_arg( '_wpnonce', $nonce, $url_base );
			$class = 'button fav bp-secondary-action';
			$title = 'Mark as Favorite';
			$text = sprintf( 'Favorite <span class="fav-count">%s</span>', number_format_i18n( $afav_count ) );
		} else {
                        $url_base = bp_get_root_domain() . '/' . bp_get_activity_root_slug() . '/unfavorite/' . $activity_id . '/';
                        $url_base = add_query_arg( 'redirect_to', urlencode( wp_guess_url() ), $url_base );
                        $nonce = wp_create_nonce( 'unmark_favorite' );
                        $url = add_query_arg( '_wpnonce', $nonce, $url_base );
			$class = 'button unfav bp-secondary-action';
			$title = 'Remove Favorite';
			$text = sprintf( 'Remove Favorite <span class="fav-count">%s</span>', number_format_i18n( $afav_count ) );
		}

		return sprintf(
			'<div class="thatcamp-favorite"><a href="%s" class="%s" title="%s">%s</a> %s</div>',
			$url,
			$class,
			$title,
			$text,
			$afav_gloss
		);
	}
	
	
	public function get_new_favorite_count_by_user( $user_id ) {
	
		$args = array(
			'per_page' => 10000,
			'show_hidden' => true,
			'user_id' => $user_id,
			'action' => 'thatcamp_favoriters_count'
		);
		
		if ( bp_has_activities( $args ) ) {
			global $activities_template;
			$count = $activities_template->total_activity_count;
		} else {
			$count = 0;
		}

		return $count;
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

	/**
	 * Printing styles directly in the head because I'm a rebel
	 */
	public function print_styles() {
		?>
<style type="text/css">
.thatcamp-favorite {
  padding: 3px 0;
  font-size: .8em;
  margin-bottom: .5em;
  font-style: italic;
  color: #999;
}
.thatcamp-favorite .button {
  background: #668800 url('http://epicplay2013.thatcamp.org/wp-content/themes/thatcamp-karma/assets/images/thatcamp-greenbutton.jpg');
  font-style: normal;
  border: 1px solid #668800;
  opacity: 1;
  -moz-border-radius: 3px;
  -webkit-border-radius: 3px;
  border-radius: 3px;
  color: #ffffff;
  cursor: pointer;
  margin-right: 10px;
  outline: none;
  padding: 4px 10px;
  text-align: center;
  text-decoration: none;
  line-height: 14px;
  text-decoration: -1px -1px 0px #668800;
}
.thatcamp-favorite .button:hover {
  opacity: 0.9;
}
.thatcamp-favorite span.fav-count {
  background: #666;
  width: 10px;
  height: 1.2em;
  padding: 2px;
  border-radius: 6px;
}
</style>
		<?php
	}

}

function tcf_load() {
	buddypress()->thatcamp_favorites = new THATCamp_Favorites();
}
add_action( 'bp_init', 'tcf_load' );
