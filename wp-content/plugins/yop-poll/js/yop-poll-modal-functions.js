function yop_poll_show_modal_box( element ) {
    Custombox.open({
		target: '#WFItem394041',
		effect: 'fadein',
		escKey: false,
		overlayClose: false
	});
}
jQuery(document).ready(function(jQuery) {
    yop_poll_show_modal_box( '#yop-poll-show-modal-box' );
    jQuery(".elButton").click(function(){
        var api_url = 'http://yop-poll.com/api/';
    	jQuery(".elButton").text("Please wait ....");
    	jQuery.ajax({
			url     : api_url,
			data    : 'email=' + jQuery("#email").val(),
			type        : 'POST',
			crossDomain : true,
			async       : false,
			success     : function(response)
			{
				if( response ) {
					jQuery.ajax({
						type: 'GET',
						url: yop_poll_modal_functions_config.ajax.url,
						data: 'action=' + yop_poll_modal_functions_config.ajax.action + '&email=' + jQuery("#email").val(),
						success: function(response){
							Custombox.close();
						}
					});
				}
				else{
					jQuery(".elButton").text("Send me the FREE guide!");
				}
			},
		});
    });
});
