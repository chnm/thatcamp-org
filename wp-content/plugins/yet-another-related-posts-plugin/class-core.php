<?php

// new in 3.4: put everything YARPP into an object, expected to be a singleton global $yarpp
class YARPP {

	public $debug = false;
	
	public $cache;
	public $cache_bypass;
	private $active_cache;
	
	public $admin;
	private $storage_class;
	
	public $myisam = true;
	
	// here's a list of all the options YARPP uses (except version), as well as their default values, sans the yarpp_ prefix, split up into binary options and value options. These arrays are used in updating settings (options.php) and other tasks.
	public $default_options = array();

	function __construct() {
		$this->load_default_options();

		// register text domain
		load_plugin_textdomain( 'yarpp', false, dirname(plugin_basename(__FILE__)) . '/lang' );

		// load cache object
		require_once(YARPP_DIR . '/class-cache.php');
		require_once(YARPP_DIR . '/cache-' . YARPP_CACHE_TYPE . '.php');
		$this->storage_class = $yarpp_storage_class;
		$this->cache = new $this->storage_class( $this );
		$this->cache_bypass = new YARPP_Cache_Bypass( $this );

		register_activation_hook( __FILE__, array($this, 'activate') );
		
		// update cache on save
		add_action( 'save_post', array($this->cache, 'save_post') );
		// new in 3.2: update cache on delete
		add_action( 'delete_post', array($this->cache, 'delete_post') );
		// new in 3.2.1: handle post_status transitions
		add_action( 'transition_post_status', array($this->cache, 'transition_post_status'), 10, 3);

		// automatic display hooks:
		add_filter( 'the_content', array( $this, 'the_content' ), 1200 );
		add_filter( 'the_content_rss', array( $this, 'the_content_rss' ), 600 );
		add_filter( 'the_excerpt_rss', array( $this, 'the_excerpt_rss' ), 600 );

		if ( isset($_REQUEST['yarpp_debug']) )
			$this->debug = true;

		// new in 3.4: only load UI if we're in the admin
		if ( is_admin() ) {
			require_once(YARPP_DIR . '/class-admin.php');
			$this->admin = new YARPP_Admin( $this );
		}
	}
		
	/*
	 * OPTIONS
	 */
	
	private function load_default_options() {
		$this->default_options = array(
			'threshold' => 5,
			'limit' => 5,
			'excerpt_length' => 10,
			'recent' => false, // new in 3.5
			'before_title' => '<li>',
			'after_title' => '</li>',
			'before_post' => ' <small>',
			'after_post' => '</small>',
			'before_related' => '<p>'.__('Related posts:','yarpp').'</p><ol>',
			'after_related' => '</ol>',
			'no_results' => '<p>'.__('No related posts.','yarpp').'</p>',
			'order' => 'score DESC',
			'rss_limit' => 3,
			'rss_excerpt_length' => 10,
			'rss_before_title' => '<li>',
			'rss_after_title' => '</li>',
			'rss_before_post' => ' <small>',
			'rss_after_post' => '</small>',
			'rss_before_related' => '<p>'.__('Related posts:','yarpp').'</p><ol>',
			'rss_after_related' => '</ol>',
			'rss_no_results' => '<p>'.__('No related posts.','yarpp').'</p>',
			'rss_order' => 'score DESC',
			'past_only' => true,
			'show_excerpt' => false,
			'rss_show_excerpt' => false,
			'template' => false, // new in 3.5
			'rss_template' => false, // new in 3.5
			'show_pass_post' => false,
			'cross_relate' => false,
			'auto_display' => true,
			'rss_display' => false, // changed default in 3.1.7
			'rss_excerpt_display' => true,
			'promote_yarpp' => false,
			'rss_promote_yarpp' => false,
			'myisam_override' => false,
			'exclude' => '', // conslidated YARPP 3.4 and further in 3.5
			'weight' => array( // consolidated in YARPP 3.4, format changed in 3.5
				'title' => 1,
				'body' => 1,
				'tax' => array(
					'category' => 1, // changed default in 3.4
					'post_tag' => 1
				)
			),
			'require_tax' => array() // new in 3.5
		);
	}
	
