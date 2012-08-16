<?php
/*
Copyright (C) 2011  Alexander Zagniotov

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>. 
*/

if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}



class SimpleContactFormRevisited_Widget extends WP_Widget {

	var $maindesc = "A simple, yet elegant email contact form plugin that installs as a widget. The contact form widget is using jQuery for validation, and makes it difficult for email bots to harvest your email address by encrypting it. This sidebar widget is particularly useful when you want to allow your visitors to contact you without forcing them to navigate away from the current page.";

	function SimpleContactFormRevisited_Widget() {

		$widget_ops = array('classname' => 'simplecontactformrevisited_widget', 'description' => __( $this->maindesc, 'kalisto') );
		$this->WP_Widget('simplecontactformrevisited', __('AZ :: Contact Form', 'kalisto'), $widget_ops);
		
		if ( is_active_widget(false, false, $this->id_base) ){
				add_action( 'wp_print_scripts', array(&$this, 'add_script') );
				add_action( 'wp_print_styles', array(&$this, 'add_style') );

		}
	}
	
	function add_script(){

		wp_register_script('jquery-validator', SCFR_PLUGIN_JS .'/jquery.tools.min.js', array('jquery'), '1.2.5', true);
		wp_enqueue_script( 'jquery-validator');
	
		wp_register_script( 'simple-form-revisited-plugin-init', SCFR_PLUGIN_JS .'/simple-contact-form-revisited-plugin.js', array('jquery', 'jquery-validator'), SCFR_VERSION, true);
		wp_enqueue_script( 'simple-form-revisited-plugin-init');
	}

	function add_style() {
		wp_enqueue_style('simple-form-revisited-plugin-style', SCFR_PLUGIN_CSS . '/style.css', false, SCFR_VERSION, "screen");
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Contact Me', 'kalisto') : $instance['title'], $instance, $this->id_base);
		$email= $instance['email'];
		$email = str_replace('@', CUSTOM_EMAIL_SEP, $email);
		$email = base64_encode($email);


		if(empty($success)){
			$success = __('Your message was successfully sent. <br /><strong>Thank You!</strong>','kalisto');
		}
		
		echo $before_widget;
		if ( $title) {
			echo $before_title . $title . $after_title;
		}
	
	
		$id = md5(time().' '.rand()); 
		?>
		
		<p style="display:none;"><?php _e('Your message was successfully sent. <br /><strong>Thank You!</strong>','kalisto');?></p>
		<form class="contactform" action="<?php echo SCFR_PLUGIN_ASSETS_URI;?>/wp-mailer.php" method="post" novalidate="novalidate">
			<p><input type="text" required="required" id="contact_<?php echo $id;?>_name" name="contact_<?php echo $id;?>_name" class="text_input" value="" size="33" tabindex="11" />
			<label for="contact_<?php echo $id;?>_name"><?php _e('Name', 'kalisto'); ?>&nbsp;<span style="color: red;">*</span></label></p>
			
			<p><input type="email" required="required" id="contact_<?php echo $id;?>_email" name="contact_<?php echo $id;?>_email" class="text_input" value="" size="33" tabindex="12"  />
			<label for="contact_<?php echo $id;?>_email"><?php _e('Email', 'kalisto'); ?>&nbsp;<span style="color: red;">*</span></label></p>
			
			<p><textarea required="required" name="contact_<?php echo $id;?>_content" class="textarea" cols="33" rows="5" tabindex="13"></textarea></p>
			
			<p><input id="btn_<?php echo $id;?>" type="submit" value="<?php _e('Submit', 'kalisto'); ?>" /></p>
			<input type="hidden" value="<?php echo $id;?>" name="unique_widget_id"/>
			<input type="hidden" value="<?php echo $email;?>" name="contact_<?php echo $id;?>_to"/>
		</form>	
	
		<?php
		echo $after_widget;

	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['email'] = strip_tags($new_instance['email']);

		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$email = isset($instance['email']) ? esc_attr($instance['email']) : get_option('admin_email');
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'kalisto'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('email'); ?>"><?php _e('Your Email:', 'kalisto'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('email'); ?>" name="<?php echo $this->get_field_name('email'); ?>" type="text" value="<?php echo $email; ?>" /></p>
		
<?php
	}
}

