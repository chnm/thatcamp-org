<?php

// vaguely based on code by MK Safi
// http://msafi.com/fix-yet-another-related-posts-plugin-yarpp-widget-and-add-it-to-the-sidebar/
class YARPP_Widget extends WP_Widget {
	function YARPP_Widget() {
		parent::WP_Widget(false, $name = __('Related Posts (YARPP)','yarpp'));
	}

	function widget($args, $instance) {
		global $yarpp;
		if ( !is_singular() )
			return;

		extract($args);

		// compatibility with pre-3.5 settings:
		if ( isset($instance['use_template']) )
			$instance['template'] = $instance['use_template'] ? $instance['template_file'] : false;

		if ( $yarpp->get_option('cross_relate') )
			$instance['post_type'] = $yarpp->get_post_types();
		else if ( 'page' == get_post_type() )
			$instance['post_type'] = array( 'page' );
		else
			$instance['post_type'] = array( 'post' );

		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		if ( !$instance['template'] ) {
			echo $before_title;
			echo $title;
			echo $after_title;
		}

		$instance['domain'] = 'widget';
		$yarpp->display_related(null, $instance, true);
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = array(
			'promote_yarpp' => isset($new_instance['promote_yarpp']),
			'template' => isset($new_instance['use_template']) ? $new_instance['template_file'] : false
		);

		if ( !!$instance['template'] ) // don't save the title change.
			$instance['title'] = $old_instance['title'];
		else // save the title change:
			$instance['title'] = $new_instance['title'];
		
		return $instance;
	}

	function form($instance) {
		global $yarpp;
	
		$instance = wp_parse_args( $instance, array(
			'title' => __('Related Posts (YARPP)','yarpp'),
			'template' => false,
			'promote_yarpp' => false
		) );
	
		// compatibility with pre-3.5 settings:
		if ( isset($instance['use_template']) )
			$instance['template'] = $instance['template_file'];
	
		?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" /></label></p>

		<?php // if there are YARPP templates installed...
			$templates = $yarpp->admin->get_templates();
			if ( count($templates) ): ?>

			<p><input class="checkbox" id="<?php echo $this->get_field_id('use_template'); ?>" name="<?php echo $this->get_field_name('use_template'); ?>" type="checkbox" <?php checked(!!$instance['template']) ?> /> <label for="<?php echo $this->get_field_id('use_template'); ?>"><?php _e("Display using a custom template file",'yarpp');?></label></p>
			<p id="<?php echo $this->get_field_id('template_file_p'); ?>"><label for="<?php echo $this->get_field_id('template_file'); ?>"><?php _e("Template file:",'yarpp');?></label> <select name="<?php echo $this->get_field_name('template_file'); ?>" id="<?php echo $this->get_field_id('template_file'); ?>">
				<?php foreach ($templates as $template): ?>
				<option value='<?php echo esc_attr($template); ?>'<?php selected($template, $instance['template']);?>><?php echo esc_html($template); ?></option>
				<?php endforeach; ?>
			</select><p>
			<script type="text/javascript">
			jQuery(function($) {
				function ensureTemplateChoice() {
					if ($('#<?php echo $this->get_field_id('use_template'); ?>').attr('checked')) {
						$('#<?php echo $this->get_field_id('title'); ?>').attr('disabled',true);
						$('#<?php echo $this->get_field_id('template_file_p'); ?>').show();
					} else {
						$('#<?php echo $this->get_field_id('title'); ?>').attr('disabled',false);
						$('#<?php echo $this->get_field_id('template_file_p'); ?>').hide();
					}
				}
				$('#<?php echo $this->get_field_id('use_template'); ?>').change(ensureTemplateChoice);
				ensureTemplateChoice();
			});
			</script>
		<?php endif; ?>

		<p><input class="checkbox" id="<?php echo $this->get_field_id('promote_yarpp'); ?>" name="<?php echo $this->get_field_name('promote_yarpp'); ?>" type="checkbox" <?php checked($instance['promote_yarpp']) ?> /> <label for="<?php echo $this->get_field_id('promote_yarpp'); ?>"><?php _e("Help promote Yet Another Related Posts Plugin?",'yarpp'); ?></label></p>
		<?php
	}
}
// new in 2.0: add as a widget
function yarpp_widget_init() {
	register_widget( 'YARPP_Widget' );
}
add_action( 'widgets_init', 'yarpp_widget_init' );
