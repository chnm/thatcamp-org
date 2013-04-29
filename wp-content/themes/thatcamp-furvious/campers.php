<?php
/*
Template Name: Campers
*/
?>

<?php get_header(); ?>

<div id="bigg">
	<div id="contentcontainer">
		<span class="contenttop"></span>
        <div class="clear"></div> 
		<ul id="content">
			<li class="entry_post">
				<div class=" pr30 pl30">
					<h1 class="campers_title maintitle"><a class="free" href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
					<p>THATCampers: to add your picture to this page, log in and upload a picture to your profile or sign up for <a href="http://en.gravatar.com/site/signup/">Gravatar.com</a> and upload an image there.</p>
					<?php $siteUsers = get_users('orderby=display_name&who=authors&exclude=246'); 
					foreach ($siteUsers as $usr) { ?>			
							<div class="camper_info">	 			
								<div class="camper_avatar"><a href="<?php echo get_author_posts_url($usr->ID); ?>"><?php echo get_avatar($usr->ID, 100); ?></a></div>
								<div class="camper_name"><a href="<?php echo get_author_posts_url($usr->ID); ?>"><?php the_author_meta('first_name', $usr->ID); ?>&nbsp;<?php the_author_meta('last_name', $usr->ID); ?></a></div>
								<div class="camper_posts"><a href="<?php echo get_author_posts_url($usr->ID); ?>">Posts (<?php echo get_usernumposts($usr->ID); ?>)</a></div>
							</div>	 
					<?php } ?>
				</div>		
			</li>
		</ul>
	</div>
		<?php get_sidebar(); ?>
</div>
<span class="contentbottom"></span>
<div class="clear"></div>
<?php get_footer(); ?>
