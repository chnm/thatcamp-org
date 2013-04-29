<?php
/*
Template Name: Campers
*/
?>

<?php get_header(); ?>

<div id="container">
			<div id="content" role="main">
			<div class="entry_post">
									<h1 class="entry-title" style="font-size: 22px;line-height: 26.4px; font-weight:normal; margin-bottom: 12px;"><?php the_title(); ?></h1>
					<p style="margin-bottom:1em;">THATCampers: to add your picture to this page, <a href="/wp-login.php">log in</a> and upload a picture to your profile or sign up for <a href="http://en.gravatar.com/site/signup/">Gravatar.com</a> and upload an image there.</p>

<?php $siteUsers = get_users('orderby=display_name&exclude=246');

					foreach ($siteUsers as $usr) { ?>
							<div id="camper_info" style="float:left; display:inline; width:150px; height:170px; margin:5px;">
								<div id="camper_avatar"><a href="<?php echo get_author_posts_url($usr->ID); ?>"><?php echo get_avatar($usr->ID, 100); ?></a></div>
								<div id="camper_name"><a href="<?php echo get_author_posts_url($usr->ID); ?>">
		<?php echo get_the_author_meta('first_name', $usr->ID); ?> <?php echo get_the_author_meta('last_name', $usr->ID); ?>
</a></div>
								<div id="camper_posts"><a href="<?php echo get_author_posts_url($usr->ID); ?>">Posts (<?php echo get_usernumposts($usr->ID); ?>)</a><br /><br /></div>
							</div>
					<?php } ?>
			</div>

			</div><!-- #content -->
</div><!-- #container -->

<span class="contentbottom"></span>
<div class="clear"></div>
<?php get_footer(); ?>
