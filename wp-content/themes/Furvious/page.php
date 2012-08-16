<?php get_header(); ?>
<div id="bigg">
	<div id="contentcontainer">
		<span class="contenttop"></span>
        <div class="clear"></div> 
		<ul id="content">
			<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
			<li class="entry index">
				<div class="entry_info">	 			
					
					<p><?php kreative_author_avatar($post->post_author); ?></p>
					<p class="authorp">Posted by<br /><?php the_author_posts_link(); ?></p>
				</div>	 	
				<div class="entry_post"> 
					<div class=" pr30 pl30">
						<h1 class="maintitle"><a class="free" href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>
						<div class="post">
							<?php the_content('Read more') ?>
						</div>
					</div>
				</div>	
			</li>
			<?php endwhile; ?>			
			<?php else: ?>
			<?php include (TEMPLATEPATH . '/notfound.php'); ?>
			<?php endif; ?>
		</ul>
	</div>
	<?php get_sidebar(); ?>
</div>
<span class="contentbottom"></span>
<div class="clear"></div>
<?php get_footer(); ?>