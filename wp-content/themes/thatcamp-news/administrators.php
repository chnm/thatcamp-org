<?php
/*
Template Name: Administrators
*/
?>

<?php get_header(); ?>
<!-- page.php -->
<div id="content">
    <div id="primary">
    
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="post" id="post-<?php the_ID(); ?>">
		<h1><?php the_title(); ?></h1>
				<?php /* Display campers */ 
                                   $siteUsers = get_users('orderby=display_name&role=administrator');                          
                                
               	                   foreach ($siteUsers as $usr) { ?>
		<div class="camper_info">
					<div class="camper_avatar"><a href="<?php echo get_author_posts_url($usr->ID); ?>">
<?php echo get_avatar($usr->ID, 100); ?></a></div>
					<div class="camper_name"><a href="<?php echo get_author_posts_url($usr->ID); ?>">
<?php echo $usr->display_name;  ?></a></div>
					<div class="camper_posts"><a href="<?php echo get_author_posts_url($usr->ID); ?>">Posts (<?php echo get_usernumposts($usr->ID); ?>)</a></div>
		</a>			
		 </div>
<?php } ?>

<div class="bottom">
				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				
				<?php previous_link(); ?><?php next_link(); ?>	<br /><br />
</div>				
				
		</div>
		<?php endwhile; endif; ?>
		
         <?php edit_post_link(__('Edit this entry')); ?>
	</div>

<?php get_sidebar(); ?>
	
<?php get_footer(); ?>