	function set_option( $options, $value = null ) {
		$current_options = $this->get_option();
	
		// we can call yarpp_set_option(key,value) if we like:
		if ( !is_array($options) ) {
			if ( isset($value) )
				$options = array( $options => $value );
			else
				return false;
		}
	
		$new_options = array_merge( $current_options, $options );
	
		// new in 3.1: clear cache when updating certain settings.
		$clear_cache_options = array( 'show_pass_post', 'recent', 'threshold' );
		$new_options_which_require_flush = array_intersect( array_keys( array_diff_assoc($options, $current_options) ), $clear_cache_options );
		if ( count($new_options_which_require_flush) ||
			( $new_options['limit'] > $current_options['limit'] ) ||
			( $new_options['weight'] != $current_options['weight'] ) ||
			( $new_options['exclude'] != $current_options['exclude'] ) ||
			( $new_options['require_tax'] != $current_options['require_tax'] ) )
			$this->cache->flush();
	
		update_option( 'yarpp', $new_options );
	}
	
	// 3.4b8: $option can be a path, of the query_str variety, i.e. "option[suboption][subsuboption]"
	function get_option( $option = null ) {
		$options = (array) get_option( 'yarpp', array() );
		// ensure defaults if not set:
		$options = array_merge( $this->default_options, $options );

		if ( is_null( $option ) )
			return $options;
	
		$optionpath = array();
		$parsed_option = array();
		wp_parse_str($option, $parsed_option);
		$optionpath = $this->array_flatten($parsed_option);
		
		$current = $options;
		foreach ( $optionpath as $optionpart ) {
			if ( !is_array($current) || !isset($current[$optionpart]) )
				return null;
			$current = $current[$optionpart];
		}
		return $current;
	}
	
	private function array_flatten($array, $given = array()) {
		foreach ($array as $key => $val) {
			$given[] = $key;
			if ( is_array($val) )
				$given = $this->array_flatten($val, $given);
		}
		return $given;
	}


	/*
	 * INFRASTRUCTURE
	 */

	function enabled() {
		global $wpdb;
		if ( $this->cache->is_enabled() === false )
			return false;
		$indexdata = $wpdb->get_results("show index from $wpdb->posts");
		foreach ($indexdata as $index) {
			if ($index->Key_name == 'yarpp_title')
				return true;
		}
		return false;
	}
	
	function activate() {
		global $wpdb;
	
		$wpdb->get_results("show index from $wpdb->posts where Key_name='yarpp_title'");
		if (!$wpdb->num_rows)
			$wpdb->query("ALTER TABLE $wpdb->posts ADD FULLTEXT `yarpp_title` ( `post_title` )");
	
		$wpdb->get_results("show index from $wpdb->posts where Key_name='yarpp_content'");
		if (!$wpdb->num_rows)
			$wpdb->query("ALTER TABLE $wpdb->posts ADD FULLTEXT `yarpp_content` ( `post_content` )");
		
		if ( !$this->enabled() ) {
			// If we are still not enabled, run the cache abstraction's setup method.
			$this->cache->setup();
			// If we're still not enabled, give up.
			if ( !$this->enabled() )
				return 0;
		}
		
		if ( !get_option('yarpp_version') ) {
			add_option( 'yarpp_version', YARPP_VERSION );
			$this->version_info(true);
		} else {
			$this->upgrade_check();
		}
	
		return 1;
	}
	
	function myisam_check() {
		global $wpdb;
		$tables = $wpdb->get_results("show table status like '{$wpdb->posts}'");
		foreach ($tables as $table) {
			if ($table->Engine == 'MyISAM') return true;
			else return $table->Engine;
		}
		return 'UNKNOWN';
	}
	
