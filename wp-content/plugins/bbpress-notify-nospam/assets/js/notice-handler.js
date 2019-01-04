/**
 * bbpnns notice handler
 */
jQuery(document).ready(function($){
	
	// Dismiss notice
	$("div.bbpnns-admin-notice button.notice-dismiss").on('click', function(){
		var notice_id = $(this).parents('div.bbpnns-admin-notice').attr('id');
		
		$.ajax({
			url: ajaxurl,
			method: 'POST',
			data: {
				action: 'bbpnns-notice-handler',
				notice_id: notice_id,
				nonce: $(this).parents('div.bbpnns-admin-notice').data('nonce')
			}
		});
	});
});