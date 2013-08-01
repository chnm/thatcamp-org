<?php 
/**
 * Retrieve the theme's user settings and default settings. Individual files can access
 * these setting via a global variable call, so database query is only
 * done once.
*/
require( get_template_directory() . '/admin/options-defaults.php' );
$graphene_defaults = apply_filters( 'graphene_defaults', $graphene_defaults );
function graphene_get_settings(){
	global $graphene_defaults;
	$graphene_settings = array_merge( $graphene_defaults, (array) get_option( 'graphene_settings', array() ) );
	return apply_filters( 'graphene_settings', $graphene_settings );
}
global $graphene_settings;
$graphene_settings = graphene_get_settings();

/* Get the WPML helper functions */
include( get_template_directory() . '/admin/wpml-helper.php' );

/**
 * Includes the files where our theme options are defined
*/
include( $graphene_settings['template_dir'] . '/admin/options.php' );
include( $graphene_settings['template_dir'] . '/admin/faq.php' );

/* Include the settings validator */
include( $graphene_settings['template_dir'] . '/admin/options-validator.php');

/* Indlude AJAX handler */
include( $graphene_settings['template_dir'] . '/admin/ajax-handler.php');

/* Include the feature pointer */
/* Disabled for now until a proper API has been implemented in WordPress core */
// include( $graphene_settings['template_dir'] . '/admin/feature-pointers.php');

/** 
 * Adds the theme options page
*/
function graphene_options_init() {
	global $graphene_settings;
	
	$graphene_settings['hook_suffix'] = add_theme_page( __( 'Graphene Options', 'graphene' ), __( 'Graphene Options', 'graphene' ), 'edit_theme_options', 'graphene_options', 'graphene_options' );
	$graphene_settings['hook_suffix_faq'] = add_theme_page( __( 'Graphene FAQs', 'graphene' ), __( 'Graphene FAQs', 'graphene' ), 'edit_theme_options', 'graphene_faq', 'graphene_faq' );
	
	add_action( 'admin_print_styles-' . $graphene_settings['hook_suffix'], 'graphene_admin_options_style' );
	add_action( 'admin_print_styles-' . $graphene_settings['hook_suffix_faq'], 'graphene_admin_options_style' );
	add_action( 'admin_print_scripts-' . $graphene_settings['hook_suffix'], 'graphene_admin_scripts' );
	add_action( 'admin_head-' . $graphene_settings['hook_suffix'], 'graphene_custom_style' );
	add_action( 'admin_head-' . $graphene_settings['hook_suffix'], 'graphene_register_t_options' );
	add_action( 'admin_head-' . $graphene_settings['hook_suffix'], 'graphene_wpml_register_strings', 20 );
	
	do_action( 'graphene_options_init' );
}
add_action( 'admin_menu', 'graphene_options_init', 8 );


/**
 * Allow users with 'edit_theme_options' capability to be able to modify the theme's options
 */
function graphene_options_page_capability( $cap ){
	return apply_filters( 'graphene_options_page_capability', 'edit_theme_options' );
}
add_filter( 'option_page_capability_graphene_options', 'graphene_options_page_capability' );


/**
 * Add JavaScript for the theme's options page
*/
function graphene_options_js(){ 
    global $graphene_settings;
	
	$tab = 'general'; // default set the current tab to general
	// detect any other allowed tabs
	if ( isset( $_GET['tab'] ) && in_array( $_GET['tab'], array('general', 'display', 'colours', 'advanced' ) ) ){ $tab = $_GET['tab']; }
	?>
	<script type="text/javascript">
	//<![CDATA[
		var graphene_tab = '<?php echo $tab; ?>';
		var graphene_settings = <?php echo json_encode( $graphene_settings ); ?>;
		var graphene_uri = '<?php echo get_template_directory_uri(); ?>';
	//]]>
	</script>
	<?php
}


/**
 * Admin footer
 */
function graphene_admin_footer(){
	global $graphene_settings;
	add_action( 'admin_footer-' . $graphene_settings['hook_suffix'], 'graphene_options_js' );
}
add_action( 'admin_menu', 'graphene_admin_footer' );