	function upgrade_check() {
		$last_version = get_option( 'yarpp_version' );
		if (version_compare(YARPP_VERSION, $last_version) === 0)
			return;
	
		if ( $last_version && version_compare('3.4b2', $last_version) > 0 )
			$this->upgrade_3_4b2();
		if ( $last_version && version_compare('3.4b5', $last_version) > 0 )
			$this->upgrade_3_4b5();
		if ( $last_version && version_compare('3.4b8', $last_version) > 0 )
			$this->upgrade_3_4b8();
		if ( $last_version && version_compare('3.4.4b2', $last_version) > 0 )
			$this->upgrade_3_4_4b2();
		if ( $last_version && version_compare('3.4.4b3', $last_version) > 0 )
			$this->upgrade_3_4_4b3();
		if ( $last_version && version_compare('3.4.4b4', $last_version) > 0 )
			$this->upgrade_3_4_4b4();
			
		$this->cache->upgrade($last_version);
		// flush cache in 3.4.1b5 as 3.4 messed up calculations.
		if ( $last_version && version_compare('3.4.1b5', $last_version) > 0 )
			$this->cache->flush();
	
		$this->version_info(true);
	
		update_option('yarpp_version',YARPP_VERSION);
	}
	
	function upgrade_3_4b2() {
		global $wpdb;
	
		$yarpp_3_3_options = array(
			'threshold' => 5,
			'limit' => 5,
			'template_file' => '', // new in 2.2
			'excerpt_length' => 10,
			'recent_number' => 12,
			'recent_units' => 'month',
			'before_title' => '<li>',
			'after_title' => '</li>',
			'before_post' => ' <small>',
			'after_post' => '</small>',
			'before_related' => '<p>'.__('Related posts:','yarpp').'</p><ol>',
			'after_related' => '</ol>',
			'no_results' => '<p>'.__('No related posts.','yarpp').'</p>',
			'order' => 'score DESC',
			'rss_limit' => 3,
			'rss_template_file' => '', // new in 2.2
			'rss_excerpt_length' => 10,
			'rss_before_title' => '<li>',
			'rss_after_title' => '</li>',
			'rss_before_post' => ' <small>',
			'rss_after_post' => '</small>',
			'rss_before_related' => '<p>'.__('Related posts:','yarpp').'</p><ol>',
			'rss_after_related' => '</ol>',
			'rss_no_results' => '<p>'.__('No related posts.','yarpp').'</p>',
			'rss_order' => 'score DESC',
			'title' => '2',
			'body' => '2',
			'categories' => '1', // changed default in 3.3
			'tags' => '2',
			'distags' => '',
			'discats' => '',
			'past_only' => true,
			'show_excerpt' => false,
			'recent_only' => false, // new in 3.0
			'use_template' => false, // new in 2.2
			'rss_show_excerpt' => false,
			'rss_use_template' => false, // new in 2.2
			'show_pass_post' => false,
			'cross_relate' => false,
			'auto_display' => true,
			'rss_display' => false, // changed default in 3.1.7
			'rss_excerpt_display' => true,
			'promote_yarpp' => false,
			'rss_promote_yarpp' => false);
	
		$yarpp_options = array();
		foreach ( $yarpp_3_3_options as $key => $default ) {
			$value = get_option( "yarpp_$key", null );
			if ( is_null($value) )
				continue;

			if ( is_bool($default) ) {
				$yarpp_options[$key] = (boolean) $value;
				continue;
			}

			// value options used to be stored with a bajillion slashes...
			$value = stripslashes(stripslashes($value));
			// value options used to be stored with a blank space at the end... don't ask.
			$value = rtrim($value, ' ');
			
			if ( is_int($default) )
				$yarpp_options[$key] = absint($value);
			else
				$yarpp_options[$key] = $value;
		}
		
		// add the options directly first, then call set_option which will ensure defaults,
		// in case any new options have been added.
		update_option( 'yarpp', $yarpp_options );
		$this->set_option( $yarpp_options );
		
		$option_keys = array_keys( $yarpp_options );
		// append some keys for options which are long deprecated:
		$option_keys[] = 'ad_hoc_caching';
		$option_keys[] = 'excerpt_len';
		$option_keys[] = 'show_score';
		if ( count($option_keys) ) {
			$in = "('yarpp_" . join("', 'yarpp_", $option_keys) . "')";
			$wpdb->query("delete from {$wpdb->options} where option_name in {$in}");
		}
	}
	
