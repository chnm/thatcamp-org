<?php

function bp_import_blog_activity_admin_add() {
	add_submenu_page(
		'settings.php',
		__( 'Import Blog Activity', 'bp-import-blog-activity' ),
		__( 'Import Blog Activity', 'bp-import-blog-activity' ),
		'manage_options',
		__FILE__,
		'bp_import_blog_activity_admin_screen'
	);
}
add_action( 'network_admin_menu', 'bp_import_blog_activity_admin_add', 70 );

function bp_import_blog_activity_admin_screen() {
	global $wpdb;
	?>

          <div class="wrap">
            <h2><?php _e( 'Import Blog Activity', 'bp-import-blog-activity' ) ?></h2>
            <form name="bp-iba-options-form" method="post" action="">
                <div class="bp-iba-options">
                	<label for="bp-iba-submit"><?php _e('Press the button below to import blog entries and comments left before BuddyPress was installed into the BuddyPress activity streams. Warning: If you have a lot of blogs and comments on your system, this could take a while.', 'bp-import-blog-activity') ?>
                    </label>

               <p class="submit">
                    <input type="submit" name="Submit" value="<?php _e( 'Import' ) ?> &raquo;" />
                    <input type="hidden" name="bp_iba_submit" value="1" />
                </p>
            </form>
        </div>

<?php

	if( ! empty( $_POST['bp_iba_submit'] ) && $_POST[ 'bp_iba_submit' ] == '1' ) {

		$query = "SELECT * FROM {$wpdb->blogs} WHERE site_id = '{$wpdb->siteid}' ";
		$blog_list = $wpdb->get_results( $query, ARRAY_A );

		foreach( $blog_list as $blog ) {
			if ( $blog['blog_id'] < 2 ) continue;
			if ( $blog['blog_id'] > 10 ) die();

			switch_to_blog( $blog['blog_id'] );
			print "<pre>";


				query_posts('order=ASC&posts_per_page=-1');

				echo "Blog name: <strong>" . get_bloginfo('name') . "</strong>";
				echo "<br />";


				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();

						//echo "The post is: "; the_title(); echo "<br />";

						$filter = array( 'object' => 'blogs', 'primary_id' => $blog['blog_id'], 'secondary_id' => get_the_ID(), 'action' => 'new_blog_post' );
						$activities = bp_activity_get( array( 'filter' => $filter ) );

						if ( empty($activities->activities) ) {
							global $post;

							if ( (int)get_blog_option( $blog['blog_id'], 'blog_public' ) ) {
								$post_permalink = get_permalink();

								$activity_action = sprintf( __( '%s wrote a new blog post: %s', 'buddypress' ), bp_core_get_userlink( (int)$post->post_author ), '<a href="' . $post_permalink . '">' . $post->post_title . '</a>' );
								$activity_content = $post->post_content;
								$post_id = get_the_ID();

							bp_blogs_record_activity( array(
								'user_id' => (int)$post->post_author,
								'action' => apply_filters( 'bp_blogs_activity_new_post_action', $activity_action, $post, $post_permalink ),
								'content' => apply_filters( 'bp_blogs_activity_new_post_content', $activity_content, $post, $post_permalink ),
								'primary_link' => apply_filters( 'bp_blogs_activity_new_post_primary_link', $post_permalink, $post_id ),
								'type' => 'new_blog_post',
								'item_id' => $blog['blog_id'],
								'secondary_item_id' => $post_id,
								'recorded_time' => $post->post_date_gmt
							));
							}
							echo "Importing: \"" . $activity_action . "\"<br />";
						}

						$comments = get_comments("post_id=" . $post_id );

						foreach( $comments as $recorded_comment ) {

							$user = get_user_by( 'email', $recorded_comment->comment_author_email );

							if ( is_wp_error( $user ) || ! is_a( 'WP_User', $user ) ) {
								continue;
							} else {
								var_Dump( $user ); die();
							}

							$user_id = $user->ID;

							$filter = array( 'object' => 'blogs', 'primary_id' => $blog['blog_id'], 'secondary_id' => $recorded_comment->comment_ID, 'action' => 'new_blog_comment' );
							$activities = bp_activity_get( array( 'filter' => $filter ) );

							if ( empty($activities->activities) ) {

								if ( (int)get_blog_option( $recorded_comment->blog_id, 'blog_public' )) {
									global $post;
									$comment_link = $post_permalink . '#comment-' . $recorded_comment->comment_ID;

									$activity_action = sprintf( __( '%s commented on the blog post %s', 'buddypress' ), bp_core_get_userlink( $user_id ), '<a href="' . $comment_link . '#comment-' . $recorded_comment->comment_ID . '">' . $post->post_title . '</a>' );
									$activity_content = $recorded_comment->comment_content;

									/* Record this in activity streams */
									bp_blogs_record_activity( array(
										'user_id' => $user_id,
										'action' => apply_filters( 'bp_blogs_activity_new_comment_action', $activity_action, $comment, $recorded_comment, $comment_link ),
										'content' => apply_filters( 'bp_blogs_activity_new_comment_content', $activity_content, $comment, $recorded_comment, $comment_link ),
										'primary_link' => apply_filters( 'bp_blogs_activity_new_comment_primary_link', $comment_link, $comment, $recorded_comment ),
										'type' => 'new_blog_comment',
										'item_id' => $blog['blog_id'],
										'secondary_item_id' => $recorded_comment->comment_ID,
										'recorded_time' => $recorded_comment->comment_date_gmt
									) );

									echo "Importing: \"" . $activity_action . ": " . $activity_content . "\"<br />";
								}
							}
						}
					} //  end while have_posts
				} // end if have_posts
			wp_reset_query();
			echo "<br /><br />";
			restore_current_blog();

		}

    }

}
?>
