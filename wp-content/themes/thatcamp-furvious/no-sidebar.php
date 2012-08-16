<?php
/*
Template Name: No Sidebar
*/
?>

<?php get_header(); ?>
<div id="bigg">

	<div id="contentcontainer" style="width:950px;">
		<ul id="content" style="width:950px;">
			<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
			<li class="entry index" style="width:950px;">	
				<div class="entry_post" style="width:940px;"> 
					<div class=" pr30 pl30" style="width:950px;">
						<h1 class="maintitle"><a class="free" href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>
						<div class="post" style="width:880px;">
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
</div>
<span class="contentbottom"></span>
<div class="clear"></div>
<?php get_footer(); ?>
