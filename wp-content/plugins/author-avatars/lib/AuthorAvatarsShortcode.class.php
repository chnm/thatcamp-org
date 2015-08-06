<?php

/**
 * Author Avatars Shortcode: provides a shortcode for displaying avatars of blog users
 */
class AuthorAvatarsShortcode {

	/**
	 * Constructor
	 */
	function AuthorAvatarsShortcode() {
		$this->register();
	}

	/**
	 * register shortcode
	 */
	function register() {
		add_shortcode( 'authoravatars', array( &$this, 'shortcode_handler' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_resources' ), 20 );
	}

	/**
	 * Add css stylesheets (using wp_enqueue_style()).
	 */
	function enqueue_resources() {
		wp_enqueue_style( 'author-avatars-shortcode' );
	}

	/**
	 * The shortcode handler for the [authoravatars] shortcode.
	 */
	function shortcode_handler( $atts, $content = null ) {
		require_once( 'UserList.class.php' );
		$this->userlist = new UserList();
		$settings       = AA_settings();


		// roles
		$roles = array(); // default value: no restriction -> all users
		if ( ! empty( $atts['roles'] ) ) {
			if ( ! is_array( $atts['roles'] ) ) {
				$roles = explode( ',', $atts['roles'] );
			} else {
				$roles = $atts['roles'];
			}
			$roles = array_map( 'trim', $roles );
		}
		$this->userlist->roles = $roles;

		// blogs
		$blogs = array(); // default value: empty -> only current blog
		if ( $settings->blog_selection_allowed() && ! empty( $atts['blogs'] ) ) {
			if ( strtolower( $atts['blogs'] ) == 'all' ) {
				$blogs = array( - 1 );
			} else {
				if ( ! is_array( $atts['blogs'] ) ) {
					$blogs = explode( ',', $atts['blogs'] );
				} else {
					$blogs = array_map( 'trim', $blogs );
				}
				$blogs = array_map( 'intval', $blogs );
			}
		}
		$this->userlist->blogs = $blogs;

		// grouping
		$group_by = '';
		if ( isset( $atts['group_by'] ) ) {
			if ( AA_is_wpmu() && $atts['group_by'] == 'blog' ) {
				$group_by = 'blog';
			}
		}
		$this->userlist->group_by = $group_by;

		// hidden users
		$hiddenusers = array(); // default value: no restriction -> all users
		if ( ! empty( $atts['hiddenusers'] ) ) {
			if ( ! is_array( $atts['hiddenusers'] ) ) {
				$hiddenusers = explode( ',', $atts['hiddenusers'] );
			}
		}
		$this->userlist->hiddenusers = array_map( 'trim', $hiddenusers );

		// just these users
		$onlyusers = array(); // default value: no restriction -> all users
		if ( ! empty( $atts['onlyusers'] ) ) {
			if ( ! is_array( $atts['onlyusers'] ) ) {
				$onlyusers = explode( ',', $atts['onlyusers'] );
			}
		}
		$this->userlist->onlyusers = array_map( 'trim', $onlyusers );

		// link to author page? (deprecated)
		if ( isset( $atts['link_to_authorpage'] ) && ( strlen( $atts['link_to_authorpage'] ) > 0 ) ) {
			// by default always true, has to be set explicitly to not link the users
			$set_to_false = ( $atts['link_to_authorpage'] == 'false' || (bool) $atts['link_to_authorpage'] == false );
			if ( $set_to_false ) {
				$this->userlist->user_link = false;
			}
		}

		if ( ! empty( $atts['user_link'] ) ) {
			$this->userlist->user_link = $atts['user_link'];
		}

		$display = array();
		if ( ! empty( $atts['display'] ) ) {
			if ( ! is_array( $atts['display'] ) ) {
				$display = explode( ',', $atts['display'] );
			}else{
				$display = $atts['display'];
			}
		}

		$display = apply_filters('aa_shortcode_display_list', $display );

		// support for all style shortcode
		$default_display_options = array('show_name','show_postcount','show_email','show_biography','show_last_post','show_bbpress_post_count');
		// loop the old name=true settings and add them to the new array format
		foreach( $default_display_options as $default_display_option ){
			if ( isset( $atts[$default_display_option] ) && ( strlen( $atts[$default_display_option] ) > 0 ) ) {
				if( true == $atts[$default_display_option] && !in_array( $default_display_option, $display ) ){
					$display[] = $default_display_option;
				}
			}

		}
		// the defaults array and set the globals if found
		foreach( $default_display_options as $default_display_option ){
			if ( in_array( $default_display_option, $display ) ) {
				$this->userlist->$default_display_option = true;
			} else {
				$this->userlist->$default_display_option = false;
			}
		}

		$this->userlist->display_extra = array_diff($display, $default_display_options );

		//var_dump($this->userlist->display_extra);


		// avatar size
		if ( ! empty( $atts['avatar_size'] ) ) {
			$size = intval( $atts['avatar_size'] );
			if ( $size > 0 ) {
				$this->userlist->avatar_size = $size;
			}
		}

		// max. number of avatars
		if ( ! empty( $atts['limit'] ) ) {
			$limit = intval( $atts['limit'] );
			if ( $limit > 0 ) {
				$this->userlist->limit = $limit;
			}
		}

		// max. number of avatars
		$this->userlist->bio_length = -1;
		if ( ! empty( $atts['max_bio_length'] ) ) {
			$bio_length = intval( $atts['max_bio_length'] );
			if (  0 < $bio_length ) {
				$this->userlist->bio_length = $bio_length;
			}
		}

		// min. number of posts
		if ( ! empty( $atts['min_post_count'] ) ) {
			$min_post_count = intval( $atts['min_post_count'] );
			if ( 0 < $min_post_count ) {
				$this->userlist->min_post_count = $min_post_count;
			}
		}
		// get page size
		if ( ! empty( $atts['page_size'] ) ) {
			$page_size = intval( $atts['page_size'] );
			if ( 0 < $page_size ) {
				$this->userlist->page_size = $page_size;
			}
		}

		// get page size
		if ( ! empty( $atts['aa_page'] ) ) {

			$page_size = intval( $atts['aa_page'] );
			if ( 0 < $page_size ) {
				$this->userlist->aa_page = $page_size;
			}
		} elseif ( isSet( $_REQUEST['aa_page'] ) && is_numeric( $_REQUEST['aa_page'] ) ) {
			$page_size = intval( $_REQUEST['aa_page'] );
			if ( 0 < $page_size ) {
				$this->userlist->aa_page = $page_size;
			}
		}

		// display order
		$sort_direction = 'asc';
		if ( ! empty( $atts['order'] ) ) {
			$order = $atts['order'];
			if ( strpos( $order, ',' ) !== false ) {
				list( $order, $sort_direction ) = explode( ',', $order, 2 );
			}
			$this->userlist->order = $order;
		}
		if ( ! empty( $atts['sort_direction'] ) ) {
			$sort_direction = $atts['sort_direction'];
		}
		$valid_directions = array( 'asc', 'ascending', 'desc', 'descending' );
		if ( in_array( $sort_direction, $valid_directions ) ) {
			$this->userlist->sort_direction = $sort_direction;
		}

		// render as a list?
		if ( isset( $atts['render_as_list'] ) ) {
			$set_to_false = ( $atts['render_as_list'] == 'false' );
			if ( ! $set_to_false ) {
				$this->userlist->use_list_template();
			}
		}

		return '<div class="shortcode-author-avatars">' . $this->userlist->get_output() . $content . $this->userlist->pagingHTML . '</div>';
	}
}