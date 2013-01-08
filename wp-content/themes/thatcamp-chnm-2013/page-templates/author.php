<?php
/**
 * The template for displaying Author Archive pages.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); ?>

		<section id="primary">
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


<div id="author">

<h1><?php echo $current_camper->first_name;if(!empty($current_camper->last_name)) echo ' '; echo $current_camper->last_name; ?>
			<?php if(empty($current_camper->first_name)): ?>
			<?php echo $current_camper->display_name; ?>
			<?php endif; ?>
</h1>


    <?php if (@$_GET['dev'] == 1) print_r($current_camper); ?>

	<div class="entry author-entry clearfix" id="author-<?php echo $lastname; ?>">

    <?php echo get_avatar( $current_camper->user_email, $size = '150'); ?>&nbsp;&nbsp;	
		<ul id="author-info">
			<?php // User title 
			if ( $title = $current_camper->user_title ): ?>
			<li class="title"><?php echo $current_camper->user_title; ?></li>
			<?php endif; ?>
			
			<?php // User organization 
			if ($institution = $current_camper->user_organization): ?>
			<li class="institution"><?php echo $current_camper->user_organization; ?></li>
			<?php endif; ?>
			
			<?php // User website -- checks to see if the user_url isn't empty and if its length is greater than 7 (so as not to write http:// only links) 			
			if(!empty($current_camper->user_url) && strlen($current_camper->user_url) > 7): ?>
			<li><a href="<?php echo $current_camper->user_url; ?>" class="url"><?php echo str_replace("http://","",$current_camper->user_url); ?></a></li>
			<?php endif; ?>
			
			<?php // User Twitter
			if(!empty($current_camper->user_twitter)): ?>
			<li><a href="http://twitter.com/<?php echo $current_camper->user_twitter; ?>/"><?php echo $current_camper->user_twitter; ?></a></li>
			<?php endif; ?>
		</ul>
		
		<?php echo wpautop($current_camper->user_description, 0); ?>
	</div>
</div>	

	<?php if(have_posts()): ?>
	<div class="author-posts">
	<h3>Posts by <?php echo $current_camper->first_name ?></h3>
	<ul id="author-posts-list">	
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
		</section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>