	function upgrade_3_4b5() {
		$options = $this->get_option();
		$options['exclude'] = array(
			'post_tag' => $options['distags'],
			'category' => $options['discats']
		);
		unset( $options['distags'] );
		unset( $options['discats'] );
		update_option( 'yarpp', $options );
	}
	
	function upgrade_3_4b8() {
		$options = $this->get_option();
		$options['weight'] = array(
			'title' => (int) @$options['title'],
			'body' => (int) @$options['body'],
			'tax' => array(
				'post_tag' => (int) @$options['tags'],
				'category' => (int) @$options['categories'],
			)
		);
		
		// ensure that we consider something
		if ( $options['weight']['title'] < 2 &&
			 $options['weight']['body'] < 2 &&
			 $options['weight']['tax']['post_tag'] < 2 &&
			 $options['weight']['tax']['category'] < 2 )
			$options['weight'] = $this->default_options['weight'];
			
		unset( $options['title'] );
		unset( $options['body'] );
		unset( $options['tags'] );
		unset( $options['categories'] );
		update_option( 'yarpp', $options );
	}
	
	function upgrade_3_4_4b2() {
		$options = $this->get_option();

		// update weight values; split out tax weights into weight[tax] and require_tax
		$weight_map = array( 2 => 1, 3 => YARPP_EXTRA_WEIGHT );
		if ((int) $options['weight']['title'] == 1)
			unset( $options['weight']['title'] );
		else 
			$options['weight']['title'] = $weight_map[(int) $options['weight']['title']];

		if ((int) $options['weight']['body'] == 1)
			unset( $options['weight']['body'] );
		else 
			$options['weight']['body'] = $weight_map[(int) $options['weight']['body']];
		
		$options['require_tax'] = array();
		foreach ( $options['weight']['tax'] as $tax => $value ) {
			if ( $value == 3 )
				$options['require_tax'][$tax] = 1;
			if ( $value == 4 )
				$options['require_tax'][$tax] = 2;			
			
			if ( $value > 1 )
				$options['weight']['tax'][$tax] = 1;
			else
				unset( $options['weight']['tax'][$tax] );
		}

		// consolidate excludes, using tt_ids.
		$exclude_tt_ids = array();
		foreach ($options['exclude'] as $tax => $term_ids) {
			if ( !empty($term_ids) )
				$exclude_tt_ids = array_merge( wp_list_pluck(get_terms( $tax, array('include' => $term_ids) ), 'term_taxonomy_id'), $exclude_tt_ids );
		}
		$options['exclude'] = join(',', $exclude_tt_ids);

		update_option( 'yarpp', $options );
	}
	
	function upgrade_3_4_4b3() {
		$options = $this->get_option();
		$options['template'] = $options['use_template'] ? $options['template_file'] : false;
		$options['rss_template'] = $options['rss_use_template'] ? $options['rss_template_file'] : false;
		unset( $options['use_template'] );
		unset( $options['template_file'] );
		unset( $options['rss_use_template'] );
		unset( $options['rss_template_file'] );
		update_option( 'yarpp', $options );
	}
	
	function upgrade_3_4_4b4() {
		$options = $this->get_option();
		$options['recent'] = $options['recent_only'] ?
			$options['recent_number'] . ' ' . $options['recent_units'] : false;
		unset( $options['recent_only'] );
		unset( $options['recent_number'] );
		unset( $options['recent_units'] );
		update_option( 'yarpp', $options );
	}
	
	private $post_types = null;
	function get_post_types( $field = 'name' ) {
		if ( is_null($this->post_types) ) {
			$this->post_types = get_post_types(array(), 'objects');
			$this->post_types = array_filter( $this->post_types, array($this, 'post_type_filter') );
		}
		
		if ( 'objects' == $field )
			return $this->post_types;
		return wp_list_pluck( $this->post_types, $field );
	}
	
