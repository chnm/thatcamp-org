<?php get_header(); ?>
<div id="content" class="home">
<div id="primary">
	<?php if(have_posts()): while (have_posts()) : the_post(); ?>
	<div class="post">
			<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
			<p><strong><?php the_time('l, F jS, Y') ?> | <a href="http://thatcampdev.info/camper/<?php the_author_login(); ?>"><?php the_author(); ?></a></strong></p>

			<div class="entry">
				<?php the_content() ?>
			</div>

			<p class="postmetadata"><?php the_tags('Tags: ', ', ', '<br />'); ?> Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>

		</div>

	<?php endwhile; endif; ?>

<div class="navigation">
			<div class="alignleft"><?php next_posts_link(__('&laquo; Older Entries', 'kubrick')) ?></div>
			<div class="alignright"><?php previous_posts_link(__('Newer Entries &raquo;', 'kubrick')) ?></div>
</div>
		
</div>

<div id="secondary">
	<h3>What Camp? THATCamp!</h3>
	<p>Short for “The Humanities and Technology Camp”, THATCamp is a BarCamp-style, user-generated “unconference” on digital humanities. THATCamp is organized and hosted by the <a href="http://chnm.gmu.edu">Center for History and New Media</a> at <a href="http://www.gmu.edu">George Mason University</a>, <a href="http://digitalcampus.tv">Digital Campus</a>, and <a href="http://thatpodcast.org">THATPodcast</a>. <a href="/about/">Learn more&hellip;.</a></p>
</div>
</div>
<?php get_footer(); ?>
