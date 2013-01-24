<?php
/*
Template Name: Campers No Sidebar
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
						<div class="post">
<h1 class="campers_title maintitle"><a class="free" href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
					<p>THATCampers: to add your picture to this page, sign up for <a href="http://en.gravatar.com/site/signup/">Gravatar.com</a> and upload an image there.</p>
<?php $siteUsers = get_users('orderby=display_name&who=authors&exclude=246'); 
					foreach ($siteUsers as $usr) { ?>			
				<div class="camper_info">	 			
					<div class="camper_avatar"><a href="<?php echo get_author_posts_url($usr->ID); ?>"><?php echo get_avatar($usr->ID, 100); ?></a></div>
					<div class="camper_name"><a href="<?php echo get_author_posts_url($usr->ID); ?>"><?php the_author_meta('first_name', $usr->ID); ?>&nbsp;<?php the_author_meta('last_name', $usr->ID); ?></a></div>
					<div class="camper_posts"><a href="<?php echo get_author_posts_url($usr->ID); ?>">Posts (<?php echo get_usernumposts($usr->ID); ?>)</a></div>
				</div>	 
<?php } ?>
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
