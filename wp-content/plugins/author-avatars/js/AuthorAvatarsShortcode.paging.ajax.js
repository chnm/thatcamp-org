// the JS need to page avatars via ajax on shortcode

jQuery('.aa_pageList a').live('click',function(e){
	shortCodeValues.aa_page = jQuery(this).attr("id");
	jQuery.post('/wp-admin/admin-ajax.php', shortCodeValues, function(response) {
		jQuery('#'+shortCodeValues.aa_page).parents('.shortcode-author-avatars').html(response);
	});
	e.preventDefault();
});
