<?php get_header(); ?>
<div id="bigg">
	<div id="contentcontainer">
	<span class="contenttop"></span> 
	<div class="clear"></div>
 		<h2 class="pagetitle">Search Results for "<?php echo $s; ?>"</h2>
		<ul id="content">
			<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
			<li class="entry index">
				<div class="entry_info">
					<div class="sidedateblock"><?php the_time('d'); ?><span><?php the_time('M'); ?></span></div>				
					
					<?php kreative_author_avatar($post->post_author); ?>
					<p class="authorp">Posted by<br /><?php the_author_posts_link(); ?></p>
					<br /> 
					<p>Category</p>
					<ul>
						<li class="sidecategory"><?php the_category('</li><li class="sidecategory">') ?></li> 
					</ul>		 
				</div>	 	
				<div class="entry_post"> 
					<div class=" pr30 pl30">
						<h1 class="maintitle">
							<a class="free" href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a>
						</h1>
						<div class="main_comment">
							<a href="<?php the_permalink() ?>#comments"><?php comments_number('0','1','%'); ?></a>
						</div>
						<div class="post">
							<?php the_content('&raquo; Read the rest of the entry.. ') ?> 
						</div>
					</div>
				</div>	
			</li>
			<?php endwhile; ?>		
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