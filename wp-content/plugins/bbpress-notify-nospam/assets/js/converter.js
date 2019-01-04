jQuery(document).ready(function($){

	// Capture the update button click
	$( "#bbpnns-convert-v1-to-v2" ).on( 'click', function(e){
		e.preventDefault();
		
		$(this).hide();
		$(".bbpnns_spinner").show();
		
		var self = this;
		$.ajax({
			method: 'POST',
			url:    ajaxurl,
			data: {
				nonce: $("#bbpnns_v2_conversion_needed").data('nonce'),
				action: 'bbpnns_update_db'
			}
		})
		.error(function(){
			$(".bbpnns_spinner").hide();
			$("#bbpnns_v2_conversion_needed").html( '<p>' + bbpnns_converter.error_message + '</p>' );
		})
		.success(function(out){

			$(".bbpnns_spinner").hide();
			
			if (out.success === true ) {
				$("#bbpnns_bbpnns_v2_conversion_needed button.notice-dismiss").show();
				$("#bbpnns_v2_conversion_needed").removeClass('error').addClass('notice notice-success').html( '<p>' + out.msg + '</p>' + '<button id="bbpnns_dismiss_v2_update" type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>' );
				
				$("#bbpnns_dismiss_v2_update").on('click', function(){
					$("#bbpnns_v2_conversion_needed").hide();
				});
			}
			else {
				$("#bbpnns_v2_conversion_needed").html( out.msg );
			}
		});
	});
	
});