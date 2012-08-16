<?php get_header(); ?>
<div id="bigg">
	<div id="contentcontainer">
		<span class="contenttop"></span> 
		<div class="clear"></div>
		<ul id="content">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<li class="entry">
				<div class="entry_info">
					<div class="sidedateblock"><?php the_time('d'); ?><span><?php the_time('M'); ?></span></div>				
					
					<p><?php kreative_author_avatar($post->post_author); ?></p>
					<p class="authorp">Posted by<br /><?php the_author_posts_link(); ?></p>
					<br /> 
					<p>Category</p>
					<ul>
						<li class="sidecategory"><?php the_category('</li><li class="sidecategory">') ?></li> 
					</ul>
				</div>	 
				<div class="entry_post"> 
					<div class=" pr30 pl30">
						<h1 class="maintitle"><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>
						<div class="main_comment"><a href="<?php the_permalink() ?>#comments"><?php comments_number('0','1','%'); ?></a></div>
						<div class="post">
							<?php the_content('Read more') ?> 
						</div>
						<?php if (get_the_tags());?>
							<p class="tagg"><?php the_tags(); ?></p>
					</div>
				</div>
			</li> 
			<?php endwhile; ?>	
		</ul><!-- end of content -->
		
		<div id="promote">
			<div class="promote_bottombg">
			<div class="p_leftcol">
				<h2>Related Entries</h2>
				<ul>
					<?php kreative_related_posts(5, 10, '<li>', '</li>'); ?> 
				</ul>
			</div>
			<div class="p_rightcol">
				<h2>Share!</h2>
				<ul>
					<li><a href="http://digg.com/submit?phase=2&url=<?php the_permalink(); ?>&title=<?php the_title(); ?>" target="_blank"><img src="<?php bloginfo('template_url'); ?>/images/digg.png" alt="Digg" /></a></li>
					<li><a href="http://delicious.com/post?url=<?php the_permalink(); ?>&title=<?php the_title(); ?>" target="_blank"><img src="<?php bloginfo('template_url'); ?>/images/delicious.png" alt="delicious" /></a></li>
					<li><a href="http://www.stumbleupon.com/submit?url=<?php the_permalink(); ?>&title=<?php the_title(); ?>" target="_blank"><img src="<?php bloginfo('template_url'); ?>/images/stumbleupon.png" alt="stumbleupon" /></a></li>
					<li> <a href="http://twitter.com/home?status=<?php the_title(); ?> - <?php the_permalink(); ?>" target="_blank"><img src="<?php bloginfo('template_url'); ?>/images/twitter.png" alt="twitter" /></a></li>
				</ul>
			</div>
			<div class="clear"></div>
			</div>
		</div>		
		
		<?php comments_template('', true); ?>
		
		<?php else: ?>
			<?php include (TEMPLATEPATH . '/notfound.php'); ?>
		<?php endif; ?>
	</div>
	<?php get_sidebar(); ?>
</div>
<span class="contentbottom"></span>
<div class="clear"></div>
<?php get_footer(); ?>
