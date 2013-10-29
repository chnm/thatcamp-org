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
		add_shortcode('authoravatars', array(&$this, 'shortcode_handler'));
		add_action('wp_enqueue_scripts', array( &$this, 'enqueue_resources' ), 20);
	}
	
	/**
	 * Add css stylesheets (using wp_enqueue_style()).
	 */
	function enqueue_resources() {
		wp_enqueue_style('author-avatars-shortcode');
	}
	
	/**
	 * The shortcode handler for the [authoravatars] shortcode.
	 */
	function shortcode_handler($atts, $content=null) {
		require_once('UserList.class.php');
		$this->userlist = new UserList();
		$settings = AA_settings();

		// roles
		$roles = array(); // default value: no restriction -> all users
		if (!empty($atts['roles'])) {
			if (!is_array( $atts['roles'])){
				$roles = explode(',', $atts['roles']);
			}
			$roles = array_map('trim', $roles);
		}
		$this->userlist->roles = $roles;
		
		// blogs
		$blogs = array(); // default value: empty -> only current blog
		if ($settings->blog_selection_allowed() && !empty($atts['blogs'])) {
			if (strtolower($atts['blogs']) == 'all') {
				$blogs = array(-1);
			}
			else {
				if (!is_array( $atts['blogs'])){
					$blogs = explode(',', $atts['blogs']);
				}
				$blogs = array_map('intval', $blogs);
			}
		}
		$this->userlist->blogs = $blogs;
		
		// grouping
		$group_by = '';
		if (isset($atts['group_by'])) {
			if (AA_is_wpmu() && $atts['group_by'] == 'blog') $group_by = 'blog';
		}
		$this->userlist->group_by = $group_by;
		
		// hidden users 
		$hiddenusers = array(); // default value: no restriction -> all users
		if (!empty($atts['hiddenusers'])) {
			if (!is_array( $atts['hiddenusers'])){
				$hiddenusers = explode(',', $atts['hiddenusers']);
			}
			$hiddenusers = array_map('trim', $hiddenusers);
		}
		$this->userlist->hiddenusers = $hiddenusers;
		
		// link to author page? (deprecated)
		if (isset($atts['link_to_authorpage'])&& ( strlen($atts['link_to_authorpage'])>0) ) {
			// by default always true, has to be set explicitly to not link the users
			$set_to_false = ($atts['link_to_authorpage'] == 'false' || (bool) $atts['link_to_authorpage'] == false);
			if ($set_to_false) $this->userlist->user_link = false;
		}
		
		if (!empty($atts['user_link'])) {
			$this->userlist->user_link = $atts['user_link'];
		}
		
		// show author name?
		if (isset($atts['show_name'])&& ( strlen($atts['show_name'])>0) ) {
			$set_to_false = ($atts['show_name'] == 'false');
			if ($set_to_false) $this->userlist->show_name = false;
			else $this->userlist->show_name = true;
		}

		// show post count?
		if (isset($atts['show_postcount'])&& ( strlen($atts['show_postcount'])>0) ) {
			$set_to_false = ($atts['show_postcount'] == 'false');
			if ($set_to_false) $this->userlist->show_postcount = false;
			else $this->userlist->show_postcount = true;
		}

		// show email?
		if (isset($atts['show_email'])&& ( strlen($atts['show_email'])>0) ) {
			$set_to_false = ($atts['show_email'] == 'false');
			if ($set_to_false) $this->userlist->show_email = false;
			else $this->userlist->show_email = true;
		}

		// show biography?
		if (isset($atts['show_biography'])&& ( strlen($atts['show_biography'])>0) ) {
			$set_to_false = ($atts['show_biography'] == 'false');
			if ($set_to_false) $this->userlist->show_biography = false;
			else $this->userlist->show_biography = true;
		}

		// show show_bbpress_post_count?
		if (isset($atts['show_bbpress_post_count'])&& ( strlen($atts['show_bbpress_post_count'])>0) ) {
			$set_to_false = ($atts['show_bbpress_post_count'] == 'false');
			if ($set_to_false) $this->userlist->show_bbpress_post_count = false;
			else $this->userlist->show_bbpress_post_count = true;
		}		

		// avatar size
		if (!empty($atts['avatar_size'])) {
			$size = intval($atts['avatar_size']);
			if ($size > 0) $this->userlist->avatar_size = $size;
		}
		
		// max. number of avatars
		if (!empty($atts['limit'])) {
			$limit = intval($atts['limit']);
			if ($limit > 0) $this->userlist->limit = $limit;
		}
                
		// min. number of posts
		if (!empty($atts['min_post_count'])) {
			$min_post_count = intval($atts['min_post_count']);
			if ($min_post_count > 0) $this->userlist->min_post_count = $min_post_count;
		}
		// get page size
		if (!empty($atts['page_size'])) {
			$page_size = intval($atts['page_size']);
			if ($page_size > 0) $this->userlist->page_size = $page_size;
		}
		
		// get page size
		if (!empty($atts['aa_page'])) {
			
			$page_size = intval($atts['aa_page']);
			if ($page_size > 0) $this->userlist->aa_page = $page_size;
		}elseif (isSet($_REQUEST['aa_page']) && is_numeric ($_REQUEST['aa_page'])){
			$page_size = intval($_REQUEST['aa_page']);
			if ($page_size > 0) $this->userlist->aa_page = $page_size;
		}
		
		// display order
		$sort_direction = 'asc';
		if (!empty($atts['order'])) {
			$order = $atts['order'];
			if (strpos($order, ',') !== false) {
				list($order, $sort_direction) = explode(',', $order, 2);
			}
			$this->userlist->order = $order;
		}
		if (!empty($atts['sort_direction'])) {
			$sort_direction = $atts['sort_direction'];
		}
		$valid_directions = array('asc', 'ascending', 'desc', 'descending');
		if (in_array($sort_direction, $valid_directions)) {
			$this->userlist->sort_direction = $sort_direction;
		}
				
		// render as a list?
		if (isset($atts['render_as_list'])) {
			$set_to_false = ($atts['render_as_list'] == 'false');
			if (!$set_to_false) $this->userlist->use_list_template();
		}
		
		return '<div class="shortcode-author-avatars">'.$this->userlist->get_output().$content.$this->userlist->pagingHTML.'</div>' ;
	}
}

?>