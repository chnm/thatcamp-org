<?php get_header(); ?>
<div id="bigg">
	<div id="contentcontainer">
		<span class="contenttop"></span>
		<div class="clear"></div>
		<ul id="content">
			
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
			<li class="entry index">
				<div class="entry_info">
					<div class="sidedateblock">
						<?php the_time('d'); ?><span><?php the_time('M'); ?></span>
					</div>	
								
					<p><?php kreative_author_avatar($post->post_author); ?></p>
				    <?php thatcamp_add_friend_button( get_the_author_ID() ) ?>                
					<p class="authorp">Posted by<br /><?php the_author_posts_link(); ?>
					</p>
					<br /> 
					<p>Categories</p>
					<ul>
						<li class="sidecategory"><?php the_category('</li><li class="sidecategory">') ?></li> 
					</ul>
				</div>	 
				<div class="entry_post"> 
					<div class=" pr30 pl30">
						<h1 class="maintitle">
							<a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a>
						</h1>
						<div class="main_comment">
							<a href="<?php the_permalink() ?>#comments"><?php comments_number('0','1','%'); ?></a>
						</div>
						<div class="post">
							<?php the_content('Read more') ?> 
						</div>
							<?php if (get_the_tags());?>
							<p class="tagg"><?php the_tags(); ?></p>						
					</div>
				</div>
			</li>
			<?php endwhile;?>
			<?php else: ?>
			<?php include (TEMPLATEPATH . '/notfound.php'); ?>		
			<?php endif; ?>
		</ul><!--end of #content -->
	</div>
	<?php get_sidebar(); ?>
</div>
<span class="contentbottom"></span>
<div class="clear"></div>
<div class="paginationbar">
	<?php kreative_pagenavi(); ?>
</div>
<?php get_footer(); ?>
