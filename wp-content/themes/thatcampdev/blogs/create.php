<?php

/**
 * BuddyPress - Create Blog
 *
 * @package logicalbones
 * @since logicalbones 1.0
 */

get_header( 'thatcamp' ); ?>

	<?php do_action( 'bp_before_directory_blogs_content' ); ?>

	<div id="primary" class="main-content">
	<div id="content" role="main">
		
		<?php do_action( 'bp_before_create_blog_content_template' ); ?>

		<?php do_action( 'template_notices' ); ?>

			<h3><?php _e( 'Create a Site', 'thatcamp' ); ?> &nbsp;<a class="button" href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_blogs_root_slug() ); ?>"><?php _e( 'Site Directory', 'thatcamp' ); ?></a></h3>

		<?php do_action( 'bp_before_create_blog_content' ); ?>

		<?php if ( bp_blog_signup_enabled() ) : ?>

			<?php bp_show_blog_signup_form(); ?>

		<?php else: ?>

			<div id="message" class="info">
				<p><?php _e( 'Site registration is currently disabled', 'thatcamp' ); ?></p>
			</div>

		<?php endif; ?>

		<?php do_action( 'bp_after_create_blog_content' ); ?>
		
		<?php do_action( 'bp_after_create_blog_content_template' ); ?>

		</div>
	</div>

	<?php do_action( 'bp_after_directory_blogs_content' ); ?>

<?php get_sidebar( 'thatcamp' ); ?>
<?php get_footer( 'thatcamp' ); ?>

