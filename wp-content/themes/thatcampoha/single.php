<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Boilerplate
 * @since Boilerplate 1.0
 */

get_header(); ?>
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
				
				<!-- 
				<nav id="nav-above" class="navigation">
					<?php// previous_post_link( '%link', '' . _x( '&larr;', 'Previous post link', 'boilerplate' ) . '<span class="np-post">Previous Post:</span> %title' ); ?>
					<?php// next_post_link( '%link', '<span class="np-post">Next Post:</span> %title ' . _x( '&rarr;', 'Next post link', 'boilerplate' ) . '' ); ?>
					<div class="clearfix"></div>
				</nav>
		        -->
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h1><?php the_title(); ?></h1>
					<div class="entry-meta">
						<?php boilerplate_posted_on(); ?>
					</div><!-- .entry-meta -->
				                
					<div class="entry-content">
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '' . __( 'Pages:', 'boilerplate' ), 'after' => '' ) ); ?>
					</div><!-- .entry-content -->
					
					<div class="clearfix"></div>
					
<?php if ( get_the_author_meta( 'description' ) ) : // If a user has filled out their description, show a bio on their entries  ?>
					<footer id="entry-author-info">
						<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'boilerplate_author_bio_avatar_size', 60 ) ); ?>
						
						<h2 id="author-meta-name"><?php printf( esc_attr__( 'About %s', 'boilerplate' ), get_the_author() ); ?></h2>
						<span id="author-meta-description">
						
						<!-- twitter -->
					 	 <?php 
					 	 $twitter=get_the_author_meta( 'user_twitter' );
					 	 if ($twitter != null){
					 	 	echo 'Twitter: <a href="http://twitter.com/'.$twitter.'">@'.$twitter.'</a><br>';
					 	 	}
					 	 ?>		
					 	 
					 	 <!-- website -->
					 	 <?php 
					 	 $website=get_the_author_meta( 'url' );
					 	 $siteroot=parse_url($website);
					 	 if ( $website != null){
					 	 	echo 'Website: <a href="'.$website.'">'.$siteroot[host].'</a><br/>';
					 	 	}
					 	 ?>	 										
						</span>

						<!-- bio -->
					 	 <?php 
					 	 $bio=get_the_author_meta( 'description' );
					 	 if ($bio != null){
					 	 	echo '<span id="author-meta-description" class="bio show">'.$bio.'</span>';
					 	 	}
					 	 ?>	
					 	 						
						<a id="author-meta-link" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
							<?php printf( __( 'All posts by %s &rarr;', 'boilerplate' ), get_the_author() ); ?>
						</a>
					</footer><!-- #entry-author-info -->
<?php endif; ?>
					<footer class="entry-utility">
						<?php boilerplate_posted_in(); ?>
						<?php edit_post_link( __( 'Edit', 'boilerplate' ), '<span class="edit-link">', '</span>' ); ?>
					</footer><!-- .entry-utility -->
				</article><!-- #post-## -->
				<nav id="nav-below" class="navigation">
					<?php previous_post_link( '%link', '' . _x( '&larr;', 'Previous post link', 'boilerplate' ) . '<span class="np-post">Previous Post:</span> %title' ); ?>
					<?php next_post_link( '%link', '<span class="np-post">Next Post:</span> %title ' . _x( '&rarr;', 'Next post link', 'boilerplate' ) . '' ); ?>
					<div class="clearfix"></div>
				</nav><!-- #nav-below -->
				<?php comments_template( '', true ); ?>
<?php endwhile; // end of the loop. ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
