<?php /*
Plugin Name: Custom Meta Widget
Plugin URI: http://shinraholdings.com/plugins/custom-meta-widget/
Description: Clone of the standard Meta widget with options to hide thlogin, admin, entry and comment feed, WordPress.org and /custom links.
Version: 1.5.1
Author: bitacre
Author URI: https://github.com/lmlsna
License: GPLv2
	Copyright 2018 Shinra Web Holdings (https://shinraholdings.com)
*/



/**
 * Class used to implement a Meta widget instead of extended the core Widget Class.
 *
 * @ TODO Extends WP_Widget_Meta directly?
 * @since 4.4.0
 */
class customMetaWidget extends WP_Widget
{
	var $homepage = 'http://shinraholdings.com/plugins/custom-meta-widget/';

	/**
	 * Sets up a new widget instance
 	 *
	 * Thank you to dsmiller for fixing the deprecated constructor
	 *
	 * @See https://wordpress.org/support/topic/updated-constructor?replies=4#post-8147479
 	 */
	public function __construct() {
		// set widget options
		$widget_ops = array (
			'classname' => 'customMetaWidget',
			'description' => __( 'Hide the individual log in/out, admin, feed and WordPress links', 'customMetaWidget' ),
			'customize_selective_refresh' => true
		);
		parent::__construct( 'customMetaWidget', __('Custom Meta', 'customMetaWidget'), $widget_ops );
	}


	/**
	 * Outputs the content for the current Custom Meta widget instance.
	 *
	 * @param array $args     	Display arguments ('before_title', 'after_title', 'before_widget', 'after_widget')
	 * @param array $instance	Settings for the current Custom Meta widget instance.
	 */
	public function widget( $args, $instance ) {
   		// extract( $args, EXTR_SKIP ); // extract arguments

		/** If no title, use default */
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Meta', 'customMetaWidget' );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		/** Before widgets filter */
	 	echo '<!--' . PHP_EOL . __( 'Plugin: Custom Meta Widget', 'customMetaWidget' ) . PHP_EOL .
		__( 'Plugin URL', 'customMetaWidget' ) . ': ' . $this->homepage .
		PHP_EOL . '-->' . PHP_EOL . $args['before_widget'];

		/** Title filter */
		if ( $title ) { echo $args['before_title'] . $title . $args['after_title']; }

		?>
		<ul>

		<?php if( (int) esc_attr( $instance['register'] ) === 1 ) : wp_register(); endif; ?>

		<?php if( (int) esc_attr( $instance['login'] ) === 1 ) : ?>
			<li><?php wp_loginout(); ?></li>
		<?php endif; ?>

		<?php if( (int) esc_attr( $instance['entryrss'] ) === 1 ): ?>
			<li><a href="<?php echo esc_url( get_bloginfo( 'rss2_url' ) ); ?>"><?php
			_e('Entries <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
		<?php endif; ?>


		<?php if( (int) esc_attr( $instance['commentrss'] ) === 1 ): ?>

			<li><a href="<?php echo esc_url( get_bloginfo( 'comments_rss2_url' ) ); ?>"><?php
			_e('Comments <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
		<?php endif; ?>

		<?php if( (int) esc_attr( $instance['wordpress'] ) === 1 ):
		/**
		 * Filters the "Powered by WordPress" text in the Meta widget.
		 *
		 * @since 3.6.0
		 * @since 4.9.0 Added the `$instance` parameter.
		 *
		 * @param string $title_text Default title text for the WordPress.org link.
		 * @param array  $instance   Array of settings for the current widget.
		 */
		echo apply_filters( 'widget_meta_poweredby', sprintf( '<li><a href="%s" title="%s">%s</a></li>',
			esc_url( __( 'https://wordpress.org/' ) ),
			esc_attr__( 'Powered by WordPress, state-of-the-art semantic personal publishing platform.' ),
			_x( 'WordPress.org', 'meta widget link text' )
		), $instance );
		endif;

		// @TODO Integrate this into wp_meta()
		if( (int) esc_attr( $instance['showcustom'] ) === 1 ) :
			echo ( !empty( $instance['customtext'] ) && !empty( $instance['customurl'] ) ?
				'<li><a href="' . esc_url( $instance['customurl'] ) . '">' . esc_attr( $instance['customtext'] ) . '</a></li>' :
				'<!--' . __( 'Error: "Show Custom Link" is checked, but either the text or URL for that link are not specified. The link was not displayed because it would be broken. Check the settings for your Custom Meta widget.', 'customMetaWidget' ) . '-->' );
		endif;

		if( (int) esc_attr( $instance['linklove'] ) === 1 ):
			echo '<li><a href="' . $this->homepage . '" title="' . __( 'WordPress Plugin Homepage', 'customMetaWidget' ) . '">' . __( 'Custom Meta', 'customMetaWidget' ) . '</a></li>';
		endif;

		wp_meta();

		?>
		</ul>

	<?php echo $args['after_widget'];

}


/**
 * Declare Form Input Options
 * (not part of vanilla WP_Widget class)
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

		// 1. no link and no URL
		if( empty( $instance['customtext']) && empty( $instance['customurl'] ) )
			$message = 'You have a custom link with no URL or text!';

		// 2. no link
		elseif( empty( $instance['customtext'] ) )
			$message = 'You have a custom link with no text!';
		// 3. no url
		elseif( empty( $instance['customurl' ] ) )
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
