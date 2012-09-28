<?php
/**
 * 404 page
 *
 * @package logicalbones
 * @since logicalbones 1.0
 */
?>
<?php get_header('signup'); ?>
<div id="primary" class="main-content">
	<div id="content" class="clearfix" role="main" >
		<?php do_action( 'bp_before_404' ); ?>
			<article id="post-0" class="post error404 not-found">
				<header class="post-header">
					<h1 class="post-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'logicalbones' ); ?></h1>
				</header>

				<div class="post-content">
					<p><?php _e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'logicalbones' ); ?></p>

					<?php get_search_form(); ?>

					<?php the_widget( 'WP_Widget_Recent_Posts' ); ?>

					<aside class="widget">
						<h2 class="widgettitle"><?php _e( 'Most Used Categories', 'logicalbones' ); ?></h2>
						<ul>
						<?php wp_list_categories( array( 'orderby' => 'count', 'order' => 'DESC', 'show_count' => 1, 'title_li' => '', 'number' => 10 ) ); ?>
						</ul>
					</aside>

					<?php
					$archive_content = '<p>' . sprintf( __( 'Try looking in the monthly archives. %1$s', 'logicalbones' ), convert_smilies( ':)' ) ) . '</p>';
					the_widget( 'WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$archive_content" );
					?>

					<?php the_widget( 'WP_Widget_Tag_Cloud' ); ?>
					<?php do_action( 'bp_404' ); ?>
				</div>
			</article>>
			<?php do_action( 'bp_after_404' ); ?>
	</div>
</div>
<?php get_footer(); ?>