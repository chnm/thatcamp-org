jQuery(document).ready(function(jQuery) {
	jQuery('#yop-poll-edit-add-new-template-form-save, #yop-poll-edit-add-new-template-form-save1').click( function() {
		jQuery.ajax({
			type: 'POST', 
			url: yop_poll_add_new_template_config.ajax.url,
			data: 'action='+yop_poll_add_new_template_config.ajax.action+'&'+jQuery( "#yop-poll-edit-add-new-template-form" ).serialize(),
			cache: false,
			beforeSend: function() {
				jQuery('html, body').animate({scrollTop: '0px'}, 800);
				jQuery('#message').html('<p>' + yop_poll_add_new_template_config.ajax.beforeSendMessage + '</p>');
				jQuery("#message").removeClass();
				jQuery('#message').addClass('updated');
				jQuery('#message').show();  								
			},
			error: function() {
				jQuery('html, body').animate({scrollTop: '0px'}, 800);
				jQuery('#message').html('<p>' + yop_poll_add_new_template_config.ajax.errorMessage + '</p>');
				jQuery("#message").removeClass();
				jQuery('#message').addClass('error');
				jQuery('#message').show();
			}, 
			success: 
			function( data ){
				jQuery('html, body').animate({scrollTop: '0px'}, 800);
				jQuery('#message').html('<p>' + data + '</p>');
				jQuery("#message").removeClass();
				jQuery('#message').addClass('updated');
				jQuery('#message').show();
			}
		});
	});

	jQuery('#yop-poll-template-before-start-date-handler').click( function() {
		jQuery('#yop-poll-template-before-start-date-div').children('.inside').toggle('medium');
	});		
	jQuery('#yop-poll-template-after-end-date-handler').click( function() {
		jQuery('#yop-poll-template-after-end-date-div').children('.inside').toggle('medium');
	});
	jQuery('#yop-poll-template-css-handler').click( function() {
		jQuery('#yop-poll-template-css-div').children('.inside').toggle('medium');
	});
	jQuery('#yop-poll-template-js-handler').click( function() {
		jQuery('#yop-poll-template-js-div').children('.inside').toggle('medium');
	});
});

function yop_poll_reset_template() {
	//jQuery('#yop-poll-edit-add-new-template-form-reset').click( function() {
	jQuery.ajax({
		type: 'POST', 
		url: yop_poll_add_new_template_config.ajax.url,
		data: 'action='+yop_poll_add_new_template_config.ajax.reset_action+'&'+jQuery( "#yop-poll-edit-add-new-template-form" ).serialize(),
		cache: false,
		beforeSend: function() {
			jQuery('html, body').animate({scrollTop: '0px'}, 800);
			jQuery('#message').html('<p>' + yop_poll_add_new_template_config.ajax.beforeSendMessage + '</p>');
			jQuery("#message").removeClass();
			jQuery('#message').addClass('updated');
			jQuery('#message').show();  								
		},
		error: function() {
			jQuery('html, body').animate({scrollTop: '0px'}, 800);
			jQuery('#message').html('<p>' + yop_poll_add_new_template_config.ajax.errorMessage + '</p>');
			jQuery("#message").removeClass();
			jQuery('#message').addClass('error');
			jQuery('#message').show();
		}, 
		success: 
		function( data ){
			jQuery('html, body').animate({scrollTop: '0px'}, 800);
			jQuery('#message').html('<p>' + data + '</p>');
			jQuery("#message").removeClass();
			jQuery('#message').addClass('updated');
			jQuery('#message').show();
			setTimeout('location.reload();', 2000 );
		}
	});
};

function yop_poll_do_change_template_author( template_id ) {
	jQuery.ajax({
		type: 'POST',
		url: yop_poll_add_new_template_config.ajax.url,
		data: 'action=yop_poll_do_change_template_author'+'&'+jQuery( "#yop-poll-change-template-author-form" ).serialize(),
		cache: false,
		beforeSend: function() {
			jQuery('#yop-poll-change-template-author-error').html('<p>' + yop_poll_add_new_template_config.ajax.beforeSendMessage + '</p>');
		},
		error: function() {
			jQuery('#yop-poll-change-template-author-error').html('<p>' + yop_poll_add_new_template_config.ajax.errorMessage + '</p>');
		},
		success:
		function( data ){
			data = yop_poll_extractApiResponse( data );
			jQuery('#yop-poll-change-template-author-error').html('<p>' + data + '</p>');
			jQuery('#yop-poll-change-template-author-container-' + template_id).html( '<b>' + jQuery('#yop-poll-template-author-select option[value='+jQuery('#yop-poll-template-author-select').val()+']' ).text() + '</b>' );
		}
	});
}

function yop_poll_show_change_template_author( template_id ) {
	jQuery.fn.modalBox({
		directCall: {
			source : yop_poll_add_new_template_config.ajax.url + '?action=yop_poll_show_change_template_author&template_id=' + template_id
		},
		disablingTheOverlayClickToClose : true
	});
	return false;
}

function yop_poll_extractApiResponse( str ) {
	var patt	= /\[response\](.*)\[\/response\]/m;
	resp 		= str.match( patt )
	return resp[1];
}		