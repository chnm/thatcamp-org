<?php get_header(); ?>
<div id="content" class="home">

    <div id="primary">
        <?php if(is_home()): ?>
        <p id="explanation">THATCamp is a user-generated &#8220;unconference&#8221; on digital humanities. This particular THATCamp was organized and hosted by the <a href="http://chnm.gmu.edu">Center for History and New Media</a> at <a href="http://www.gmu.edu">George Mason University</a> on May 22&ndash;23, 2010. For the main THATCamp site, see <a href="http://thatcamp.org">http://thatcamp.org</a></p>
    
    <?php endif; ?>
	<?php if(have_posts()): ?>
		
		<div id="blog">
		    <h2>Latest Posts</h2>
		
			<?php while (have_posts()) : the_post(); ?>
			<div class="post">
				<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
				<p><strong><?php the_time('l, F jS, Y') ?> | <?php the_author_posts_link(); ?></strong></p>
				<?php thatcamp_add_friend_button( get_the_author_ID() ) ?>
				<div class="entry">
					<?php the_content() ?>
				</div>

				<p class="postmetadata"><?php the_tags('Tags: ', ', ', '<br />'); ?> Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>

			</div>
			<?php endwhile; ?>
		</div>
		
		<div id="bottom-navigation" class="navigation">
				<div class="previous"><?php next_posts_link('&laquo; Older Entries') ?></div>
				<div class="next"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>
		<?php endif; ?>			
	</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
