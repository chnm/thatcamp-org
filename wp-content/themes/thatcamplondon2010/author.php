<?php
/**
 * @package WordPress
 * @subpackage Thatcamp
 */

get_header();
?>

	<div id="content" class="narrowcolumn" role="main">
<?php
if(get_query_var('author_name')) :
    $curauth = get_userdatabylogin(get_query_var('author_name'));
else :
    $curauth = get_userdata(get_query_var('author'));
endif;
?>

<h2><?php echo $curauth->first_name; ?> <?php echo $curauth->last_name; ?></h2>
<div class="authoravat">
<div class="avat"><?php echo get_avatar( get_the_author_email(), '75' ); ?></div>
</div>
<hr />
<strong>Username:</strong> <?php echo $curauth->nickname; ?>
<hr />
<strong>Website:</strong> <a href="<?php echo $curauth->user_url; ?>" target="_blank"><?php echo $curauth->user_url; ?></a>
<hr />
<strong>Biography:</strong> <?php echo $curauth->description; ?>
<hr />
<h2><?php echo $curauth->first_name; ?> <?php echo $curauth->last_name; ?>'s Blog posts</h2>
		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>
        
<?php if (have_posts()) : ?>
 	  <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
 	  <?php /* If this is a category archive */ if (is_category()) { ?>
		
 	  <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
		
 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		
 	  <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		
 	  <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		
	  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
		
 	  <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		
 	  <?php } ?>

<?php while (have_posts()) : the_post(); ?>
		<div <?php post_class() ?>>
				<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
				<small><?php the_time('l, F jS, Y') ?></small>

				<div class="entry">
					<?php the_content() ?>
				</div>

				<p class="postmetadata"><?php the_tags('Tags: ', ', ', '<br />'); ?> Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>

			</div>
            <hr />

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>
	<?php else :

		if ( is_category() ) { // If this is a category archive
			printf("<h3>Sorry, but there aren't any posts in the %s category yet.</h3>", single_cat_title('',false));
		} else if ( is_date() ) { // If this is a date archive
			echo("<h3>Sorry, but there aren't any posts with this date.</h3>");
		} else if ( is_author() ) { // If this is a category archive
			$userdata = get_userdatabylogin(get_query_var('author_name'));
			printf("<h3>Sorry, but there aren't any posts by %s yet.</h3>", $userdata->display_name);
		} else {
			echo("<h3>No posts found.</h3>");
		}
		get_search_form();

	endif;
?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
