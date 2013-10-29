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
		$extraClass = "";
		$hrefStart ="";
		$name ="";
		$bio ="";
		$style ="";
		$email ="";
		$link ="";		
		$id = ''; // get id or email

		if (!empty($atts['id'])) {
			$id = preg_replace('[^\w\.\@\-]', '', $atts['id']);
		}
		if (empty($id) && !empty($atts['email'])) {
			$id = preg_replace('[^\w\.\@\-]', '', $atts['email']);
		}
		
		// get avatar size
		$avatar_size = false;
		if (!empty($atts['avatar_size'])) {
			$avatar_size = intval($atts['avatar_size']);
		}
		 
		
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
		// is there an user link request
	
		if (!empty($atts['user_link'])
				||!empty($atts['show_biography'])
				||!empty($atts['show_postcount'])
				||!empty($atts['show_name'])
				||!empty($atts['show_email'])
			) {
		
		// try to fetch user profile
		$isUser = true;

		if (  !is_numeric($id)){
			 if ( email_exists($id) ){
				$id = email_exists($id); 
				
			 }else{
				$isUser = false; 
			 }
		}	 
			 if ($isUser){
				$all_meta_for_user = get_user_meta( $id );	 
				if (count ($all_meta_for_user) == 0){
					$isUser = false; 
				}
			 }
	

		if ($isUser)	{
			if (!empty($atts['user_link'])){  
			 		switch ($atts['user_link']) {
						case 'authorpage':
							$link = get_author_posts_url($id);
							break;
						case 'website':
							$link =  get_the_author_meta('user_url', $id);
						if (empty($link) || $link == 'http://') $link = false;
						break;
						case 'blog':
							if (AA_is_wpmu()) {
							$blog = get_active_blog_for_user($id);
							if (!empty($blog->siteurl)) $link = $blog->siteurl;
						}
							break;
						case 'bp_memberpage':
						if (function_exists('bp_core_get_user_domain')) {
							$link = bp_core_get_user_domain($id);
						}
						elseif (function_exists('bp_core_get_userurl')) { // BP versions < 1.1
							$link = bp_core_get_userurl($id);
						}
						break;
						case 'bbpress_memberpage':
							if (function_exists('bbp_get_user_profile_url')) {
								$link = bbp_get_user_profile_url( $id);
							}
								if (empty($link) || $link == 'http://') $link = false;
							break;
					}
					if ($link) $hrefStart = '<a href="'. $link .'">'; 
			}
	
			if(!empty($atts['show_name'])){
				$name = '<br />'.get_the_author_meta('display_name', $id);
				$extraClass .= ' with-name';
			}	
			
			if(!empty($atts['show_email'])){
				$userEmail = get_the_author_meta('user_email', $id);
				$email = "<div class='email'><a href='mailto:".$userEmail."''>".$userEmail."</a></div>";
				if (empty($email)) {
					$extraClass .= 'email-missing';
				}else{
					$extraClass .= ' with-email';
				}				
			}

			if(!empty($atts['show_postcount'])){
				$name .= ' ('. $postcount = get_user_postcount($id).')';
			}

			if(!empty($atts['show_bbpress_post_count'])){
				if (function_exists('bbp_get_user_topic_count_raw')) {
					$BBPRESS_postcount = bbp_get_user_topic_count_raw(  $id) + bbp_get_user_reply_count_raw( $id );
					$name .= ' ('. $postcount = $BBPRESS_postcount.')';
				}
			}
			
			if(!empty($atts['show_biography'])){
				$bio = get_the_author_meta('description', $id);
				if(!empty($atts['show_name']))$bio = '<div class="bio">'. $bio .'</div>';
				if (empty($bio)) {
					$extraClass .= 'biography-missing';
				}else{
					$extraClass .= ' with-biography';
				}
			}		
		}
			
		}
		$hrefend = '';
		if (!empty($hrefStart)) $hrefend = '</a>' ;
		if (!empty($style)) $style = ' style="'. $style .'"';
		return '<div class="shortcode-show-avatar '.$extraClass.'"'. $style .'>'.$hrefStart. $avatar .$name.$hrefend.$bio.$email.'</div>' . $content;
	}
}


	/**
	 * Returns the postcount for a given user. 
	 * On WPMU sites posts are counted from all blogs in field $blogs and summed up.
	 *
	 * @param int $user_id
	 * @return int post count
	 */
	function get_user_postcount($user_id) {	
		$total = 0;
		if (AA_is_wpmu() && !empty($this->blogs)) {
			$blogs = $this->blogs;
			// all blogs -> only search the user's blogs
			if (in_array('-1', (array)$this->blogs)) {
				$blogs = (array) $this->get_user_blogs($user_id);
			}
			foreach ($blogs as $blog_id) {
				switch_to_blog($blog_id);
				$total += count_user_posts($user_id);
	
				restore_current_blog();
			}
		}else{
			$total += count_user_posts($user_id);
		}
		
		return $total;
	}
	
	
?>
