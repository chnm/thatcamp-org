<?php

/**
 * Show Avatar Shortcode: provides a shortcode for displaying avatars for any email address/userid
 */
class ShowAvatarShortcode {

	/**
	 * Constructor
	 */
	function ShowAvatarShortcode() {
		$this->register();
	}
	
	/**
	 * register shortcode 
	 */
	function register() {	
		add_shortcode('show_avatar', array($this, 'shortcode_handler'));
	}
		
	/**
	 * The shortcode handler for the [show_avatar] shortcode.
	 * 
	 * Example: [show_avatar id=pbearne@tycoelectronics.com avatar_size=30 align=right]
	 */	
	function shortcode_handler($atts, $content=null) {
	
		// get id or email
		$id = '';
		if (!empty($atts['id'])) {
			$id = preg_replace('[^\w\.\@\-]', '', $atts['id']);
		}
		if (empty($id) && !empty($atts['email'])) {
			$id = preg_replace('[^\w\.\@\-]', '', $atts['email']);
		}
		
		// get avatar size
		if (!empty($atts['avatar_size'])) {
			$avatar_size = intval($atts['avatar_size']);
		}
		if (!$avatar_size) $avatar_size = false;
		
		// get alignment
		if (!empty($atts['align'])) {
			switch ($atts['align']) {
				case 'left':
					$style = "float: left; margin-right: 10px;";
					break;
				case 'right':
					$style = "float: right; margin-left: 10px;";
					break;
				case 'center':
					$style = "text-align: center; width: 100%;";
					break;
			}
		}
		
		if (!empty($id)) {
			$avatar = get_avatar($id, $avatar_size);
		}
		else {
			$avatar = __("[show_author shortcode: please set id/email attribute]");
		}
	
		if (!empty($style)) $style = ' style="'. $style .'"';
		return '<div class="shortcode-show-avatar"'. $style .'>'. $avatar .'</div>' . $content;
	}
}

?>