/**
 * Enqueue style for admin page
*/
if ( ! function_exists( 'graphene_admin_options_style' ) ) :
	function graphene_admin_options_style() {
	
		wp_register_style( 'graphene-admin-style', get_template_directory_uri() . '/admin/admin.css' );
		if ( is_rtl() ) { wp_register_style( 'graphene-admin-style-rtl', get_template_directory_uri() . '/admin/admin-rtl.css' );}
	
		wp_enqueue_style( 'graphene-admin-style' );
		if ( is_rtl() ) { wp_enqueue_style( 'graphene-admin-style-rtl' ); }
		
		wp_enqueue_style( 'thickbox' );
		// wp_enqueue_style( 'wp-pointer' );
		
		wp_enqueue_style( 'graphene-codemirror', get_template_directory_uri() . '/js/codemirror/codemirror.css', array(), '', 'all' );
		wp_deregister_style( 'chosen' );
		wp_enqueue_style( 'chosen', get_template_directory_uri() . '/js/chosen/chosen.css', array(), '', 'all' );
		
		if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'display' )
			wp_enqueue_style( 'jquery-ui-slider' );
		else if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'colours' )
			wp_enqueue_style( 'farbtastic' );
	}
endif;


/**
 * Script required for the theme options page
 */
function graphene_admin_scripts() {
		
	/* Enqueue scripts */
    wp_enqueue_script( 'media-upload' );
    wp_enqueue_script( 'thickbox' );
	wp_enqueue_script( 'graphene-admin-js' );
	wp_localize_script( 'graphene-admin-js', 'grapheneAdminScript', array(
		'preset_name'			=> __( 'What should we call this preset?', 'graphene' ),
		'preset_name_req'		=> __( 'Preset name is required to save a preset.', 'graphene' ),
		'preset_delete_confirm'	=> __( 'You are deleting this preset:', 'graphene' ),
		'chosen_no_search_result'	=> __( 'Oops, nothing found.', 'graphene' ),
		'is_rtl'				=> is_rtl(),
	));
    // wp_enqueue_script( 'wp-pointer' );
	
	wp_enqueue_script( 'graphene-codemirror', get_template_directory_uri() . '/js/codemirror/codemirror.js', array(), '', false );
	wp_deregister_script( 'chosen' );
	wp_enqueue_script( 'chosen', get_template_directory_uri() . '/js/chosen/chosen.jquery.min.js', array( 'jquery' ), '', false );
	
	if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'display' )
		wp_enqueue_script( 'jquery-ui-slider' );
	else if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'colours' )
		wp_enqueue_script( 'farbtastic' );
	else 
		wp_enqueue_script( 'jquery-ui-sortable' );     
}


/**
 * Generates the tabs in the theme's options page
*/
if ( ! function_exists( 'graphene_options_tabs' ) ) :
	function graphene_options_tabs( $current = 'general', $tabs = array( 'general' => 'General' ) ) {
		$links = array();
		foreach( $tabs as $tab => $name ) :
			if ( $tab == $current ) :
				$links[] = "<a class='nav-tab nav-tab-$tab nav-tab-active' href='?page=graphene_options&amp;tab=$tab'>$name</a>";
			else :
				$links[] = "<a class='nav-tab nav-tab-$tab' href='?page=graphene_options&amp;tab=$tab'>$name</a>";
			endif;
		endforeach;
		
		echo '<h3 class="options-tab">';
		foreach ( $links as $link )
			echo $link;
		echo '<a class="toggle-all" href="#">' . __( 'Toggle all options boxes', 'graphene' ) . '</a>';
		/* Disabled for now, until proper feature point API is implemented in WordPress core
		echo '<div class="toggle-options-wrapper">';
		echo '<a class="toggle-essential-options" href="#">' . __( 'Show essential options', 'graphene' ) . '</a>';
		echo '<a class="toggle-all-options" href="#">' . __( 'Show all options', 'graphene' ) . '</a>';
		echo '</div>';
		*/
		echo '</h3>';
	}
endif;


/**
 * Output the options content
 *
 * @param string $tab The slug of the option tab to display
 *
 * @package Graphene
 * @since Graphene 1.8
 */
if ( ! function_exists( 'graphene_options_tabs_content' ) ) :
function graphene_options_tabs_content( $tab ){
	require( get_template_directory() . '/admin/options-' . $tab . '.php' );
	call_user_func( 'graphene_options_' . $tab );
}
endif;


/**
 * Include the file for additional user fields
*/
include( $graphene_settings['template_dir'] . '/admin/user.php' );

/**
 * Include the file for additional custom fields in posts and pages editing screens
*/
include( $graphene_settings['template_dir'] . '/admin/custom-fields.php' );


/**
 * Add a link to the theme's options page in the admin bar
*/
function graphene_wp_admin_bar_theme_options(){
	if ( ! current_user_can( 'edit_theme_options' ) ) return;
	global $wp_admin_bar;
	$wp_admin_bar->add_menu( array( 
								'parent' 	=> 'appearance',
								'id' 		=> 'graphene-options',
								'title' 	=> 'Graphene Options',
								'href' 		=> admin_url( 'themes.php?page=graphene_options' ) ) );
}
add_action( 'admin_bar_menu', 'graphene_wp_admin_bar_theme_options', 61 );