	private function post_type_filter( $post_type ) {
		if ( $post_type->_builtin && $post_type->show_ui )
			return true;
		if ( isset($post_type->yarpp_support) )
			return $post_type->yarpp_support;
		return false;
	}
	
	private $taxonomies = null;
	function get_taxonomies( $field = false ) {
		if ( is_null($this->taxonomies) ) {
			$this->taxonomies = get_taxonomies(array(), 'objects');
			$this->taxonomies = array_filter( $this->taxonomies, array($this, 'taxonomy_filter') );
		}
		
		if ( $field )
			return wp_list_pluck( $this->taxonomies, $field );
		return $this->taxonomies;
	}
	
	private function taxonomy_filter( $taxonomy ) {
		if ( !count(array_intersect( $taxonomy->object_type, $this->get_post_types() )) )
			return false;

		// if yarpp_support is set, follow that; otherwise include if show_ui is true
		if ( isset($taxonomy->yarpp_support) )
			return $taxonomy->yarpp_support;
		return $taxonomy->show_ui;
	}
	
	/*
	 * CORE LOOKUP + DISPLAY FUNCTIONS
	 */
	 
	/* new in 2.1! the domain argument refers to {website,widget,rss} */
	/* new in 3.0! new query-based approach: EXTREMELY HACKY! */
	/* 
	 * @param (int) $reference_ID
	 * @param (array) $args
	 * @param (bool) $echo
	 */
	function display_related($reference_ID = null, $args = array(), $echo = true) {
		global $wp_query, $pagenow;
	
		$this->upgrade_check();

		$reference_ID = ( null === $reference_ID || false === $reference_ID ) ?
			get_the_ID() : absint($reference_ID);

		// if we're already in a YARPP loop, stop now.
		if ( $this->cache->is_yarpp_time() || $this->cache_bypass->is_yarpp_time() )
			return false;
		
		$this->setup_active_cache( $args );

		$options = array( 'domain', 'limit', 'template', 'order', 'promote_yarpp' );
		extract( $this->parse_args( $args, $options ) );

		$cache_status = $this->active_cache->enforce($reference_ID);
		// If cache status is YARPP_DONT_RUN, end here without returning or echoing anything.
		if ( YARPP_DONT_RUN == $cache_status )
			return;
		
		if ( YARPP_NO_RELATED == $cache_status ) {
			// There are no results, so no yarpp time for us... :'(
		} else {
			// Get ready for YARPP TIME!
			$this->active_cache->begin_yarpp_time($reference_ID, $args);
		}
	
		// so we can return to normal later
		$current_query = $wp_query;
		$current_pagenow = $pagenow;
	
		$output = '';
		$wp_query = new WP_Query();
		if ( YARPP_NO_RELATED == $cache_status ) {
			// If there are no related posts, get no query
		} else {
			$orders = explode(' ',$order);
			$wp_query->query(array(
				'p' => $reference_ID,
				'orderby' => $orders[0],
				'order' => $orders[1],
				'showposts' => $limit,
				'post_type' => ( isset($args['post_type']) ? $args['post_type'] : $this->get_post_types() )
			));
		}
		$this->prep_query( $current_query->is_feed );
		$related_query = $wp_query; // backwards compatibility
	
		if ( 'metabox' == $domain ) {
			include(YARPP_DIR.'/template-metabox.php');
		} elseif ( !!$template && file_exists(STYLESHEETPATH . '/' . $template) ) {
			global $post;
			ob_start();
			include(STYLESHEETPATH . '/' . $template);
			$output = ob_get_contents();
			ob_end_clean();
		} elseif ( 'widget' == $domain ) {
			include(YARPP_DIR.'/template-widget.php');
		} else {
			include(YARPP_DIR.'/template-builtin.php');
		}
	
		if ( YARPP_NO_RELATED == $cache_status ) {
			// Uh, do nothing. Stay very still.
		} else {
			$this->active_cache->end_yarpp_time(); // YARPP time is over... :(
		}
	
		// restore the older wp_query.
		$wp_query = $current_query; unset($current_query); unset($related_query);
		wp_reset_postdata();
		$pagenow = $current_pagenow; unset($current_pagenow);
	
		if ($promote_yarpp && $domain != 'metabox')
			$output .= "\n<p>".sprintf(__("Related posts brought to you by <a href='%s'>Yet Another Related Posts Plugin</a>.",'yarpp'), 'http://yarpp.org')."</p>";
	
		if ($echo)
			echo $output;
		return $output;
	}
	
