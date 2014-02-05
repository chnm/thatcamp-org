<?php

/**
 * The THATCamp Network Posts widget
 */
class THATCamp_Network_Posts extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'thatcamp_network_posts',
			'THATCamp Post Browser',
			array(
				'description' => 'A browser for posts with default categories from across the THATCamp network.',
			)
		);
	}

	/**
	 * Don't think we need any options
	 */
	public function form() {}
	public function update() {}

	public function widget( $args, $instance ) {
		if ( ! function_exists( 'get_sitewide_tags_option' ) ) {
			return;
		}

		extract( $args );

		$tags_blog_id = get_sitewide_tags_option( 'tags_blog_id' );

		switch_to_blog( $tags_blog_id );

		$title = 'Default Categories';
		$c = ! empty( $instance['count'] ) ? '1' : '0';
		$h = ! empty( $instance['hierarchical'] ) ? '1' : '0';
		$d = ! empty( $instance['dropdown'] ) ? '1' : '0';

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

		$cat_args = array('orderby' => 'name', 'show_count' => $c, 'hierarchical' => $h);

		echo '<ul>';

		$cat_args['title_li'] = '';

		// We need to filter the category links, but only right here
		add_filter( 'term_link', array( $this, 'filter_term_link' ), 10, 3 );
		add_filter( 'get_terms', array( $this, 'filter_get_terms' ), 10, 3 );

		wp_list_categories(apply_filters('widget_categories_args', $cat_args));

		remove_filter( 'term_link', array( $this, 'filter_term_link' ), 10, 3 );
		remove_filter( 'get_terms', array( $this, 'filter_get_terms' ), 10, 3 );

		echo '</ul>';

		echo $after_widget;

		restore_current_blog();
	}

	function filter_term_link( $term_link, $term, $taxonomy ) {
                global $wpdb;
                $proceedings_blog_id = $wpdb->get_var( "SELECT blog_id FROM $wpdb->blogs WHERE domain LIKE 'proceedings%'" );
		$term_link  = trailingslashit( get_blog_option( $proceedings_blog_id, 'home' ) );
		$term_link .= 'all/';
		$term_link  = add_query_arg( 'category', $term->slug, $term_link );
		return $term_link;
	}

	/**
	 * Filter against the cat whitelist
	 */
	function filter_get_terms( $terms, $taxonomies, $args ) {
		$cat_whitelist = array(
			'Administrative',
			'Archives',
			'Blogging',
			'Coding',
			'Collaboration',
			'Copyright',
			'Crowdsourcing',
			'Data Mining',
			'Digital Literacy',
			'Diversity',
			'Funding',
			'Games',
			'General',
			'Jobs',
			'Libraries',
			'Licensing',
			'Linked Data',
			'Mapping',
			'Metadata',
			'Mobile',
			'MOOCs',
			'Museums',
			'Notepads',
			'Open Access',
			'Project Management',
			'Publishing',
			'Research Methods',
			'Scholarly Editions',
			'Search',
			'Session Notes',
			'Session Proposals',
			'Session: Make',
			'Session: Play',
			'Session: Talk',
			'Session: Teach',
			'Social Media',
			'Teaching',
			'Tenure and Promotion',
			'Text Mining',
			'Uncategorized',
			'Visualization',
			'Workshops',
			'Your Categories Are Inadequate',
		);

		foreach ( $terms as $tkey => $t ) {
			if ( ! in_array( $t->name, $cat_whitelist ) ) {
				unset( $terms[ $tkey ] );
			}
		}

		return array_values( $terms );
	}
}
register_widget( 'THATCamp_Network_Posts' );

/**
 * The THATCamp Network Posts widget
 */
class THATCamp_Network_Search extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'thatcamp_network_search',
			'THATCamp Network Search',
			array(
				'description' => 'Search the entire THATCamp network.',
			)
		);
	}

	/**
	 * Don't think we need any options
	 */
	public function form() {}
	public function update() {}

	public function widget( $args, $instance ) {
		$potc_url = $this->proceedings_link();

		extract( $args );

		switch_to_blog( $tags_blog_id );

		$title = 'Search';
		$value = ! empty( $_GET['tcs'] ) ? urldecode( $_GET['tcs'] ) : '';

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

		?>

		<form method="get" action="<?php echo $potc_url . 'all/' ?>">
			<input id="thatcamp_network_search" name="tcs" value="<?php echo esc_attr( $value ) ?>" />
		</form>

		<?php
		echo $after_widget;

	}

	function proceedings_link() {
                global $wpdb;
                $proceedings_blog_id = $wpdb->get_var( "SELECT blog_id FROM $wpdb->blogs WHERE domain LIKE 'proceedings%'" );
		$term_link  = trailingslashit( get_blog_option( $proceedings_blog_id, 'home' ) );
		return $term_link;
	}
}
register_widget( 'THATCamp_Network_Search' );
