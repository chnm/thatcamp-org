// the JS need to page avatars via ajax on shortcode

jQuery('.aa_pageList a').live('click',function(e){
	shortCodeValues.aa_page = jQuery(this).attr("id");
	jQuery(this).parents('.shortcode-author-avatars').addClass('paging');
	jQuery.post('/wp-admin/admin-ajax.php', shortCodeValues, function(response) {
		jQuery('.paging').html(response);
		jQuery('.shortcode-author-avatars').removeClass('paging');
	});
	e.preventDefault();
});
