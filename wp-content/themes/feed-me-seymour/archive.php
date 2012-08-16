<?php get_header(); ?>
    <?php if (have_posts()) : ?>
    
    <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
    <?php /* If this is a category archive */ if (is_category()) { ?>
    <h1 class="catheader"><?php single_cat_title(); ?></h1>
    <?php $catdesc = category_description(); if(stristr($catdesc,'<p>')) { echo '<div class="catdesc">'.$catdesc.'</div>'; } ?>   
    <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
    <h1 class="catheader"><?php printf(__("Posts Tagged &#8216; %s &#8217;", "feed-me-seymour"), single_tag_title('',false)); ?></h1>
    <div id="tagcloud"><?php wp_tag_cloud('smallest=8&largest=16'); ?></div>
    <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
    <h1 class="catheader"><?php _e("Archive for ", "feed-me-seymour").the_time('F jS, Y'); ?></h1>
    <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
    <h1 class="catheader"><?php _e("Archive for ", "feed-me-seymour").the_time('F, Y'); ?></h1>
    <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
    <h1 class="catheader"><?php _e("Archive for ", "feed-me-seymour").the_time('Y'); ?></h1>
    <?php /* If this is an author archive */ } elseif (is_author()) { ?>
    <h1 class="catheader"><?php _e("Author Archive", "feed-me-seymour"); ?></h1>
    <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
    <h1 class="catheader"><?php _e("Blog Archives", "feed-me-seymour"); ?></h1>
    <?php } ?>

		<?php while (have_posts()) : the_post(); ?>
			<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink() ?>" title="<?php printf(__("Permanent Link to %s", "feed-me-seymour"), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a></h2>
				<div class="thedate"><?php the_time(get_option('date_format')); ?></div>
				<div class="entry">
		            <?php 
					if(function_exists('has_post_thumbnail') && has_post_thumbnail()) { 
						echo '<a href="'.get_permalink().'">';
						the_post_thumbnail('thumbnail', array('class'=>'alignleft'));
						echo '</a>';
					} else { 
						echo resize(get_option('thumbnail_size_w'),get_option('thumbnail_size_h')); 
					}
					?>
					<?php the_excerpt() ?>
				</div>
				<p class="meta"><?php the_tags(__('Tags', "feed-me-seymour").": ", ', ', '<br />'); echo __('Posted in', "feed-me-seymour")." "; the_category(', ') ?> | <?php edit_post_link(__('Edit', "feed-me-seymour"), '', ' | '); ?>  <?php comments_popup_link(__('No Comments &#187;', "feed-me-seymour"), __('1 Comment &#187;', "feed-me-seymour"), __('% Comments &#187;', "feed-me-seymour")); ?></p>

			</div>

		<?php endwhile; ?>
		<?php if ( $wp_query->max_num_pages > 1 ) : ?>
		<div class="navigation">
			<div class="alignleft"><?php next_posts_link(__('&laquo; Older Entries', "feed-me-seymour")) ?></div>
			<div class="alignright"><?php previous_posts_link(__('Newer Entries &raquo;', "feed-me-seymour")) ?></div>
		</div>
        <?php endif; ?>
	<?php else :

		if ( is_category() ) { // If this is a category archive
			printf(__("<h2 class='center'>Sorry, but there aren't any posts in the %s category yet.</h2>", "feed-me-seymour"), single_cat_title('',false));
		} else if ( is_date() ) { // If this is a date archive
			_e("<h2>Sorry, but there aren't any posts with this date.</h2>", "feed-me-seymour");
		} else if ( is_author() ) { // If this is a category archive
			$userdata = get_userdatabylogin(get_query_var('author_name'));
			printf(__("<h2 class='center'>Sorry, but there aren't any posts by %s yet.</h2>", "feed-me-seymour"), $userdata->display_name);
		} else {
			_e("<h2 class='center'>No posts found.</h2>", "feed-me-seymour");
		}

	endif;
?>

<?php get_footer(); ?>
