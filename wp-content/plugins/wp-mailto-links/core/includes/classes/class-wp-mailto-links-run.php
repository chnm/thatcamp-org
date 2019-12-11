<?php

/**
 * Class WP_Mailto_Links_Run
 *
 * Thats where we bring the plugin to life
 *
 * @since 3.0.0
 * @package WPMT
 * @author Ironikus <info@ironikus.com>
 */

class WP_Mailto_Links_Run{

	/**
	 * The main page name for our admin page
	 *
	 * @var string
	 * @since 3.0.0
	 */
	private $page_name;

	/**
	 * The main page title for our admin page
	 *
	 * @var string
	 * @since 3.0.0
	 */
	private $page_title;

	/**
	 * Our WP_Mailto_Links_Run constructor.
	 */
	function __construct(){
		$this->page_name    			= WPMT()->settings->get_page_name();
		$this->page_title   			= WPMT()->settings->get_page_title();
		$this->final_outout_buffer_hook = WPMT()->settings->get_final_outout_buffer_hook();
		$this->widget_callback_hook 	= WPMT()->settings->get_widget_callback_hook();
		$this->add_hooks();
	}

	/**
	 * Define all of our necessary hooks
	 */
	private function add_hooks(){
		$filter_hook = (bool) WPMT()->settings->get_setting( 'filter_hook', true, 'filter_body' );
		if( $filter_hook ){
			$hook_name = 'init';
		} else {
			$hook_name = 'wp';
		}
		
		add_action( 'wp', array( $this, 'display_email_image' ), WPMT()->settings->get_hook_priorities( 'display_email_image' ) );
		add_action( 'init', array( $this, 'buffer_final_output' ), WPMT()->settings->get_hook_priorities( 'buffer_final_output' ) );
		add_action( 'init', array( $this, 'add_custom_template_tags' ), WPMT()->settings->get_hook_priorities( 'add_custom_template_tags' ) );
		add_action( $hook_name, array( $this, 'setup_single_filter_hooks' ), WPMT()->settings->get_hook_priorities( 'setup_single_filter_hooks' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_frontend_header_styling' ), WPMT()->settings->get_hook_priorities( 'load_frontend_header_styling' ) );
		add_filter( 'dynamic_sidebar_params', array( $this, 'wpmt_dynamic_sidebar_params' ), WPMT()->settings->get_hook_priorities( 'wpmt_dynamic_sidebar_params' ) );

		//Add shortcodes
		add_shortcode( 'wpml_mailto', array( $this, 'mailto_shortcode' ) );
		add_shortcode( 'wpmt_protect', array( $this, 'protect_content_shortcode' ) );

	}

	/**
	 * ######################
	 * ###
	 * #### PAGE BUFFERING & WIDGET FILTER
	 * ###
	 * ######################
	 */

	 /**
	  * Buffer the final output on the init hook
	  *
	  * @return void
	  */
	public function buffer_final_output(){
        if ( ! defined( 'WP_CLI' ) ) {
			ob_start( array( $this, 'apply_content_filter' ) );
		}
    }

	 /**
     * Apply the callabla function for ob_start()
	 * 
     * @param string $content
     * @return string - the filtered content
     */
    public function apply_content_filter( $content ){
        $filteredContent = apply_filters( $this->final_outout_buffer_hook, $content );

        // remove filters after applying to prevent multiple applies
        remove_all_filters( $this->final_outout_buffer_hook );

        return $filteredContent;
	}
	
	/**
     * Filter for "dynamic_sidebar_params" hook
	 * 
     * @global array $wp_registered_widgets
     * @param  array $params
     * @return array
     */
    public function wpmt_dynamic_sidebar_params( $params){
         global $wp_registered_widgets;

        if ( is_admin() ) {
            return $params;
        }

        $widget_id = $params[0]['widget_id'];

        // prevent overwriting when already set by another version of the widget output class
        if ( isset( $wp_registered_widgets[ $widget_id ]['_wo_original_callback'] ) ) {
            return $params;
        }

        $wp_registered_widgets[ $widget_id ]['_wo_original_callback'] = $wp_registered_widgets[ $widget_id ]['callback'];
        $wp_registered_widgets[ $widget_id ]['callback'] = array( $this, 'call_widget_callback' );

        return $params;
	}
	
	/**
     * The Widget Callback
     * @global array $wp_registered_widgets
     */
    public function call_widget_callback(){
        global $wp_registered_widgets;

		$original_callback_params = func_get_args();
		$original_callback = null;
		
		$widget_id = $original_callback_params[0]['widget_id'];

		$original_callback = $wp_registered_widgets[ $widget_id ]['_wo_original_callback'];
		$wp_registered_widgets[ $widget_id ]['callback'] = $original_callback;

		$widget_id_base = $wp_registered_widgets[ $widget_id ]['callback'][0]->id_base;

        if ( is_callable( $original_callback ) ) {
            ob_start();
            call_user_func_array( $original_callback, $original_callback_params );
            $widget_output = ob_get_clean();

            echo apply_filters( $this->widget_callback_hook, $widget_output, $widget_id_base, $widget_id );

            // remove filters after applying to prevent multiple applies
            remove_all_filters( $this->widget_callback_hook );
        }
    }

	/**
	 * ######################
	 * ###
	 * #### SCRIPT ENQUEUEMENTS
	 * ###
	 * ######################
	 */

	public function load_frontend_header_styling(){

		$js_version  = date( "ymd-Gis", filemtime( WPMT_PLUGIN_DIR . 'core/includes/assets/js/custom.js' ));
		$css_version = date( "ymd-Gis", filemtime( WPMT_PLUGIN_DIR . 'core/includes/assets/css/style.css' ));
		$protection_activated = (int) WPMT()->settings->get_setting( 'protect', true );
		$without_javascript = (string) WPMT()->settings->get_setting( 'protect_using', true );
		$footer_scripts = (bool) WPMT()->settings->get_setting( 'footer_scripts', true );
		 
		if( $without_javascript !== 'without_javascript' ){
			wp_enqueue_script( 'wpmt-js-frontend', WPMT_PLUGIN_URL . 'core/includes/assets/js/custom.js', array( 'jquery' ), $js_version, $footer_scripts );
		}
		
		wp_register_style( 'wpmt-css-frontend',    WPMT_PLUGIN_URL . 'core/includes/assets/css/style.css', false,   $css_version );
		wp_enqueue_style ( 'wpmt-css-frontend' );

		if( (string) WPMT()->settings->get_setting( 'security_check', true ) === '1' ){
			wp_enqueue_style('dashicons');
		}

	}

	/**
	 * ######################
	 * ###
	 * #### CORE LOGIC
	 * ###
	 * ######################
	 */

	 /**
	  * Register all single filters to protect your content
	  *
	  * @return void
	  */
    public function setup_single_filter_hooks(){

		$exclude_posts = (string) WPMT()->settings->get_setting( 'exclude_posts', true );
		if( ! empty( $exclude_posts ) ){
			global $post;
			$exclude_pages = explode( ',', $exclude_posts );

			if( is_array( $exclude_pages ) ){
				$exclude_pages_validated = array();

				foreach( $exclude_pages as $post_id ){
					if( is_numeric( $post_id ) ){
						$exclude_pages_validated[] = intval( $post_id );
					}
				}

				if (in_array($post->ID, $exclude_pages_validated)) {
					return;
				}
			}

		}


		$protection_method = (int) WPMT()->settings->get_setting( 'protect', true );
		$filter_rss = (int) WPMT()->settings->get_setting( 'filter_rss', true, 'filter_body' );
		$remove_shortcodes_rss = (int) WPMT()->settings->get_setting( 'remove_shortcodes_rss', true, 'filter_body' );
		$protect_shortcode_tags = (bool) WPMT()->settings->get_setting( 'protect_shortcode_tags', true, 'filter_body' );
		$protect_shortcode_tags_valid = false;

		if ( is_feed() ) {
			
			if( $filter_rss === 1 ){
				add_filter( $this->final_outout_buffer_hook, array( $this, 'filter_rss' ), WPMT()->settings->get_hook_priorities( 'filter_rss' ) );
			}

			if ( $remove_shortcodes_rss ) {
				add_filter( $this->final_outout_buffer_hook, array( $this, 'callback_rss_remove_shortcodes' ), WPMT()->settings->get_hook_priorities( 'callback_rss_remove_shortcodes' ) );
			}
		
		}

		if ( $protection_method === 2 ) {
			$protect_shortcode_tags_valid = true;

			$filter_hooks = array(
				'the_title', 
				'the_content', 
				'the_excerpt', 
				'get_the_excerpt',

				//Comment related
				'comment_text', 
				'comment_excerpt', 
				'comment_url',
				'get_comment_author_url',
				'get_comment_author_url_link',

				//Widgets
				'widget_title',
				'widget_text',
				'widget_content',
				'widget_output',
			);

			$filter_hooks = apply_filters( 'wpmt/frontend/wordpress_filters', $filter_hooks );

			foreach ( $filter_hooks as $hook ) {
			   add_filter( $hook, array( $this, 'filter_content' ), WPMT()->settings->get_hook_priorities( 'filter_content' ) );
			}
		} elseif( $protection_method === 1 ){
			$protect_shortcode_tags_valid = true;

			add_filter( $this->final_outout_buffer_hook, array( $this, 'filter_page' ), WPMT()->settings->get_hook_priorities( 'filter_page' ) );
		}

		if( $protect_shortcode_tags_valid ){
			if( $protect_shortcode_tags ){
				add_filter( 'do_shortcode_tag', array( $this, 'filter_content' ), WPMT()->settings->get_hook_priorities( 'do_shortcode_tag' ) );
			}
		}
		
	}
	
	/**
	 * Filter the page itself
	 * 
     * @param string $content
     * @return string
     */
    public function filter_page( $content ){
		$protect_using = (string) WPMT()->settings->get_setting( 'protect_using', true );

        return WPMT()->validate->filter_page( $content, $protect_using );
    }

    /**
	 * Filter the whole content
	 * 
     * @param string $content
     * @return string
     */
    public function filter_content( $content ){
		$protect_using = (string) WPMT()->settings->get_setting( 'protect_using', true );
        return WPMT()->validate->filter_content( $content, $protect_using );
    }

    /**
	 * Filter the rss content
	 * 
     * @param string $content
     * @return string
     */
    public function filter_rss( $content ){
		$protection_type = (string) WPMT()->settings->get_setting( 'protect_using', true );
        return WPMT()->validate->filter_rss( $content, $protection_type );
	}

	/**
     * RSS Callback Remove shortcodes
     * @param string $content
     * @return string
     */
    public function callback_rss_remove_shortcodes( $content ) {
        
        $content = strip_shortcodes($content);

        return $content;
    }
	
	/**
	 * ######################
	 * ###
	 * #### SHORTCODES
	 * ###
	 * ######################
	 */

	 /**
     * Handle mailto shortcode
     * @param array   $atts
     * @param string  $content
     */
    public function mailto_shortcode( $atts, $content = null ){
		$protect = (int) WPMT()->settings->get_setting( 'protect', true );
		$protection_activated = ( $protect === 1 || $protect === 2 ) ? true : false;

        if ( $protection_activated && preg_match( WPMT()->settings->get_email_regex(), $content ) > 0 ) {
            $content = WPMT()->validate->get_protected_display( $content );
        }

        if ( isset( $atts['email'] ) ) {
            $atts['href'] = 'mailto:' . $atts['email'];
            unset( $atts['email'] );
        }

        $content = WPMT()->validate->create_protected_mailto( $content, $atts );

        return $content;
	}

	 /**
     * Handle content filter shortcode
     * @param array   $atts
     * @param string  $content
     */
    public function protect_content_shortcode( $atts, $content = null ){
		$protect = (int) WPMT()->settings->get_setting( 'protect', true );
		$protect_using = (string) WPMT()->settings->get_setting( 'protect_using', true );
		$protection_activated = ( $protect === 1 || $protect === 2 ) ? true : false;

        if ( ! $protection_activated ) {
			return $content;
		}
		
		if( isset( $atts['protect_using'] ) ){
			$protect_using = $atts['protect_using'];
		}

        $content = WPMT()->validate->filter_content( $content, $protect_using );

        return $content;
	}
	
	/**
	 * ######################
	 * ###
	 * #### EMAIL IMAGE
	 * ###
	 * ######################
	 */

	 public function display_email_image(){

		if( ! isset( $_GET['wpmt_mail'] ) ){
			return;
		}

		$email = sanitize_email( base64_decode( $_GET['wpmt_mail'] ) );
		 
		if( ! is_email( $email ) || ! isset( $_GET['wpmt_hash'] ) ){
			return;
		}

		$hash = (string) $_GET['wpmt_hash'];
		$secret = WPMT()->settings->get_email_image_secret();

		if( WPMT()->validate->generate_email_signature( $email, $secret ) !== $hash ){
			wp_die( WPMT()->helpers->translate('Your signture is invalid.', 'plugin-frontend') );
		}

		$image = WPMT()->validate->email_to_image( $email );

		if( empty( $image ) ){
			wp_die( WPMT()->helpers->translate('Your email could not be converted.', 'plugin-frontend') );
		}

		header('Content-type: image/png');
		echo $image;
		die();

	 }
	
	/**
	 * ######################
	 * ###
	 * #### TEMPLATE TAGS
	 * ###
	 * ######################
	 */

	 public function add_custom_template_tags(){
		$template_tags = WPMT()->settings->get_template_tags();

		foreach( $template_tags as $hook => $callback ){

			//Make sure we only call our own custom template tags
			if( is_callable( array( $this, $callback ) ) ){
				apply_filters( $hook, array( $this, $callback ), 10 );
			}

		}
	 }

	 /**
	  * Filter for the wpml_filter template tag
	  *	
	  * This function is called dynamically by add_custom_template_tags 
	  * using the WPMT()->settings->get_template_tags() callback.
	  * 
	  * @param string $content - the default content
	  * @return string - the filtered content
	  */
	 public function template_tag_wpmt_filter( $content ){
		$protect_using = (string) WPMT()->settings->get_setting( 'protect_using', true );
        return WPMT()->validate->filter_content( $content, $protect_using );
	 }

	 /**
	  * Filter for the wpml_filter template tag
	  *	
	  * This function is called dynamically by add_custom_template_tags 
	  * using the WPMT()->settings->get_template_tags() callback.
	  * 
	  * @param string $content - the default content
	  * @return string - the filtered content
	  */
	 public function template_tag_wpml_mailto( $email, $display = null, $atts = array() ){
        if ( is_array( $display ) ) {
            // backwards compatibility (old params: $display, $attrs = array())
            $atts   = $display;
            $display = $email;
        } else {
            $atts['href'] = 'mailto:'.$email;
        }

        return WPMT()->validate->create_protected_mailto( $display, $atts );
	 }

}
