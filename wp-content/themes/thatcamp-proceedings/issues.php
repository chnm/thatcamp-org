<?php
/*
Template Name: Issue Archive
*/
?>

<?php get_header(); ?>

<div id="article" class="twelve columns offset-by-two">

<h1><?php the_title(); ?></h1>

<?php
if(is_user_logged_in()) {
    $pages = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => 'table-of-contents.php',
        'sort_column' => 'desc',
        'post_status' => 'publish,private'
    ));
} else {
    $pages = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => 'table-of-contents.php',
        'sort_column' => 'desc'
    ));
}
$i = 0;
?>

<?php foreach($pages as $page):
    $i++;
    $pageId = $page->ID;
    if ($i == 1 || $i % 3 == 1) {
        echo '<div class="issue four columns alpha">';
    } else if ($i % 3 == 0) {
        echo '<div class="issue four columns omega">';
    } else {
        echo '<div class="issue four columns">';
    }
    if ( has_post_thumbnail($pageId) ) {
        echo '<div class="issue-image">';
        echo '<a href="' . get_permalink($pageId) . '">' . get_the_post_thumbnail($pageId) . '</a>';
        echo '</div>';
	}
    echo '<p><a href="' . get_permalink($pageId) . '">' . $page->post_title . '</a></p>';
    echo '</div>';
endforeach; ?>

</div>

<?php get_footer(); ?>