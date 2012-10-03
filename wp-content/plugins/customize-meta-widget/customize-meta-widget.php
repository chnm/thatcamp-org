<?php
/*
Plugin Name: Customize Meta Widget
Plugin URI: http://jehy.ru/wp-plugins.en.html
Description: Add or remove links from meta widget, especially sometimes annoying link to wordpress.org.
Author: Jehy
Version: 0.2
Min WP Version: 2.6
Max WP Version: 2.9.0
Author URI: http://jehy.en.html

########   PLEASE GO DOWN TO EDIT THE CONTENT OF META WIDGET     #############
*/
function replace_meta_widget()
{
wp_unregister_sidebar_widget ('meta');
$widget_ops = array('classname' => 'widget_meta', 'description' => __( "Log in/out, admin, feed and WordPress links") );
wp_register_sidebar_widget('meta', __('Meta'), 'wp_widget_meta_modified', $widget_ops);
}

add_action('widgets_init','replace_meta_widget');

function wp_widget_meta_modified($args) {
	extract($args);
	$options = get_option('widget_meta');
	$title = empty($options['title']) ? __('Meta') : apply_filters('widget_title', $options['title']);
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title;
#WIDGET BEGINS HERE. PLEASE EDIT AS MUCH AS YOU WANT
?>
			<ul>
			<li><?php wp_loginout(); ?></li>
			<li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php echo attribute_escape(__('Syndicate this site using RSS 2.0')); ?>"><?php _e('Entries <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
			<li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php echo attribute_escape(__('The latest comments to all posts in RSS')); ?>"><?php _e('Comments <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
			<?php wp_meta(); ?>
			</ul>
		<?php
#WIDGET ENDS HERE.
echo $after_widget;

}
?>
