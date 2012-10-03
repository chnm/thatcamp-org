<?php
/**
 * Index main page
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>
<?php get_header('front'); ?>
<div id="primary" class="main-content">
	<div id="content" class="clearfix" role="main">
		<div id="upcoming-camps" class="clearfix feature-box">
			<h2>Upcoming THATCamps</h2>

			<?php $group_args = array(
				'per_page' => 10
			) ?>

			<?php if ( bp_has_groups( $group_args ) ) : while ( bp_groups() ) : bp_the_group() ?>

			<article class="camp-listitem">
				<div class="camp-listdate"><?php thatcamp_camp_date() ?></div>
				<h3><a href="<?php thatcamp_camp_permalink() ?>" class="camplink"><?php bp_group_name() ?></a></h3>
				<p class="camp-listmeta">Workshops <span class="camp-list<?php thatcamp_camp_has_workshops() ?>"><?php thatcamp_camp_has_workshops() ?></span></p>
			</article>

			<?php endwhile; endif ?>
		</div>

		<div id="latest-posts" class="clearfix feature-box">
			<h2>Blog posts</h2>
			<?php rewind_posts();
			while ( have_posts() ) : the_post();
				get_template_part( 'content', 'latestposts' );
			endwhile;?>
			<a href="" class="button postbutton offset">
				<span class="button-inner">View all posts</span>
			</a>
		</div>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer() ?>
