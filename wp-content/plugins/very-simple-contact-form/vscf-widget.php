<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class vscf_widget extends WP_Widget {
	// constructor
	public function __construct() {
		$widget_ops = array( 'classname' => 'vscf-widget', 'description' => __('Display your contact form in a widget.', 'very-simple-contact-form') );
		parent::__construct( 'vscf-widget', __('Very Simple Contact Form', 'very-simple-contact-form'), $widget_ops );
	}

	// set widget and title in dashboard
	function form( $instance ) {
		$instance = wp_parse_args( $instance, array(
			'title' => '',
			'text' => '',
			'attributes' => ''
		));
		$title = !empty( $instance['title'] ) ? $instance['title'] : __('Very Simple Contact Form', 'very-simple-contact-form');
		$text = $instance['text'];
		$attributes = $instance['attributes'];
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title', 'very-simple-contact-form'); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" maxlength='50' value="<?php echo esc_attr( $title ); ?>">
 		</p>
		<p>
		<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text above form', 'very-simple-contact-form'); ?>:</label>
		<textarea class="widefat monospace" rows="6" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo wp_kses_post( $text ); ?></textarea>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'attributes' ); ?>"><?php _e('Attributes', 'very-simple-contact-form'); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'attributes' ); ?>" name="<?php echo $this->get_field_name( 'attributes' ); ?>" type="text" maxlength='200' value="<?php echo esc_attr( $attributes ); ?>">
 		</p>
		<?php $link_label = esc_attr__( 'click here', 'very-simple-contact-form' ); ?>
		<?php $link_wp = '<a href="https://wordpress.org/plugins/very-simple-contact-form" target="_blank">'.$link_label.'</a>'; ?>
		<?php $link_settings = '<a href="'.admin_url( 'options-general.php?page=vscf' ).'">'.$link_label.'</a>'; ?>
		<p><?php printf( esc_attr__( 'For info, available attributes and support %s.', 'very-simple-contact-form' ), $link_wp ); ?></p>
		<p><?php printf( esc_attr__( 'For plugin settings %s.', 'very-simple-contact-form' ), $link_settings ); ?></p>
		<?php
	}

	// update widget
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// sanitize content
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['text'] = wp_kses_post( $new_instance['text'] );
		$instance['attributes'] = strip_tags( $new_instance['attributes'] );

		return $instance;
	}

	// display widget with form in frontend
	function widget( $args, $instance ) {
		echo $args['before_widget'];

		if ( !empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		if ( !empty( $instance['text'] ) ) {
			echo '<div class="vscf-widget-text">'.wpautop( wp_kses_post($instance['text']).'</div>');
		}

		$content = '[contact-widget ';
		if ( !empty( $instance['attributes'] ) ) {
			$content .= $instance['attributes'];
		}
		$content .= ']';
		echo do_shortcode( $content );

		echo $args['after_widget'];
	}
}
