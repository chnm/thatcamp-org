<?php
/**
 * The template for displaying Author Archive pages.
 *
 * @package WordPress
 * @subpackage Boilerplate
 * @since Boilerplate 1.0
 */

get_header(); ?>

<?php
	/* Queue the first post, that way we know who
	 * the author is when we try to get their name,
	 * URL, description, avatar, etc.
	 *
	 * We reset this later so we can run the loop
	 * properly with a call to rewind_posts().
	 */

	if ( have_posts() )
		the_post();
?>


<h1><?php printf( __( 'Author Archives: %s', 'boilerplate' ), "<a class='url fn n' href='" . get_author_posts_url( get_the_author_meta( 'ID' ) ) . "' title='" . esc_attr( get_the_author() ) . "' rel='me'>" . get_the_author() . "</a>" ); ?></h1>

<footer id="entry-author-info" class="top">
<?php
// If a user has filled out their description, show a bio on their entries.
if ( get_the_author_meta( 'description' ) ) : ?>

<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'boilerplate_author_bio_avatar_size', 60 ) ); ?>

<h2 id="author-meta-name"><?php printf( __( 'About %s', 'boilerplate' ), get_the_author() ); ?></h2>

<span id="author-meta-description">

	<span class="links">				
	<!-- twitter -->
 	 <? if (get_the_author_meta( 'user_twitter' ) != null){
 	 	echo 'Twitter: <a href="http://twitter.com/'.get_the_author_meta( 'user_twitter' ).'">@'.get_the_author_meta( 'user_twitter' ).'</a><br>';
 	 	}
 	 ?>

	<!-- website -->
 	 <? 
 	 $website=get_the_author_meta( 'user_url' );
 	 $siteroot=parse_url($website);
 	 if ( $website != null){
 	 	echo 'Website: <a href="'.$website.'">'.$siteroot[host].'</a>';
 	 	}
 	 ?>	
 	 </span>	
 	 
 	 <span class="bio show"><?php the_author_meta( 'description' ); ?></span>
	
					
							
</span>

<?php endif; ?>
<div class="clearfix"></div>
</footer>

<?php

	//if ( !have_posts() && (isset($_GET['author_name'])) )
		//echo 'foo';
?>

<?php
	/* Since we called the_post() above, we need to
	 * rewind the loop back to the beginning that way
	 * we can run the loop properly, in full.
	 */
	rewind_posts();

	/* Run the loop for the author archive page to output the authors posts
	 * If you want to overload this in a child theme then include a file
	 * called loop-author.php and that will be used instead.
	 */
	 get_template_part( 'loop', 'author' );
?>




<?php get_sidebar(); ?>
<?php get_footer(); ?>