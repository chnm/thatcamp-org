<?php
/*
Template Name: Campers
*/
?>

<?php get_header(); ?>
<div id="content" class="campers">
	<h1>Campers</h1>
<?php 
$campers = cw_get_authors();
foreach($campers as $key => $camper):

	$camper_info = get_userdata($camper->ID);
	
	if($camper_info->make_profile_private == 'Yes') continue;
	// print_r($camper_info);
	// Used to generate ID for vcard
	$firstname = strtolower($camper_info->first_name);
	$lastname = strtolower($camper_info->last_name);
?>
	<div class="vcard camper" id="hcard-<?php echo $lastname ?>">
		<h2 class="fn"><a href="<?php echo get_author_posts_url($camper_info->ID, $camper_info->user_nicename) ?>"><?php echo get_avatar($camper_info->user_email); ?>
		<?php if(!empty($camper_info->last_name)): echo $camper_info->first_name; if(!empty($camper_info->last_name)) echo ' '; echo $camper_info->last_name; else: echo $camper_info->display_name; endif;?></a></h2>
		
	</div>
<?php endforeach;?>
</div>

<?php get_footer(); ?>