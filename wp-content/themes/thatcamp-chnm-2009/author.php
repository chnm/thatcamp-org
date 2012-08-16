<?php
if(isset($_GET['author_name'])) :
$current_camper = get_userdatabylogin($author_name);
else :
$current_camper = get_userdata(intval($author));
endif;
?>
<?php $privacy = $current_camper->make_profile_private;
if($privacy == "Yes"):
include('404.php'); exit;
else:
?>
<?php get_header(); ?>

<div id="content">
<div id="primary" class="camper five">
<?php 
// Used to generate ID for vcard
$firstname = strtolower($current_camper->first_name);
$lastname = strtolower($current_camper->last_name);

?>
<h1>Campers</h1>

	<div class="vcard" id="hcard-<?php echo $lastname; ?>">
		<h3 class="fn"><?php echo $current_camper->first_name;if(!empty($current_camper->last_name)) echo ' '; echo $current_camper->last_name; ?></h3>
		<ul id="info">
			<li class="title"><?php echo $current_camper->title; ?></li>
			<li class="institution"><?php echo $current_camper->institution; ?></li>
			
			<?php
			// Checks to see if the user_url isn't empty and if its length is greater than 7 (so as not to write http:// only links) 
			if(!empty($current_camper->user_url) && strlen($current_camper->user_url) > 7): ?>
			<li>Website: <a href="<?php echo $current_camper->user_url; ?>" class="url"><?php echo str_replace("http://","",$current_camper->user_url); ?></a></li>
			<?php endif; ?>
			
			<?php if(!empty($current_camper->twitter_username)): ?>
			<li>Twitter: <a href="http://twitter.com/<?php echo $current_camper->twitter_username; ?>/"><?php echo $current_camper->twitter_username; ?></a></li>
			<?php endif; ?>
		</ul>
		<div class="photo" style="float:right; margin-left: 18px;"><?php twittar("", "", "#CCCCCC", "", 1, "R"); ?></div>
		
		<div id="bio"><?php echo nls2p($current_camper->user_description); ?></div>
	</div>
	<?php if(have_posts()): ?>
		<h3>My Posts</h3>
		<div id="blog">		
	<?php while (have_posts()) : the_post(); ?>
	<div class="post">
			<h4 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
			<p><strong><?php the_time('l, F jS, Y') ?> | <a href="http://thatcampdev.info/camper/<?php the_author_login(); ?>"><?php the_author(); ?></a></strong></p>

			<div class="entry">
				<?php the_content(); ?>
			</div>

			<p class="postmetadata"><?php the_tags('Tags: ', ', ', '<br />'); ?> Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>

		</div>

	<?php endwhile; ?>
	</div>
	<?php endif; ?>
</div>
<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
<?php endif; ?>