<?php get_header(); ?>
   
<div class="main thatcamp-stream">

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="post-avatar">
							<span class="img-wrapper"><?php echo get_avatar( get_the_author_meta( 'user_email' ), '50' ); ?></span>
					</div>
					<div class="post-meta">
						<header class="post-header">
							<?php
								// Sitewide Tags keeps the original post permalink in postmeta
								$permalink = get_post_meta( get_the_ID(), 'permalink' );
								if ( ! $permalink ) {
									$permalink = get_permalink();
								}

								// We need some data about the source blog
								$source_blog_url = $source_blog_name = $source_blog_link = '';
								$source_blog_id = get_post_meta( get_the_ID(), 'blogid', true );
								if ( $source_blog_id ) {
									$source_blog_url  = get_blog_option( $source_blog_id, 'home', true );
									$source_blog_name = get_blog_option( $source_blog_id, 'blogname', true );
									$source_blog_link = '<a href="' . $source_blog_url . '">' . $source_blog_name . '</a>';
								}
							?>
							<h1 class="post-title"><?php the_title(); ?></h1>
						</header>
						<span class="meta-author"><?php printf( _x( 'By %s', 'Post written by...', 'thatcamp' ), bp_core_get_userlink( $post->post_author ) ); ?></span>
						<?php if ( $source_blog_link ) : ?>
							<span class="meta-source"><?php printf( _x( 'at %s', 'From the blog...', 'thatcamp' ), $source_blog_link ) ?></span>												
						<?php endif ?>
						<span class="meta-date"><?php echo get_the_date(); ?></span>
						<span class="post-body"><?php the_content(); ?></span>
						<span class="meta-cat"><?php echo "Categories: " ?><?php the_category(', '); ?></span>
						<span class="meta-tags"><?php the_tags('Tags: ', ', ', '<br />'); ?></span>
					</div>
				</article>

    <?php if(get_the_author_meta('description')): ?>

    <div class="author-bio">

        <h2>
            About
            <?php if(function_exists('coauthors_posts_links'))
                coauthors_posts_links();
            else
                the_author_posts_link(); ?>
        </h2>


        <?php
            if(function_exists('coauthors')):
                $i = new CoAuthorsIterator();
                $i->iterate();
                print '<div class="gravatar">'.get_avatar(get_the_author_meta('ID'), $size = '52', $default = 'http://www.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536').'</div>';
                print '<p class="coauthor">'.get_the_author_meta('description').'</p>';
                while($i->iterate()){
                    print '<div class="gravatar">'.get_avatar(get_the_author_meta('ID'), $size = '52', $default = 'http://www.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536').'</div>';
                    print '<p class="coauthor">'.get_the_author_meta('description').'</p>';

                }
            else:
                print '<p>'.get_the_author_meta('description').'</p>';
            endif;
        ?>

    </div>

    <?php endif; ?>

<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts were found.'); ?></p>
<?php endif; ?>


</div>

<div class="sidebar">
    <?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>