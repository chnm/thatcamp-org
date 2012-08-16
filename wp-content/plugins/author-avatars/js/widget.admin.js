jQuery(document).ready(function() {
	/* only works in Wordpress 2.8+ */
	jQuery('#widgets-right, #wp_inactive_widgets').bind('mouseover', function() {
		jQuery('.widget[id*="author_avatars"] .widget-inside:visible', this).each(function() {
			if (!jQuery(this).data('aa_form_initialised')) {
				AA_init_avatarpreview(jQuery("div.avatar_size_preview", this), jQuery('input.avatar_size_input', this));
				AA_check_sortdirection_status(this);
			}
		});
	});
});