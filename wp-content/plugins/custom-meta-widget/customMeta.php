<?php /*
Plugin Name: Custom Meta Widget
Plugin URI: http://shinraholdings.com/plugins/custom-meta-widget/
Description: Clone of the standard Meta widget with options to hide thlogin, admin, entry and comment feed, WordPress.org and /custom links.
Version: 1.4.6
Author: bitacre
Author URI: http://shinraholdings.com/
License: GPLv2
	Copyright 2013 Shinra Web Holdings (http://shinraholdings.com)
*/

/**
 * CLASS customMetaWidget 
 */
class customMetaWidget extends WP_Widget { // start of class

/**
 * Initialize Classwide Variables
 */
var $homepage = 'http://shinraholdings.com/plugins/custom-meta-widget/';

/**
 * CONSTRUCTOR
 */
function customMetaWidget() {	
	// set widget options
	$widget_ops = array ( 
		'classname' => 'customMetaWidget',
		'description' => __( 'Hide the individual log in/out, admin, feed and WordPress links', 'customMetaWidget' )
	); 
	
	// extend widget
	$this->WP_Widget( 'customMetaWidget', 'Custom Meta', $widget_ops );
}


/**
 *  Declare Form Input Options 
 * (not part of WP_Widget class)
 */
function get_options() {
	$keys = array( 'slug', 'type', 'default', 'label', 'before' );
	
	$values = array( 
		'title' => array( 'title', 'text', __( 'Meta', 'customMetaWidget' ), __( 'Title', 'customMetaWidget' ), '' ),			
		'register' => array( 'register', 'checkbox', 1, __( 'Show "Register/Admin" link?', 'customMetaWidget' ), '' ),
		'login' => array( 'login', 'checkbox', 1, __( 'Show "Log in/out" link?', 'customMetaWidget' ), '' ),
		'entryrss' => array( 'entryrss', 'checkbox', 1, __( 'Show "Entries RSS" link?', 'customMetaWidget' ), '' ),
		'commentrss' => array( 'commentrss', 'checkbox', 1, __( 'Show "Comments RSS" link?', 'customMetaWidget' ), '' ),
		'wordpress' => array( 'wordpress', 'checkbox', 1, __( 'Show "WordPress.org" link?', 'customMetaWidget' ), '' ),
		'showcustom' => array( 'showcustom', 'checkbox', 0, __( 'Show the custom link?', 'customMetaWidget' ), 'before' => '' ),
		'customurl' => array( 'customurl', 'text', '', __( 'URL', 'customMetaWidget' ), ' style="margin-left:20px;"' ),
		'customtext' => array( 'customtext', 'text', '', __( 'Text', 'customMetaWidget' ), ' style="margin-left:20px;"' ),
		'linklove' => array( 'linklove', 'checkbox', 0, '<small>' . __( 'An awesome way to support this free plugin!', 'customMetaWidget' ) . '</small>', '' )
	);
	
	// build into multi-array
	$options = array();
	foreach( $values as $slug => $sub_values ) {
		$temp = array();
		for( $i=0; $i<5; $i++ )
			$temp[$keys[$i]] = $sub_values[$i];
		$options[$slug] = $temp;
	} 
	return $options;
}


/**
 * Declare Form Input Defaults
 * (not part of WP_Widget Class)
 */
function get_defaults() {
	// create container and loop
	$defaults = array(); 
	foreach( $this->get_options() as $key => $value )
		$defaults[$key] = $value['default'];
	return $defaults;
}


/**
 * Declare Form Input Keys
 * (not part of WP_Widget Class)
 */
function get_keys() {
	// create container and loop
	$keys = array(); 
	foreach( $this->get_options() as $key => $value )
			$keys[] = $key;
	return $keys;
}


/**
 * Draw Widget Options
 */
function form( $instance ) { 
	// parse instance values over defaults
	$instance = wp_parse_args( ( array ) $instance, $this->get_defaults() ); 

	// loop through input option
	foreach( $this->get_options() as $slug => $value ) :
		extract( $value );
		$id = $this->get_field_id( $slug );
		$name = $this->get_field_name( $slug );
		if( $type == 'text' ) {
			$value = $instance[$slug];
			$checked = '';
			$label = $label . ': ';
		} else {
			$checked = checked( $instance[$slug], 1, false );
			$value = 1;
		}
		$label_tag = '<label style="margin:0 3px;" for="' . $id . '">' . $label . '</label>'; 
		?>

        
	<!-- <?php echo $slug; ?> -->
    
	<p<?php echo $before; ?>><?php echo ( $type == 'text' ? $label_tag : '' ); ?><input class="<?php echo ( $type == 'text' ? 'widefat' : 'check' ); ?>" id="<?php echo $id; ?>" name="<?php echo $name; ?>" type="<?php echo $type; ?>" value="<?php echo $value; ?>" <?php echo $checked; ?>/><?php echo ( $type == 'checkbox' ? $label_tag : '' ); ?></p>
    
	<?php endforeach; ?>

	

	<?php // check for errors
	if( $instance['showcustom'] ) { // IF 'showcustom' is checked, AND
			
		if( empty( $instance['customtext']) && empty( $instance['customurl'] ) ) // 1. no link and no URL
			$message = 'You have a custom link with no URL or text!';
		
		elseif( empty( $instance['customtext'] ) ) // 2. no link
			$message = 'You have a custom link with no text!';
		
		elseif( empty( $instance['customurl' ] ) ) // 3. no url
			$message = 'You have a custom link with no URL!';
		
	}
	if( isset( $message ) ) // set message (or don't)
		echo '<p style="color:#f00; font-weight:bold;" >' . __( $message, 'customMetaWidget' ) . '</p>';
}


/**
 * SAVE WIDGET OPTIONS 
 */
function update( $new_instance, $old_instance) {
	$instance = $old_instance; // move over unchanged
	
	foreach( $this->get_keys() as $key ) // parse new values over
		$instance[$key] = $new_instance[$key];

	return $instance;
}

// ACTUAL WIDGET OUTPUT
function widget( $args, $instance ) { 
   	extract( $args, EXTR_SKIP ); // extract arguments
	$title = empty( $instance['title'] ) ? __( 'Meta', 'customMetaWidget' ) : apply_filters( 'widget_title', $instance['title'] ); // if no title, use default ?>
    
<!-- 
	<?php _e( 'Plugin: Custom Meta Widget', 'customMetaWidget' ); ?>
	<?php _e( 'Plugin URL', 'customMetaWidget' ); ?>: <?php echo $this->homepage; ?>
-->


	<?php echo $before_widget; // (from theme) ?>
	<?php echo $before_title . esc_attr( $instance['title'] ) . $after_title; ?>
	<ul>
	
	
    <?php // ADD LINKS
	$content = array(
		'register' => wp_register( '<li>', '</li>', false ),
		
		'login' => '<li>' . wp_loginout( NULL, false ) . '</li>',
		
		'entryrss' => sprintf( __( '%1$sSyndicate this site using RSS 2.0%2$sEntries %3$sRSS%4$s', 'customMetaWidget' ), 
			'<li><a href="' . get_bloginfo( 'rss2_url' ) . '" title="', '">', 
			'<abbr title="' . __( 'Really Simple Syndication', 'customMetaWidget' ) . '">', '</abbr></a></li>' ),
			
		'commentrss' => sprintf( __( '%1$sSyndicate this site using RSS 2.0%2$sComments %3$sRSS%4$s', 'customMetaWidget' ),
			'<li><a href="' . get_bloginfo( 'comments_rss2_url' ) . '" title="', '">',
			'<abbr title="' . __( 'Really Simple Syndication', 'customMetaWidget' ) . '">', '</abbr></a></li>' ),
			
		'wordpress' => '<li><a href="http://wordpress.org/" title="' . 
			__( 'Powered by WordPress, state-of-the-art semantic personal publishing platform.', 'customMetaWidget' ) . 
			'">WordPress.org</a></li>', 
	
		'showcustom' => ( !empty( $instance['customtext'] ) && !empty( $instance['customurl'] ) ? 
			'<li><a href="' . esc_url( $instance['customurl'] ) . '">' . esc_attr( $instance['customtext'] ) . '</a></li>' :
		'<!--' . __( 'Error: "Show Custom Link" is checked, but either the text or URL for that link are not specified. The link was not displayed because it would be broken. Check the settings for your Custom Meta widget.', 'customMetaWidget' ) . '-->' ),

		'linklove' => '<li><a href="' . $this->homepage . '" title="' . __( 'WordPress Plugin Homepage', 'customMetaWidget' ) . '">' . __( 'Custom Meta', 'customMetaWidget' ) . '</a></li>' 
	
	);
	

	foreach( $content as $checked => $output )
		if( (int) esc_attr( $instance[$checked] ) === 1 ) echo $output; ?>
	
	</ul>
    
	<?php echo $after_widget;

}

} // end class 


/**
 * Unregister WP_Widget_Meta
 */
function customMetaWidget_swap() {
	unregister_widget( 'WP_Widget_Meta' );
	register_widget( 'customMetaWidget' );
} add_action( 'widgets_init', 'customMetaWidget_swap' ); // hook


/**
 * Load TextDomain
 */
function customMetaWidget_i18n() {
	load_plugin_textdomain( 'customMetaWidget', NULL, trailingslashit( basename( dirname(__FILE__) ) ) . 'lang' );
} add_action( 'plugins_loaded', 'customMetaWidget_i18n' ); // hook

?>