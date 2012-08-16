<?php

class YARPP_Admin {
	public $core;
	public $hook;
	
	function __construct( &$core ) {
		$this->core = &$core;
		
		// if action=flush and the nonce is correct, reset the cache
		if ( isset($_GET['action']) && $_GET['action'] == 'flush' &&
			 check_ajax_referer( 'yarpp_cache_flush', false, false ) !== false ) {
			$this->core->cache->flush();
			wp_redirect( admin_url( '/options-general.php?page=yarpp' ) );
			exit;
		}
		
		add_action( 'admin_init', array($this, 'ajax_register') );
		add_action( 'admin_menu', array( $this, 'ui_register' ) );
		// new in 3.3: set default meta boxes to show:
		add_filter( 'default_hidden_meta_boxes', array( $this, 'default_hidden_meta_boxes' ), 10, 2 );
	}
	
	private $templates = null;
	public function get_templates() {
		if ( is_null($this->templates) ) {
			$this->templates = glob(STYLESHEETPATH . '/yarpp-template-*.php');
			// if glob hits an error, it returns false.
			if ( $this->templates === false )
				$this->templates = array();
			// get basenames only
			$this->templates = array_map('basename', $this->templates);
		}
		return (array) $this->templates;
	}
	
	function ajax_register() {
		// Register AJAX services
		if ( defined('DOING_AJAX') && DOING_AJAX ) {
			add_action( 'wp_ajax_yarpp_display_exclude_terms', array( $this, 'ajax_display_exclude_terms' ) );
			add_action( 'wp_ajax_yarpp_display_demo', array( $this, 'ajax_display_demo' ) );
			add_action( 'wp_ajax_yarpp_display', array( $this, 'ajax_display' ) );
		}
	}
	
	function ui_register() {
		global $wp_version;
		if ( get_option( 'yarpp_activated' ) && version_compare($wp_version, '3.3b1', '>=') ) {
			delete_option( 'yarpp_activated' );
			add_action( 'admin_enqueue_scripts', array( $this, 'pointer_enqueue' ) );
			add_action( 'admin_print_footer_scripts', array( $this, 'pointer_script' ) );
		}

		// setup admin
		$this->hook = add_options_page(__('Related Posts (YARPP)','yarpp'),__('Related Posts (YARPP)','yarpp'), 'manage_options', 'yarpp', array( $this, 'options_page' ) );
		// new in 3.3: load options page sections as metaboxes
		require_once('options-meta-boxes.php');

		// new in 3.0.12: add settings link to the plugins page
		add_filter('plugin_action_links', array( $this, 'settings_link' ), 10, 2);

		// new in 3.0: add meta box		
		add_meta_box( 'yarpp_relatedposts', __( 'Related Posts' , 'yarpp') . ' <span class="postbox-title-action"><a href="' . esc_url( admin_url('options-general.php?page=yarpp') ) . '" class="edit-box open-box">' . __( 'Configure' ) . '</a></span>', array( $this, 'metabox' ), 'post', 'normal' );
		
		// new in 3.3: properly enqueue scripts for admin:
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}
	
	// since 3.3
	function enqueue() {
		global $current_screen;
		$version = defined('WP_DEBUG') && WP_DEBUG ? time() : YARPP_VERSION;
		if (is_object($current_screen) && $current_screen->id == 'settings_page_yarpp') {
			wp_enqueue_script( 'postbox' );
			wp_enqueue_style( 'yarpp_options', plugins_url( 'options.css', __FILE__ ), array(), $version );
			wp_enqueue_script( 'yarpp_options', plugins_url( 'js/options.js', __FILE__ ), array('jquery'), $version );
		}
		if (is_object($current_screen) && $current_screen->id == 'post') {
			wp_enqueue_script( 'yarpp_metabox', plugins_url( 'js/metabox.js', __FILE__ ), array('jquery'), $version );
		}
	}
	
	// since 3.4 and WP 3.3
	function pointer_enqueue() {
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
	}
	function pointer_script() {
		$content = '<h3>' . str_replace('<span>', '<span style="font-style:italic; font-weight: inherit;">', __('Thank you for installing <span>Yet Another Related Posts Plugin</span>!', 'yarpp') )  . '</h3>';
		$content .= '<p>' . str_replace('<a>', '<a href="' . esc_url(admin_url('options-general.php?page=yarpp')) .'">', __('Make sure to visit the <a>Related Posts settings page</a> to customize YARPP.', 'yarpp') ). '</p>';
		?>
<script>
jQuery(function () {
	var body = jQuery(document.body),
	menu = jQuery('#menu-settings'),
	collapse = jQuery('#collapse-menu'),
	yarpp = menu.find("a[href='options-general.php?page=yarpp']"),
	options = {
		content: '<?php echo $content; ?>',
		position: {
			edge: 'left',
			align: 'center',
			of: menu.is('.wp-menu-open') && !menu.is('.folded *') ? yarpp : menu
		},
		close: function() {
			menu.unbind('mouseenter mouseleave', yarpp_pointer);
			collapse.unbind('mouseenter mouseleave', yarpp_pointer);
		}};
	
	if ( !yarpp.length )
		return;
	
	body.pointer(options).pointer('open');
	
	if ( menu.is('.folded *') || !menu.is('.wp-menu-open') ) {
		function yarpp_pointer(e) {
			setTimeout(function() {
				if (yarpp.is(':visible'))
					options.position.of = yarpp;
				else
					options.position.of = menu;
				body.pointer( options );
			}, 200);
		}
		menu.bind('mouseenter mouseleave', yarpp_pointer);
		collapse.bind('mouseenter mouseleave', yarpp_pointer);
	}
});
</script>
		<?php
	}
	
