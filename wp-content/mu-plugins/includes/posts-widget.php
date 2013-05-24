<?php

class THATCamp_Blogs_Recent_Posts_Widget extends BP_Blogs_Recent_Posts_Widget {
	function __construct() {
		parent::__construct( false, $name = _x( '(BuddyPress) Recent Networkwide Posts', 'widget name', 'buddypress' ) );
	}

	function widget($args, $instance) {
		extract( $args );

		echo $before_widget;
		echo $before_title . 'Posts' . $after_title;

		if ( empty( $instance['max_posts'] ) || !$instance['max_posts'] )
			$instance['max_posts'] = 10; ?>

		<?php // Override some of the contextually set parameters for bp_has_activities() ?>
		<?php if ( bp_has_activities( array( 'action' => 'new_blog_post', 'max' => $instance['max_posts'], 'per_page' => $instance['max_posts'], 'user_id' => 0, 'scope' => false, 'object' => false, 'primary_id' => false ) ) ) : ?>

			<ul id="blog-post-list" class="activity-list item-list">

				<?php while ( bp_activities() ) : bp_the_activity(); ?>

					<li>
						<div class="activity-content" style="margin: 0">
							<div class="activity-header">
								<?php bp_activity_action() ?>
							</div>
						</div>
					</li>

				<?php endwhile; ?>

			</ul>

		<?php else : ?>
			<div id="message" class="info">
				<p><?php _e( 'Sorry, there were no posts found. Why not write one?', 'buddypress' ) ?></p>
			</div>
		<?php endif; ?>

		<?php echo $after_widget; ?>
	<?php
	}


}
