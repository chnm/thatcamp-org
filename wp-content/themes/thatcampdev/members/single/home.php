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
	<div id="content" role="main">
		<div id="item-header">
			<span class="user-nicename">@<?php bp_displayed_user_username(); ?></span>
		</div>
		<div class="feature-box">
			<p>
				Vestibulum id ligula porta felis euismod semper. Nulla vitae elit libero, a pharetra augue. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Cras mattis consectetur purus sit amet fermentum. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit.
			</p>
		</div>
			<div id="item-body">
				<?php do_action( 'bp_before_member_body' );

				if ( bp_is_user_activity() || !bp_current_component() ) :
					get_template_part( 'members/single/activity'); 

				 elseif ( bp_is_user_blogs() ) :
					get_template_part( 'members/single/blogs'); 

				elseif ( bp_is_user_friends() ) :
					get_template_part( 'members/single/friends'); 

				elseif ( bp_is_user_groups() ) :
					get_template_part( 'members/single/groups'); 

				elseif ( bp_is_user_messages() ) :
					get_template_part( 'members/single/messages'); 

				elseif ( bp_is_user_profile() ) :
					get_template_part( 'members/single/profile'); 

				elseif ( bp_is_user_forums() ) :
					get_template_part( 'members/single/forums'); 

				elseif ( bp_is_user_settings() ) :
					get_template_part( 'members/single/settings'); 

				else :
					get_template_part( 'members/single/plugins'); 

				endif;

				do_action( 'bp_after_member_body' ); ?>

			</div>

			<?php do_action( 'bp_after_member_home_content' ); ?>

		</div>
	</div>
<?php get_footer( 'thatcamp' ); ?>
