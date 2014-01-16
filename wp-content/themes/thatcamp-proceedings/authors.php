<?php
/*
Template Name: Authors
*/
?>

<?php get_header(); ?>

<div id="article" class="twelve columns offset-by-two">

<h1><?php the_title(); ?></h1>

<?php 
if(function_exists('coauthors_wp_list_authors')) {
    coauthors_wp_list_published('show_fullname=1&optioncount=1');
} else {
    wp_list_authors('show_fullname=1&optioncount=1&orderby=post_count&order=DESC&number=200');
}
?>

</div>

<?php get_footer(); ?>