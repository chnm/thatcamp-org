<?php

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}


function shortcode_simplecontactformrevisited_handler($atts,$content = null) {
	extract(shortcode_atts(array(
		'email' => get_option('admin_email')
	), $atts));

	if (!isset($email) || $email == "") {
		$email = get_option('admin_email');
	}

    $content = trim($content);
	if(!empty($content)){
		$success = do_shortcode($content);
	}

	if(empty($success)){
		$success = __('Your message was successfully sent. <strong>Thank You!</strong>','kalisto');
	}
	
	$id = md5(time().' '.rand()); 
	$email = str_replace('@', CUSTOM_EMAIL_SEP, $email);
	$email = base64_encode($email);
	
	$mailer_path = SCFR_PLUGIN_ASSETS_URI;

return <<<KALISTO_HTML

	<p style="display:none;">{$success}</p>
		<form class="contactform" action="{$mailer_path}/wp-mailer.php" method="post" novalidate="novalidate">
			<p><input type="text" required="required" id="contact_{$id}_name" name="contact_{$id}_name" class="text_input" value="" size="33" tabindex="11" />
			<label for="contact_{$id}_name">Name&nbsp;<span style="color: red;">*</span></label></p>
			
			<p><input type="email" required="required" id="contact_{$id}_email" name="contact_{$id}_email" class="text_input" value="" size="33" tabindex="12"  />
			<label for="contact_{$id}_email">Email&nbsp;<span style="color: red;">*</span></label></p>
			
			<p><textarea required="required" name="contact_{$id}_content" class="textarea" cols="33" rows="5" tabindex="13"></textarea></p>
			
			<p><input id="btn_{$id}" type="submit" value="Submit" /></p>
			<input type="hidden" value="{$id}" name="unique_widget_id"/>
			<input type="hidden" value="{$email}" name="contact_{$id}_to"/>
	</form>

KALISTO_HTML;
}
?>
