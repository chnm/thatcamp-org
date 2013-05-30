<?php get_header(); ?>
<div id="content" class="home">
    <div id="primary" class="five">
        <?php if(is_home()): ?>
    <p class="explanation five">THATCamp is a user-generated &#8220;unconference&#8221; on digital humanities organized and hosted by the <a href="http://chnm.gmu.edu">Center for History and New Media</a> at <a href="http://www.gmu.edu">George Mason University</a>, June 27&ndash;28, 2009. <a href="/about/">Learn More</a></p>
    
    <?php endif; ?>
	<?php if(have_posts()): ?>
		
		<div id="blog">
		    <h2>Latest Posts</h2>
		
			<?php while (have_posts()) : the_post(); ?>
			<div class="post">
				<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
				<p><strong><?php the_time('l, F jS, Y') ?> | <a href="http://thatcampdev.info/camper/<?php the_author_login(); ?>"><?php the_author(); ?></a> | <?php //echo the_author_meta('ID'); ?></strong></p>
				<?php thatcamp_add_friend_button( get_the_author_ID() ) ?>
				<div class="entry">
					<?php the_content() ?>
				</div>

				<p class="postmetadata"><?php the_tags('Tags: ', ', ', '<br />'); ?> Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>

			</div>
			<?php endwhile; ?>
		</div>
		<?php endif; ?>	
	</div>
	</div>
<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
