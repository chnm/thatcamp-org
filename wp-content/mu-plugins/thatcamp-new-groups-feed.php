<?php

function thatcamp_new_group_rss_catcher() {
	global $wp_query;

	$feed_template = false;

	if ( bp_is_groups_component() && bp_is_current_action( 'feed' ) ) {
		$feed_template = 'rss-newest-groups.php';
	}

	if ( ! $feed_template ) {
		return;
	}

	$wp_query->is_404 = false;
	status_header( 200 );

	include( __DIR__ . '/includes/' . $feed_template );
	die;

}
add_action( 'template_redirect', 'thatcamp_new_group_rss_catcher', 5 );

function thatcamp_last_group_created() {
	global $wpdb, $bp;

	return $wpdb->get_var( "SELECT date_created FROM {$bp->groups->table_name} ORDER BY date_created DESC LIMIT 1" );
}

function thatcamp_camp_summary() {
	$summary = 'A new THATCamp has been registered at <a href="http://thatcamp.org">thatcamp.org</a>:<br /><br />';

	$summary .= 'Name: ' . bp_get_group_name() . '<br />';

	$permalink = thatcamp_get_camp_permalink();
	$summary .= 'URL: <a href="' .  thatcamp_get_camp_permalink() . '">' . thatcamp_get_camp_permalink() . '</a><br />';

	$location = thatcamp_get_location( bp_get_group_id() );
	if ( $location ) {
		$summary .= 'Location: ' . $location . '<br />';
	}

	$date = thatcamp_get_camp_date_pretty( bp_get_group_id() );
	if ( $date ) {
		$summary .= 'Date: ' . $date . '<br />';
	}

	echo $summary;
}
