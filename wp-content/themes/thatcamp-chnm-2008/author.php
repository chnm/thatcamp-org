<?php get_header(); ?>

<?php
if(isset($_GET['author_name'])) :
$current_user = get_userdatabylogin($author_name);
else :
$current_user = get_userdata(intval($author));
endif;
?>

<div id="content" class="staff">
<?php 
// Used to generate ID for vcard
$firstname = strtolower($current_user->first_name);
$lastname = strtolower($current_user->last_name);

// Gets values from DB with the Cimy Users Extra Fields plugin.
$title = get_cimyFieldValue($current_user->ID, 'TITLE');
$picture = get_cimyFieldValue($current_user->ID, 'IMAGE');
$bio = get_cimyFieldValue($current_user->ID, 'BIO');
$institution = get_cimyFieldValue($current_user->ID, 'INSTITUTION');
$twitter = get_cimyFieldValue($current_user->ID, 'TWITTER');
$twitter_name = str_replace("http://twitter.com/","",$twitter);
$twitter_url = 'http://twitter.com/'.$twitter_name;
?>

	<div class="vcard" id="hcard-<?php echo $lastname; ?>">
		<h2 class="fn"><?php echo $current_user->first_name;echo ' '; echo $current_user->last_name; ?></h2>
		<img id="photo" src="<?php echo $picture; ?>" class="photo" />
		<ul id="info">
			<li class="title"><?php echo $title; ?></li>
			<li class="institution"><?php echo $institution; ?></li>
			
			<?php
			// Checks to see if the user_url isn't empty and if its length is greater than 7 (so as not to write http:// only links) 
			if(!empty($current_user->user_url) && strlen($current_user->user_url) > 7): ?>
			<li>Website: <a href="<?php echo $current_user->user_url; ?>" class="url"><?php echo str_replace("http://","",$current_user->user_url); ?></a></li>
			<?php endif; ?>
			<?php if(!empty($twitter)): ?>
			
			<li>Twitter: <a href="<?php echo $twitter_url; ?>"><?php echo $twitter_name; ?></a></li>
			<?php endif; ?>
			<li>Email: <a href="mailto:<?php echo $current_user->user_email; ?>"><?php echo $current_user->user_email; ?></a></li>
		</ul>
		<div id="bio"><?php echo nls2p($bio); ?></div>
	</div>
</div>


<?php get_footer(); ?>