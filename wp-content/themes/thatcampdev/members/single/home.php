<?php

/**
 * BuddyPress - Users Home
 *
 * @package logicalbones
 * @since logicalbones 1.0
 */

get_header( 'thatcamp' ); ?>

<?php do_action( 'bp_before_member_home_content' ); ?>	
	<?php get_sidebar( 'profile' ); ?>
	<div id="primary" class="main-content">
	<div id="content" class="clearfix" role="main">
		<div id="item-header">
			<h2>
				<a href="<?php bp_displayed_user_link(); ?>"><?php bp_displayed_user_fullname(); ?></a>
			</h2>
			<span class="user-nicename">@<?php bp_displayed_user_username(); ?></span>
			<div class="feature-box">
				<p>
					Vestibulum id ligula porta felis euismod semper. Nulla vitae elit libero, a pharetra augue. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Cras mattis consectetur purus sit amet fermentum. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit.
				</p>
			</div>
		</div>
			<div id="item-body">
				<?php do_action( 'bp_before_member_body' );

				if ( bp_is_user_activity() || !bp_current_component() ) :
					locate_template( array( 'members/single/activity.php'  ), true );

				 elseif ( bp_is_user_blogs() ) :
					locate_template( array( 'members/single/blogs.php'     ), true );

				elseif ( bp_is_user_friends() ) :
					locate_template( array( 'members/single/friends.php'   ), true );

				elseif ( bp_is_user_groups() ) :
					locate_template( array( 'members/single/groups.php'    ), true );

				elseif ( bp_is_user_messages() ) :
					locate_template( array( 'members/single/messages.php'  ), true );

				elseif ( bp_is_user_profile() ) :
					locate_template( array( 'members/single/profile.php'   ), true );

				elseif ( bp_is_user_forums() ) :
					locate_template( array( 'members/single/forums.php'    ), true );

				elseif ( bp_is_user_settings() ) :
					locate_template( array( 'members/single/settings.php'  ), true );

				else :
					locate_template( array( 'members/single/plugins.php'   ), true );

				endif;

				do_action( 'bp_after_member_body' ); ?>

			</div>

			<?php do_action( 'bp_after_member_home_content' ); ?>

		</div>
	</div>
<?php get_footer( 'thatcamp' ); ?>