/**
 * Displays a graphic visualizer for template selection in the Edit Page screen
*/
function graphene_page_template_visualizer() {  
    global $graphene_settings, $post_id;
    $template_not_found = __( 'Template preview not found.', 'graphene' );    
    
	if ( ! get_post_meta( $post_id, '_wp_page_template', true ) ){
		$default_template = __( 'default', 'graphene' );
	} else {
		switch( $graphene_settings['column_mode']){
			case 'one_column':
				$default_template = 'template-onecolumn.php';
				break;
			case 'two_col_right':
				$default_template = 'template-twocolumnsright.php';
				break;
			case 'three_col_left':
				$default_template = 'template-threecolumnsleft.php';
				break;
			case 'three_col_right':
				$default_template = 'template-threecolumnsright.php';
				break;
			case 'three_col_center':
				$default_template = 'template-threecolumnscenter.php';
				break;
			default:
				$default_template = 'template-twocolumnsleft.php';
				break;
		}
	}
    
    
    $preview_img_path = get_template_directory_uri() . '/admin/images/';
    ?>
    <script type="text/javascript">
    //<![CDATA[
    jQuery(document).ready(function($){
        $( '#page_template' ).change(function(){
           update_page_template();           
        });
		$( '#page_template' ).after( '<p><img id="page_template_img" alt="" /></p>' );
        
        function update_page_template() {
            var preview_img = $( '#page_template' ).val().replace(/.php$/, '.png' );
			<?php if ( is_rtl() ) : ?>
				if ( preview_img.indexOf('left') > -1 ) preview_img = preview_img.replace('left','right');
				else if ( preview_img.indexOf('right') > -1 ) preview_img = preview_img.replace('right','left');
			<?php endif; ?>
            if (preview_img == 'default' ) $( '#page_template_img' ).removeAttr('src');
            $( '#page_template_img' ).attr( 'src', '<?php echo $preview_img_path ?>'+preview_img);
        }
        
        // if the template preview image is not found, hide the image not found and show text
        $( '#page_template_img' ).error(function(){
           $(this).hide();  
           $( 'span', $(this).parent() ).show();
        });
        // if the template preview image is found, show the image
        $( '#page_template_img' ).load(function(){
           $(this).show();     
           $( 'span', $(this).parent() ).hide();
        });
        
        // remove the default option (because the theme overrides the template
        $( '#page_template option[value="default"]' ).remove();
        // add the theme default item
        $( '#page_template option:first' ).before( $( '<option value="default"><?php _e( 'Theme default', 'graphene' ); ?></option>' ) );
        // select the default template if it isn't already selected
        if ( $( '#page_template option[selected="selected"]' ).length == 0){
            // $( '#page_template option[text="<?php echo $default_template; ?>"]' ).attr( 'selected', 'selected' );
            $( '#page_template option:contains("<?php _e( 'Theme default', 'graphene' ); ?>")' ).attr( 'selected', 'selected' );
        }
        
        update_page_template();
    });
    //]]>
    </script>
    <?php
}
add_action( 'edit_page_form', 'graphene_page_template_visualizer' ); // only works on pages 


/**
 * Add content width parameter to the WordPress editor
 */
function graphene_editor_width( $mce_css ){
	global $content_width, $graphene_settings;
	$content_width = graphene_get_content_width();
	
	if ( ! $graphene_settings['disable_editor_style'] )
		$mce_css = str_replace( 'admin/editor.css.php', add_query_arg( 'content_width', $content_width, 'admin/editor.css.php' ), $mce_css );
	
	return $mce_css;
}
add_filter( 'mce_css', 'graphene_editor_width' );


if ( ! function_exists( 'graphene_docs_link' ) ) :
/**
 * Display a link to the documentation page
 *
 * @package Graphene
 * @since 1.9.1
 */
function graphene_docs_link( $suffix = '' ){
	$docs_uri = 'http://docs.graphene-theme.com/' . $suffix;
	?>
    	<a href="<?php echo $docs_uri; ?>" class="graphene-docs-link" title="<?php esc_attr_e( 'Learn more about this feature set', 'graphene' ); ?>" target="_blank">
        	<img src="<?php echo get_template_directory_uri(); ?>/admin/images/ico-info.png" alt="<?php esc_attr_e( 'Documentation', 'graphene' ); ?>" width="16" height="16" />
        </a>
    <?php
}
endif;