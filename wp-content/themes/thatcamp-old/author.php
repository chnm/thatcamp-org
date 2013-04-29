<?php
if(isset($_GET['author_name'])) :
$current_camper = get_userdatabylogin($author_name);
else :
$current_camper = get_userdata(intval($author));
endif;
?>
<?php 

// Check privacy setting. If set to Yes, include the 404 template and abort.
$privacy = $current_camper->make_profile_private;
if($privacy == "Yes"):
include('404.php'); exit;

// Else, stick with the plan.
else:
?>
<?php get_header(); ?>

<div id="content">
<div id="primary" class="seven">
<?php 
// Used to generate ID for vcard
$firstname = strtolower($current_camper->first_name);
$lastname = strtolower($current_camper->last_name);

?>
<h1><?php echo $current_camper->first_name;if(!empty($current_camper->last_name)) echo ' '; echo $current_camper->last_name; ?></h1>

    <?php if (@$_GET['dev'] == 1) print_r($current_camper); ?>

	<div class="vcard" id="hcard-<?php echo $lastname; ?>">

    <?php echo get_avatar( $current_camper->user_email, $size = '96'); ?>&nbsp;&nbsp;	
		<ul id="info">
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
	<?php if(have_posts()): ?>
		<h3>Posts by <?php echo $current_camper->first_name ?></h3>
		<ul id="blog">	
	<?php while (have_posts()) : the_post(); ?>
	<li class="post"id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>

	<?php endwhile; ?>
	</ul>
	<?php endif; ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
<?php endif; ?>