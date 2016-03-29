<?php

/**
 * The Create Notepad widget
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
	public function form( $instance ) {}
	public function update( $new_instance, $old_instance ) {}

	public function widget( $args, $instance ) {
		if ( ! function_exists( 'get_sitewide_tags_option' ) ) {
			return;
		}

		extract( $args );

		$tags_blog_id = get_sitewide_tags_option( 'tags_blog_id' );

		switch_to_blog( $tags_blog_id );

		$title = 'THATCamp Default Categories';
		$c = ! empty( $instance['count'] ) ? '1' : '0';
		$h = ! empty( $instance['hierarchical'] ) ? '1' : '0';
		$d = ! empty( $instance['dropdown'] ) ? '1' : '0';

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

		$cat_args = array('orderby' => 'name', 'show_count' => $c, 'hierarchical' => $h);

		if ( $d ) {
			$cat_args['show_option_none'] = __('Select Category');
			wp_dropdown_categories(apply_filters('widget_categories_dropdown_args', $cat_args));
?>

<script type='text/javascript'>
/* <![CDATA[ */
	var dropdown = document.getElementById("cat");
	function onCatChange() {
		if ( dropdown.options[dropdown.selectedIndex].value > 0 ) {
			location.href = "<?php echo home_url(); ?>/?cat="+dropdown.options[dropdown.selectedIndex].value;
		}
	}
	dropdown.onchange = onCatChange;
/* ]]> */
</script>

<?php
		} else {
?>
		<ul>
<?php
		$cat_args['title_li'] = '';

		// We need to filter the category links, but only right here
		add_filter( 'term_link', array( $this, 'filter_term_link' ), 10, 3 );

		wp_list_categories(apply_filters('widget_categories_args', $cat_args));

		remove_filter( 'term_link', array( $this, 'filter_term_link' ), 10, 3 );
?>
		</ul>
<?php
		}

		echo $after_widget;

		restore_current_blog();
		echo 'foo';
	}

	function filter_term_link( $term_link, $term, $taxonomy ) {
		$term_link  = trailingslashit( get_blog_option( 1, 'home' ) );
		$term_link .= 'stream/';
		$term_link  = add_query_arg( 'category', $term->slug, $term_link );
		return $term_link;
	}
}
register_widget( 'THATCamp_Network_Posts' );
