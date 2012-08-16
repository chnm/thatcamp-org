<?php
/**
 * Template Name: Author
 *
 * A custom page template for making a Camper / Author page on a THATCamp website
 */
 ?>
 
<?php get_header(); ?>

		<div id="container">
			<div id="content" role="main">

<?php
if(isset($_GET['author_name'])) :
$current_camper = get_userdatabylogin($author_name);
else :
$current_camper = get_userdata(intval($author));
endif;
?>

<?php 
// Used to generate ID for vcard
$firstname = strtolower($current_camper->first_name);
$lastname = strtolower($current_camper->last_name);

?>


<div id="author-0" class="post-103 post type-post hentry category-general clearfix page author nodate">

<h1><?php echo $current_camper->first_name;if(!empty($current_camper->last_name)) echo ' '; echo $current_camper->last_name; ?>
			<?php if(empty($current_camper->first_name)): ?>
			<?php echo $current_camper->display_name; ?>
			<?php endif; ?>
</h1>


    <?php if (@$_GET['dev'] == 1) print_r($current_camper); ?>

	<div class="entry author-entry clearfix" id="author-<?php echo $lastname; ?>">

    <?php echo get_avatar( $current_camper->user_email, $size = '150'); ?>&nbsp;&nbsp;	
		<ul id="author-info">
		    <?php if ( $title = $current_camper->user_title ): ?>
			<li class="title"><?php echo $current_camper->user_title; ?></li>
			<?php endif; ?>
			<?php if ($institution = $current_camper->user_organization): ?>
			<li class="institution"><?php echo $current_camper->user_organization; ?></li>
			<?php endif; ?>
			<?php
			// Checks to see if the user_url isn't empty and if its length is greater than 7 (so as not to write http:// only links) 
			if(!empty($current_camper->user_url) && strlen($current_camper->user_url) > 7): ?>
			<li>Website: <a href="<?php echo $current_camper->user_url; ?>" class="url"><?php echo str_replace("http://","",$current_camper->user_url); ?></a></li>
			<?php endif; ?>
			
			<?php if(!empty($current_camper->user_twitter)): ?>
			<li>Twitter: <a href="http://twitter.com/<?php echo $current_camper->user_twitter; ?>/"><?php echo $current_camper->user_twitter; ?></a></li>
			<?php endif; ?>
		</ul>
		
		<?php echo wpautop($current_camper->user_description, 0); ?>
	</div>
</div>	

	<?php if(have_posts()): ?>

	<div class="post">

		<h3 class="author-post-list">Posts by <?php echo $current_camper->first_name ?></h3>
		<ul class="camper-posts">	
	<?php /* List of posts */	
while (have_posts()) : the_post(); ?>
	<li class="camper-post-meta" id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
	<p><?php the_time(get_option('date_format')); ?></p>
</li>

	<?php endwhile; ?>
	</ul>
	</div>
        
	<?php endif; ?>
	
			</div><!-- #content -->
		</div><!-- #container -->	
 <?php get_sidebar(); ?>       
<?php get_footer(); ?>

