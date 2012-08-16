<?php

function kreative_wp_foot()
{
	$kt =& get_instance();
	echo $kt->config->item('analytics', 'general');
}

add_action('wp_footer', 'kreative_wp_foot');