	/* 
	 * @param (int) $reference_ID
	 * @param (array) $args
	 */
	function get_related($reference_ID = null, $args = array()) {
		$this->upgrade_check();

		$reference_ID = ( null === $reference_ID ) ? get_the_ID() : absint($reference_ID);
	
		// if we're already in a YARPP loop, stop now.
		if ( $this->cache->is_yarpp_time() || $this->cache_bypass->is_yarpp_time() )
			return false;
		
		$this->setup_active_cache( $args );

		$options = array( 'limit', 'order' );
		extract( $this->parse_args( $args, $options ) );

		$cache_status = $this->active_cache->enforce($reference_ID);
		if ( YARPP_DONT_RUN == $cache_status || YARPP_NO_RELATED == $cache_status )
			return array();
					
		// Get ready for YARPP TIME!
		$this->active_cache->begin_yarpp_time($reference_ID, $args);
	
		$related_query = new WP_Query();
		$orders = explode(' ',$order);
		$related_query->query(array(
			'p' => $reference_ID,
			'orderby' => $orders[0],
			'order' => $orders[1],
			'showposts' => $limit,
			'post_type' => ( isset($args['post_type']) ? $args['post_type'] : $this->get_post_types() )
		));
		$this->active_cache->end_yarpp_time(); // YARPP time is over... :(
	
		return $related_query->posts;
	}
	
	/* 
	 * @param (int) $reference_ID
	 * @param (array) $args
	 */
	function related_exist($reference_ID = null, $args = array()) {
		$this->upgrade_check();
	
		$reference_ID = ( null === $reference_ID ) ? get_the_ID() : absint($reference_ID);
			
		// if we're already in a YARPP loop, stop now.
		if ( $this->cache->is_yarpp_time() || $this->cache_bypass->is_yarpp_time() )
			return false;
	
		$this->setup_active_cache( $args );
	
		$cache_status = $this->active_cache->enforce($reference_ID);
	
		if ( YARPP_NO_RELATED == $cache_status )
			return false;
	
		$this->active_cache->begin_yarpp_time($reference_ID); // get ready for YARPP TIME!
		$related_query = new WP_Query();
		$related_query->query(array(
			'p' => $reference_ID,
			'showposts' => 1,
			'post_type' => ( isset($args['post_type']) ? $args['post_type'] : $this->get_post_types() )
		));
		$return = $related_query->have_posts();
		unset($related_query);
		$this->active_cache->end_yarpp_time(); // YARPP time is over. :(
	
		return $return;
	}
		
	/* 
	 * @param (array) $args
	 * @param (bool) $echo
	 */
	function display_demo_related($args = array(), $echo = true) {
		global $wp_query;
	
		if ( $this->cache_bypass->demo_time ) // if we're already in a demo YARPP loop, stop now.
			return false;
	
		$options = array( 'domain', 'limit', 'template', 'order', 'promote_yarpp' );
		extract( $this->parse_args( $args, $options ) );
	
		$this->cache_bypass->begin_demo_time( $limit );
	
		$output = '';
		$wp_query = new WP_Query();
		$wp_query->query('');
	
		$this->prep_query( $domain == 'rss' );
		$related_query = $wp_query; // backwards compatibility
	
		if ( !!$template && file_exists(STYLESHEETPATH . '/' . $template) ) {
			global $post;
			ob_start();
			include(STYLESHEETPATH . '/' . $template);
			$output = ob_get_contents();
			ob_end_clean();
		} else {
			include(YARPP_DIR.'/template-builtin.php');
		}
	
		$this->cache_bypass->end_demo_time();
	
		if ($promote_yarpp)
			$output .= "\n<p>".sprintf(__("Related posts brought to you by <a href='%s'>Yet Another Related Posts Plugin</a>.",'yarpp'), 'http://yarpp.org')."</p>";
	
		if ( $echo )
			echo $output;
		return $output;
	}
	