	function settings_link($links, $file) {
		$this_plugin = dirname(plugin_basename(__FILE__)) . '/yarpp.php';
		if($file == $this_plugin) {
			$links[] = '<a href="options-general.php?page=yarpp">' . __('Settings') . '</a>';
		}
		return $links;
	}
	
	function options_page() {
		// for proper metabox support:
		require(YARPP_DIR.'/options.php');
	}

	// since 3.4: don't actually compute results here, but use ajax instead		
	function metabox() {
		?>
		<style>
		#yarpp_relatedposts h3 .postbox-title-action {
			right: 30px;
			top: 5px;
			position: absolute;
			padding: 0;
		}
		#yarpp_relatedposts:hover .edit-box {
			display: inline;
		}
		</style>
		<?php
		if ( !get_the_ID() ) {
			echo "<div><p>".__("Related entries may be displayed once you save your entry",'yarpp').".</p></div>";
		} else {
			wp_nonce_field( 'yarpp_display', 'yarpp_display-nonce', false );
			echo '<div id="yarpp-related-posts"><img src="' . esc_url( admin_url( 'images/wpspin_light.gif' ) ) . '" alt="" /></div>';
		}
	}
	
	// since 3.3: default metaboxes to show:
	function default_hidden_meta_boxes($hidden, $screen) {
		if ( 'settings_page_yarpp' == $screen->id )
			$hidden = array( 'yarpp_pool', 'yarpp_relatedness' );
		return $hidden;
	}
	
	/*
	 * AJAX SERVICES
	 */

	function ajax_display_exclude_terms() {
		check_ajax_referer( 'yarpp_display_exclude_terms' );
		
		if ( !isset($_REQUEST['taxonomy']) )
			return;
		
		$taxonomy = (string) $_REQUEST['taxonomy'];
		
		header("HTTP/1.1 200");
		header("Content-Type: text/html; charset=UTF-8");
		
		$exclude_tt_ids = wp_parse_id_list(yarpp_get_option('exclude'));
		$exclude_term_ids = $this->get_term_ids_from_tt_ids( $taxonomy, $exclude_tt_ids );
//		if ( 'category' == $taxonomy )
//			$exclude .= ',' . get_option( 'default_category' );

		$terms = get_terms($taxonomy, array(
			'exclude' => $exclude_term_ids,
			'hide_empty' => false,
			'hierarchical' => false,
			'number' => 100,
			'offset' => $_REQUEST['offset']
		));
		
		if ( !count($terms) ) {
			echo ':('; // no more :(
			exit;
		}
		
		foreach ($terms as $term) {
			echo "<span><input type='checkbox' name='exclude[{$term->term_taxonomy_id}]' id='exclude_{$term->term_taxonomy_id}' value='true' /> <label for='exclude_{$term->term_taxonomy_id}'>" . esc_html($term->name) . "</label></span> ";
		}
		exit;
	}
	
	function get_term_ids_from_tt_ids( $taxonomy, $tt_ids ) {
		global $wpdb;
		$tt_ids = wp_parse_id_list($tt_ids);
		if ( empty($tt_ids) )
			return array();
		return $wpdb->get_col("select term_id from $wpdb->term_taxonomy where taxonomy = '{$taxonomy}' and term_taxonomy_id in (" . join(',', $tt_ids) . ")");
	}
	
	function ajax_display() {
		check_ajax_referer( 'yarpp_display' );

		if ( !isset($_REQUEST['ID']) )
			return;

		header("HTTP/1.1 200");
		header("Content-Type: text/html; charset=UTF-8");

		$args = array(
			'post_type' => array('post'),
			'domain' => isset($_REQUEST['domain']) ? $_REQUEST['domain'] : 'website'
		);
		if ( $this->core->get_option('cross_relate') )
			$args['post_type'] = $yarpp->get_post_types();
			
		$return = $this->core->display_related(absint($_REQUEST['ID']), $args, false);
		echo $return;
		exit;
	}

	function ajax_display_demo() {
		check_ajax_referer( 'yarpp_display_demo' );

		header("HTTP/1.1 200");
		header("Content-Type: text/html; charset=UTF-8");
	
		$args = array(
			'post_type' => array('post'),
			'domain' => isset($_REQUEST['domain']) ? $_REQUEST['domain'] : 'website'
		);
			
		$return = $this->core->display_demo_related($args, false);
		echo ereg_replace("[\n\r]",'',nl2br(htmlspecialchars($return)));
		exit;
	}
}