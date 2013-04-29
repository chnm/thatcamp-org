<?php
/**
 * People
 *
 * @package thatcampbase
 * @since thatcampbase 1.0
 *
 * Template Name: People
 */
?>
<?php get_header(); ?>
<div id="primary" class="main-content">
	<div id="content">
		<div id="people-view" role="main">
                <header class="post-header">
                    <h1 class="post-title"><?php the_title(); ?></h1>
                </header>

		<?php
$display_admins = true;
$order_by = 'display_name'; // 'nicename', 'email', 'url', 'registered', 'display_name', or 'post_count'
$role = ''; // 'subscriber', 'contributor', 'editor', 'author' - leave blank for 'all'
$avatar_size = 64;
$hide_empty = false; // hides authors with zero posts

if(!empty($display_admins)) {
    $blogusers = get_users('orderby='.$order_by.'&role='.$role);
} else {
    $admins = get_users('role=administrator');
    $exclude = array();
    foreach($admins as $ad) {
        $exclude[] = $ad->ID;
    }
    $exclude = implode(',', $exclude);
    $blogusers = get_users('exclude='.$exclude.'&orderby='.$order_by.'&role='.$role);
}
$authors = array();
foreach ($blogusers as $bloguser) {
    $user = get_userdata($bloguser->ID);
    if(!empty($hide_empty)) {
        $numposts = count_user_posts($user->ID);
        if($numposts < 1) continue;
    }
    $authors[] = (array) $user;
}

echo '<ul class="contributors">';
foreach($authors as $author) {
    $display_name = $author['data']->display_name;
    $avatar = get_avatar($author['ID'], $avatar_size);
    $author_profile_url = get_author_posts_url($author['ID']);

    echo '<li><a href="', $author_profile_url, '">', $avatar , '</a><a href="', $author_profile_url, '" class="contributor-link">', $display_name, '</a></li>';
}
echo '</ul>';
?>
		</div>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer() ?>