	public function parse_args( $args, $options ) {
		$options_with_rss_variants = array( 'limit', 'template', 'excerpt_length', 'before_title', 'after_title', 'before_post', 'after_post', 'before_related', 'after_related', 'no_results', 'order' );

		$r = array();
		foreach ( $options as $option ) {
			if ( isset($args['domain']) && 'rss' == $args['domain'] &&
				 in_array( $option, $options_with_rss_variants ) )
				$default = $this->get_option( 'rss_' . $option );
			else
				$default = $this->get_option( $option );
			
			if ( isset($args[$option]) && $args[$option] !== $default ) {
				$r[$option] = $args[$option];
			} else {
				$r[$option] = $default;
			}
		}
		return $r;
	}
	
	private function setup_active_cache( $args ) {
		// the options which the main sql query cares about:
		$magic_options = array( 'limit', 'threshold', 'show_pass_post', 'past_only', 'weight', 'exclude', 'require_tax', 'recent' );

		$defaults = $this->get_option();
		foreach ( $magic_options as $option ) {
			if ( !isset($args[$option]) )
				continue;
				
			// limit is a little different... if it's less than what we cache,
			// let it go.
			if ( 'limit' == $option &&
				 $args[$option] <= max($defaults['limit'], $defaults['rss_limit']) )
				 continue;
			
			if ( $args[$option] !== $defaults[$option] ) {
				$this->active_cache = $this->cache_bypass;
				return;
			}
		}
		$this->active_cache = $this->cache;
	}
	
	private function prep_query( $is_feed = false ) {
		global $wp_query;
		$wp_query->in_the_loop = true;
		$wp_query->is_feed = $is_feed;
		// make sure we get the right is_single value
		// (see http://wordpress.org/support/topic/288230)
		$wp_query->is_single = false;
	}
	
	/*
	 * DEFAULT CONTENT FILTERS
	 */
	 
	function the_content($content) {
		if (is_feed())
			return $this->the_content_rss($content);
	
		if ( $this->get_option('cross_relate') )
			$type = $this->get_post_types();
		else if ( 'page' == get_post_type() )
			$type = array( 'page' );
		else
			$type = array( 'post' );
	
		if ( $this->get_option('auto_display') && is_single() )
			return $content . $this->display_related(null, array('post_type' => $type, 'domain' => 'website'), false);
		else
			return $content;
	}
	
	function the_content_rss($content) {
		if ( $this->get_option('cross_relate') )
			$type = $this->get_post_types();
		else if ( 'page' == get_post_type() )
			$type = array( 'page' );
		else
			$type = array( 'post' );
	
		if ( $this->get_option('rss_display') )
			return $content . $this->display_related(null, array('post_type' => $type, 'domain' => 'rss'), false);
		else
			return $content;
	}
	
	function the_excerpt_rss($content) {
		if ( $this->get_option('cross_relate') )
			$type = $this->get_post_types();
		else if ( 'page' == get_post_type() )
			$type = array( 'page' );
		else
			$type = array( 'post' );
	
		if ( $this->get_option('rss_excerpt_display') && $this->get_option('rss_display') )
			return $content . clean_pre($this->display_related(null, array('post_type' => $type, 'domain' => 'rss'), false));
		else
			return $content;
	}
	
	/*
	 * UTILS
	 */
	
	// new in 3.3: use PHP serialized format instead of JSON
	function version_info( $enforce_cache = false ) {
		if (false === ($result = get_transient('yarpp_version_info')) || $enforce_cache) {
			$version = YARPP_VERSION;
			$remote = wp_remote_post("http://mitcho.com/code/yarpp/checkversion.php?format=php&version={$version}");
			
			if (is_wp_error($remote))
				return false;
			
			$result = unserialize($remote['body']);
			set_transient('yarpp_version_info', $result, 60*60*12);
		}
		return $result;
	}
}
