<?php
/*
Template Name: Campers
*/
?>

<?php get_header(); ?>
<div id="content">
    <h1>Campers</h1>
    <div id="campers">
<?php 

$campers = get_users_of_blog();
foreach($campers as $key => $camper):

    // We'll skip over some users with these IDs
    if ($camper->ID == 111 || $camper->ID == 112 || $camper->ID == 113) continue;
    
    $camper_info = get_userdata($camper->ID);
    
    /*
    If make_profile_private is set to Yes, we'll skip the user. We should probably
    do this directly in the query, and only return users who do not set this field
    to Yes. But we're lazy for now.
    */
    // if($camper_info->make_profile_private == 'Yes') continue;

    $lastname = strtolower($camper_info->last_name);
?>
    <div class="vcard camper" id="hcard-<?php echo $lastname ?>">

        <div class="camper_info">	 			
		<div class="camper_avatar"><a href="<?php echo get_author_posts_url($usr->ID); ?>"><?php echo get_avatar($usr->ID, 100); ?></a></div>
		<div class="camper_name"><a href="<?php echo get_author_posts_url($usr->ID); ?>"><?php echo thatcamp_display_user_name($camper_info,false); ?></a></div>
		<div class="camper_posts"><a href="<?php echo get_author_posts_url($usr->ID); ?>">Posts (<?php echo get_usernumposts($usr->ID); ?>)</a></div>
	</div>	
    </div>
<?php endforeach;?>
</div>

<?php get_footer(); ?>
