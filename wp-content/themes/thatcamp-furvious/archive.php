<?php get_header(); ?>
<div id="bigg">
	<div id="contentcontainer">
		<span class="contenttop"></span> 
		<div class="clear"></div>
		
		<?php if (have_posts()) : ?>
			<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
			<?php /* If this is a category archive */ if (is_category()) { ?>
				<h2 class="pagetitle">Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category</h2>
			<?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
				<h2 class="pagetitle">Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;</h2>
			<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
				<h2 class="pagetitle">Archive for <?php the_time('F jS, Y'); ?></h2>
			<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
				<h2 class="pagetitle">Archive for <?php the_time('F, Y'); ?></h2>
			<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
				<h2 class="pagetitle">Archive for <?php the_time('Y'); ?></h2>
			<?php /* If this is an author archive */ } elseif (is_author()) { ?>
				<h2 class="pagetitle">Author Archive  </h2>
			<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
				<h2 class="pagetitle">Blog Archives</h2>
		<?php } ?>
		
		<ul id="content">
		<?php while(have_posts()) : the_post(); ?>
		
			<li class="entry index">
				<div class="entry_info">
					<div class="sidedateblock">
						<?php the_time('d'); ?> <span><?php the_time('M'); ?> </span> 
					</div>				
					
					<p><?php kreative_author_avatar($post->post_author); ?></p>
					<p class="authorp">Posted by<br /><?php the_author_posts_link(); ?></p>
				    <?php thatcamp_add_friend_button( get_the_author_ID() ) ?>                
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