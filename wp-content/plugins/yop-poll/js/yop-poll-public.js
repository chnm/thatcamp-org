var write_console	= false;
function cslw ( msg ) {
	if ( write_console ) {
		if( console && console.log ) {
			console.log( msg );
		}
	}
} 

function yop_poll_runEval( poll_id, unique_id ) {
	eval("if(typeof window.strip_results_" + poll_id + unique_id + " == 'function')  strip_results_"+poll_id + unique_id +"();");
	eval("if(typeof window.tabulate_answers_" + poll_id + unique_id + " == 'function')  tabulate_answers_"+poll_id + unique_id +"();")
	eval("if(typeof window.tabulate_results_" + poll_id + unique_id + " == 'function')    tabulate_results_"+poll_id + unique_id +"(); ") 
	eval("if(typeof runOnPollStateChange_" + poll_id + unique_id + " == 'function') runOnPollStateChange_" +poll_id + unique_id +"(); ")
}

function yop_poll_show_loading( target, loading_img_id, yop_poll_public_config  ) {
	jQuery( '#' + target ).hide();
	var target_loading_image	= document.createElement('img');
	target_loading_image.src	= yop_poll_public_config.loading_image_src;
	target_loading_image.alt	= yop_poll_public_config.loading_image_alt;
	target_loading_image.id		= loading_img_id;
	jQuery( '#' + target ).after( target_loading_image );
}

function yop_poll_hide_loading( target, loading_img_id ) {
	jQuery( '#' + loading_img_id ).remove();
	jQuery( '#' + target ).show();
}

function yop_poll_get_vote_options_number( yop_poll_public_config ) {
	switch( yop_poll_public_config.poll_options.vote_permisions_types ) {
		case 1:
		case 2:
		case 4:
			return 'single';
			break;
		case 3:
		case 5:
		case 6:
		case 7:
			return 'multiple';
			break;
	}	
	return 'default';
}

function yop_poll_base64_encode( str ) {
	str	= jQuery.base64.encode( str );
	str	= str.replace( '/', '-' );
	str	= str.replace( '+', '_' );
	return str;
}

function yop_poll_various_config_to_get_params( yop_poll_various_config, is_share ) {
	var params	= '';
	if ( typeof is_share !== 'undefined' )
		is_share	= 'yes'
	else
		is_share	= 'no'

	if ( typeof yop_poll_various_config.poll_id !== 'undefined' )
		params		+= '&poll_id=' + yop_poll_base64_encode( yop_poll_various_config.poll_id );	
	if ( typeof yop_poll_various_config.is_modal !== 'undefined' )
		params		+= '&is_modal=' + yop_poll_base64_encode( yop_poll_various_config.is_modal );
	if ( typeof yop_poll_various_config.vote_loading_image_target !== 'undefined' )	
		params		+= '&vote_loading_image_target=' + yop_poll_base64_encode( yop_poll_various_config.vote_loading_image_target );
	if ( typeof yop_poll_various_config.vote_loading_image_id !== 'undefined' )	
		params		+= '&vote_loading_image_id=' + yop_poll_base64_encode( yop_poll_various_config.vote_loading_image_id );
	if ( typeof yop_poll_various_config.vote_loading_image_alt !== 'undefined' )	
		params		+= '&vote_loading_image_alt=' + yop_poll_base64_encode( yop_poll_various_config.vote_loading_image_alt );
	if ( typeof yop_poll_various_config.vote_type !== 'undefined' )	
		params		+= '&vote_type=' + yop_poll_base64_encode( yop_poll_various_config.vote_type );
	if ( typeof yop_poll_various_config.vote_with_facebook_ajax_url !== 'undefined' )
		params		+= '&vote_with_facebook_ajax_url=' + yop_poll_base64_encode( yop_poll_various_config.vote_with_facebook_ajax_url );
	if ( typeof yop_poll_various_config.unique_id !== 'undefined' )
		params		+= '&unique_id=' + yop_poll_base64_encode( yop_poll_various_config.unique_id );
	if ( typeof yop_poll_various_config.poll_location !== 'undefined' )
		params		+= '&poll_location=' + yop_poll_base64_encode( yop_poll_various_config.poll_location );

	if ( 'yes' == is_share ) {
		if ( typeof yop_poll_various_config.public_config.poll_options.share_name !== 'undefined' )
			params		+= '&share_name=' + yop_poll_base64_encode( yop_poll_various_config.public_config.poll_options.share_name );		
		if ( typeof yop_poll_various_config.public_config.poll_options.share_caption !== 'undefined' )
			params		+= '&share_caption=' + yop_poll_base64_encode( yop_poll_various_config.public_config.poll_options.share_caption );
		if ( typeof yop_poll_various_config.public_config.poll_options.share_description !== 'undefined' )
			params		+= '&share_description=' + yop_poll_base64_encode( yop_poll_various_config.public_config.poll_options.share_description );
		if ( typeof yop_poll_various_config.public_config.poll_options.share_picture !== 'undefined' )
			params		+= '&share_picture=' + yop_poll_base64_encode( yop_poll_various_config.public_config.poll_options.share_picture );	
		if ( typeof yop_poll_various_config.public_config.poll_options.share_link !== 'undefined' )
			params		+= '&share_link=' + yop_poll_base64_encode( yop_poll_various_config.public_config.poll_options.share_link );	
		if ( typeof yop_poll_various_config.public_config.poll_options.share_question !== 'undefined' )
			params		+= '&share_question=' + yop_poll_base64_encode( yop_poll_various_config.public_config.poll_options.share_question );
		if ( typeof yop_poll_various_config.public_config.poll_options.share_poll_name !== 'undefined' )
			params		+= '&share_poll_name=' + yop_poll_base64_encode( yop_poll_various_config.public_config.poll_options.share_poll_name );

		params		+= '&share_answer=' + yop_poll_base64_encode( get_form_answers( yop_poll_various_config ) );
	}	

	return params;	
} 

function get_form_answers( yop_poll_various_config ) {
	var answer_params	= '';
	poll_id	= typeof yop_poll_various_config.poll_id  !== 'undefined' ? yop_poll_various_config.poll_id  : 0;
	cslw( yop_poll_various_config.public_config.poll_options.answers );
	jQuery.each( yop_poll_various_config.public_config.poll_options.answers, function( i, answer ) {
			if ( jQuery('#yop-poll-answer-' + answer.id ).is(':checked') ) {
				if ( 'other' == answer.type )				 
					answer_params	+= jQuery('#yop-poll-other-answer-' + answer.id ).val() + ', ';
				else
					answer_params	+= answer.value + ', ';
			}
	} );
	return answer_params;	
}

function yop_poll_register_vote( poll_id, poll_location, unique_id ) {
	var yop_poll_public_config							= window['yop_poll_public_config_' + poll_id + unique_id ];
	var yop_poll_various_config 						= new Object();
	yop_poll_various_config.poll_id						= poll_id;
	yop_poll_various_config.poll_location				= poll_location;
	yop_poll_various_config.unique_id					= unique_id;
	yop_poll_various_config.is_modal					= 0;
	yop_poll_various_config.vote_loading_image_target	= 'yop_poll_vote-button-'+ poll_id + unique_id;
	yop_poll_various_config.vote_loading_image_id		= 'yop_poll_vote_button_loading_img-'+ poll_id + unique_id;
	yop_poll_various_config.vote_loading_image_alt		= yop_poll_public_config.loading_image_alt;
	yop_poll_various_config.vote_type					= 'default';
	yop_poll_various_config.public_config				= yop_poll_public_config;

	if ( yop_poll_public_config.poll_options.vote_permisions != 'quest-only' ) {
		switch ( yop_poll_get_vote_options_number( yop_poll_public_config ) ) {
			case 'single':
			yop_poll_show_loading( yop_poll_various_config.vote_loading_image_target, yop_poll_various_config.vote_loading_image_id , yop_poll_public_config );
			switch( yop_poll_public_config.poll_options.vote_permisions_types ) {
				case 1:
					yop_poll_vote_with_wordpress( yop_poll_various_config ); 
					break;
				case 2:
					yop_poll_various_config.vote_type	=  'anonymous';
					yop_poll_do_vote( yop_poll_various_config );
					break;
				case 4:
					yop_poll_vote_with_facebook( yop_poll_various_config );
					break;
			}                                     
			break;
			case 'multiple':
				yop_poll_show_multiple_vote_options( yop_poll_various_config );
				return;
				break;
			default:
				yop_poll_show_loading( yop_poll_various_config.vote_loading_image_target, yop_poll_various_config.vote_loading_image_id , yop_poll_public_config );
				yop_poll_do_vote( yop_poll_various_config );
				break;	
		}
	}
	else {
		yop_poll_show_loading( yop_poll_various_config.vote_loading_image_target, yop_poll_various_config.vote_loading_image_id , yop_poll_public_config );
		yop_poll_do_vote( yop_poll_various_config );
	}
	yop_poll_hide_loading( yop_poll_various_config.vote_loading_image_target, yop_poll_various_config.vote_loading_image_id );
}

function yop_poll_vote_with_facebook( yop_poll_various_config ) {
	yop_poll_various_config.vote_type		= 'facebook';  
	yop_poll_various_config.vote_with_facebook_ajax_url	= yop_poll_public_config_general.vote_with_facebook_ajax_url;  

	jQuery('#yop_poll_vote-button-' + yop_poll_various_config.poll_id + yop_poll_various_config.unique_id ).popupWindow({
			windowURL: yop_poll_public_config_general.pro.api_server_url + '/api/facebook/login?' + 'api_key='+yop_poll_public_config_general.pro.api_key + '&' + yop_poll_various_config_to_get_params( yop_poll_various_config ),
			windowName:'yop_poll_popup_window',
			height:200,
			left:0,
			location:0,
			menubar:0,
			resizable:0,
			scrollbars:1,
			status:0,
			width:450,
			top:0,
			toolbar:0,
			centerScreen:1
	});  
	yop_poll_hide_loading( yop_poll_various_config.vote_loading_image_target, yop_poll_various_config.vote_loading_image_id ); 
}

function yop_poll_vote_with_wordpress( yop_poll_various_config ) {
	yop_poll_various_config.vote_type	= 'wordpress';

	jQuery.ajax({
			type: 'POST',
			url: yop_poll_public_config_general.ajax.url,
			data: 'action='+yop_poll_public_config_general.ajax.is_wordpress_user_action,
			cache: false,
			async: false,
			error: function() {
				alert('An error has occured!');
				yop_poll_hide_loading( yop_poll_various_config.vote_loading_image_target, yop_poll_various_config.vote_loading_image_id );
			},
			success:
			function( data ){
				data		= yop_poll_extractApiResponse( data );
				response	= JSON.parse(data);
				if ( response == true ) {
					yop_poll_do_vote( yop_poll_various_config );
				}
				else {
					jQuery('#yop_poll_vote-button-' + yop_poll_various_config.poll_id + yop_poll_various_config.unique_id ).popupWindow({
							windowURL: yop_poll_public_config_general.vote_with_wordpress_login_url + yop_poll_urlencode( yop_poll_various_config_to_get_params( yop_poll_various_config ) ),
							windowName:'yop_poll_popup_window',
							height:500,
							left:0,
							location:0,
							menubar:0,
							resizable:0,
							scrollbars:1,
							status:0,
							width:450,
							top:0,
							toolbar:0,
							centerScreen:1
					});

					yop_poll_hide_loading( yop_poll_various_config.vote_loading_image_target, yop_poll_various_config.vote_loading_image_id );
				}
			}
	});
}

function yop_poll_do_vote( yop_poll_various_config ) { 
	poll_id  			= typeof yop_poll_various_config.poll_id  !== 'undefined' ? yop_poll_various_config.poll_id  : 0;
	is_modal  			= typeof yop_poll_various_config.is_modal  !== 'undefined' ? yop_poll_various_config.is_modal  : false;
	vote_type  			= typeof yop_poll_various_config.vote_type  !== 'undefined' ? yop_poll_various_config.vote_type  : 'default';
	poll_location 		= typeof yop_poll_various_config.poll_location  !== 'undefined' ? yop_poll_various_config.poll_location  : 'page';
	unique_id 			= typeof yop_poll_various_config.unique_id  !== 'undefined' ? yop_poll_various_config.unique_id  : '';

	var popupClose		= false;

	var pollData = {
		'action'                : yop_poll_public_config_general.ajax.vote_action,
		'poll_id'               : poll_id,
		'vote_type'             : vote_type,
		'facebook_user_details' : yop_poll_various_config.facebook_user_details,
		'facebook_error'        : yop_poll_various_config.facebook_error,
		'unique_id'             : unique_id,
		'location'              : poll_location
	};
	pollData = jQuery.param(pollData) + "&" + jQuery('#yop-poll-form-'+ poll_id + unique_id ).serialize();
																				
	
	jQuery.ajax({
			type: 'POST',
			url: yop_poll_public_config_general.ajax.url,
			data: pollData,
			cache: false,
			async: false, 
			success: function( data ) {
				data		= yop_poll_extractResponse( data );
				response	= JSON.parse(data);
				if ( '' != response.error ) {
					jQuery( '#yop-poll-container-error-'+ poll_id + unique_id ).html(response.error);
					jQuery( '#yop-poll-container-success-'+ poll_id + unique_id ).html('');
					popupClose	= true;
				}
				else {
					if ( '' != response.message ) {

						if ( 'yes' == yop_poll_public_config_general.pro.pro_user ) {
							if ( 'yes' == yop_poll_various_config.public_config.poll_options.share_after_vote ) {
								jQuery('#yop_poll_vote-button-' + yop_poll_various_config.poll_id + unique_id ).popupWindow({
										windowURL: yop_poll_public_config_general.pro.api_server_url + '/api/facebook/share_vote?' + 'api_key='+yop_poll_public_config_general.pro.api_key + '&' + yop_poll_various_config_to_get_params( yop_poll_various_config, 'yes' ),
										windowName:'yop_poll_popup_window',
										height:200,
										left:0,
										location:0,
										menubar:0,
										resizable:0,
										scrollbars:1,
										status:0,
										width:450,
										top:0,
										toolbar:0,
										centerScreen:1
								});	
							}
							else 
								popupClose	= true;
						}
						else 
							popupClose	= true;				

						jQuery('#yop-poll-container-'+ poll_id + unique_id ).replaceWith(response.message);
						jQuery('#yop-poll-container-error-'+ poll_id + unique_id ).html('');
						jQuery( '#yop-poll-container-success-'+ poll_id + unique_id ).html(response.success);

						yop_poll_runEval(poll_id, unique_id);

						if ( 'yes' == yop_poll_various_config.public_config.poll_options.redirect_after_vote ) {
                                window.location = yop_poll_various_config.public_config.poll_options.redirect_after_vote_url;
						}
					}
					else {
						jQuery( '#yop-poll-container-error-' + poll_id + unique_id ).html('An Error Has Occured!');
						jQuery( '#yop-poll-container-success-'+ poll_id + unique_id ).html('');
						popupClose	= true;
					}
				}
			},
			error: 
			function() {
				jQuery( '#yop-poll-container-error-' + poll_id + unique_id ).html('An Error Has Occured!');
				jQuery( '#yop-poll-container-success-'+ poll_id + unique_id ).html('');
				popupClose	= true;
			} 
	});
	return popupClose;
}

function yop_poll_view_results( poll_id, poll_location, unique_id ) {
	poll_location = typeof poll_location  !== 'undefined' ? poll_location  : 'page';
	unique_id = typeof unique_id  !== 'undefined' ? unique_id  : '';
	var yop_poll_public_config	= window['yop_poll_public_config_' + poll_id + unique_id ];
	jQuery('#yop_poll_result_link'+ poll_id + unique_id ).hide();
	var result_link_loading_image	= document.createElement('img');
	result_link_loading_image.src	= yop_poll_public_config.loading_image_src;
	result_link_loading_image.alt	= yop_poll_public_config.loading_image_alt;
	result_link_loading_image.id	= 'yop_poll_result_link_loading_img-'+ poll_id + unique_id;
	jQuery('#yop_poll_result_link'+ poll_id  + unique_id).after( result_link_loading_image );
	jQuery('#yop_poll_result_link_loading_img-'+ poll_id + unique_id ).css( 'border', 'none' );
    var pollData = {

    };
   pollData = jQuery.param(pollData) + "&" + jQuery('#yop-poll-form-'+ poll_id + unique_id ).serialize();
	jQuery.ajax({
			type: 'POST',
			url: yop_poll_public_config_general.ajax.url,
			data: 'action='+yop_poll_public_config_general.ajax.view_results_action+'&poll_id=' + poll_id + '&unique_id=' + unique_id + '&location=' + poll_location + '&tr_id=' + jQuery('#yop-poll-tr-id-' + poll_id + unique_id ).val()+pollData,
			cache: false,
			error: function() {
				alert('An error has occured!');
				jQuery('#yop_poll_result_link_loading_img-'+ poll_id + unique_id ).remove();
				jQuery('#yop_poll_result_link'+ poll_id + unique_id).show();
			},
			success:
			function( data ){
				data		= yop_poll_extractResponse( data );
				response	= JSON.parse(data);
				if ( '' != response.error ) {
					jQuery('#yop-poll-container-error-'+ poll_id + unique_id).html(response.error);
					jQuery( '#yop-poll-container-success-'+ poll_id + unique_id ).html('');
				}
				else {
					if ( '' != response.message ) {
						jQuery('#yop-poll-container-'+ poll_id + unique_id ).replaceWith(response.message);
						jQuery('#yop-poll-container-error-'+ poll_id + unique_id ).html('');
						jQuery( '#yop-poll-container-success-'+ poll_id + unique_id ).html(response.success);
						yop_poll_runEval(poll_id, unique_id);
					}
					else {
						jQuery('#yop-poll-container-error-'+ poll_id + unique_id ).replaceWith('An Error Has Occured!');
						jQuery( '#yop-poll-container-success-'+ poll_id + unique_id ).html('');
					}
				}
				jQuery('#yop_poll_result_link_loading_img-'+ poll_id + unique_id ).remove();  licence
				jQuery('#yop_poll_result_link'+ poll_id + unique_id ).show();
			}
	});
}

function yop_poll_back_to_vote( poll_id, poll_location, unique_id ) {
	poll_location = typeof poll_location  !== 'undefined' ? poll_location  : 'page';
	unique_id = typeof unique_id  !== 'undefined' ? unique_id  : '';
	var yop_poll_public_config	= window['yop_poll_public_config_' + poll_id + unique_id ];
	jQuery('#yop_poll_back_to_vote_link'+ poll_id + unique_id ).hide();
	var back_to_vote_loading_image	= document.createElement('img');
	back_to_vote_loading_image.src	= yop_poll_public_config.loading_image_src;
	back_to_vote_loading_image.alt	= yop_poll_public_config.loading_image_alt;
	back_to_vote_loading_image.id	= 'yop_poll_back_to_vote_loading_img-'+ poll_id + unique_id ;
	jQuery('#yop_poll_back_to_vote_link'+ poll_id + unique_id ).after( back_to_vote_loading_image );
	jQuery('#yop_poll_back_to_vote_loading_img-'+ poll_id + unique_id ).css( 'border', 'none' );
	jQuery.ajax({
			type: 'POST',
			url: yop_poll_public_config_general.ajax.url,
			data: 'action='+yop_poll_public_config_general.ajax.back_to_vote_action+'&poll_id=' + poll_id + '&unique_id=' + unique_id + '&location=' + poll_location + '&tr_id=' + jQuery('#yop-poll-tr-id-' + poll_id + unique_id ).val(),
			cache: false,
			error: function() {
				alert('An error has occured!');
				jQuery('#yop_poll_back_to_vote_loading_img-'+ poll_id + unique_id ).remove();
				jQuery('#yop_poll_result_link'+ poll_id + unique_id ).show();
			},
			success:
			function( data ){
				data		= yop_poll_extractResponse( data );
				response	= JSON.parse(data);
				if ( '' != response.error ) {                                            
					jQuery('#yop-poll-container-error-'+ poll_id + unique_id ).html(response.error);
					jQuery( '#yop-poll-container-success-'+ poll_id + unique_id ).html('');
				}
				else {
					if ( '' != response.message ) {
						jQuery('#yop-poll-container-'+ poll_id + unique_id ).replaceWith(response.message);
						jQuery('#yop-poll-container-error-'+ poll_id + unique_id ).html('');
						jQuery( '#yop-poll-container-success-'+ poll_id + unique_id ).html(response.success);
						yop_poll_runEval(poll_id, unique_id);
					}
					else {
						jQuery('#yop-poll-container-error-'+ poll_id + unique_id ).html('An Error Has Occured!');
						jQuery( '#yop-poll-container-success-'+ poll_id + unique_id ).html('');
					}
				}
				jQuery('#yop_poll_back_to_vote_loading_img-'+ poll_id + unique_id ).remove();
				jQuery('#yop_poll_result_link'+ poll_id + unique_id ).show();
			}
	});
}

function yop_poll_extractResponse( str ) {
	var patt	= /\[ajax-response\](.*)\[\/ajax-response\]/m;
	resp 		= str.match( patt )
	return resp[1];
}

function yop_poll_extractApiResponse( str ) {
	var patt	= /\[response\](.*)\[\/response\]/m;
	resp 		= str.match( patt )
	return resp[1];
}

function yop_poll_reloadCaptcha( poll_id, unique_id ) {
	unique_id = typeof unique_id  !== 'undefined' ? unique_id  : '';
	var yop_poll_public_config	= window['yop_poll_public_config_' + poll_id + unique_id  ];
	jQuery('#yop_poll_captcha_image_' + poll_id + unique_id ).attr( 'src', yop_poll_public_config_general.ajax.url + '?action=' + yop_poll_public_config_general.ajax.captcha_action + '&poll_id=' + poll_id + '&sid=' + Math.random() + '&unique_id=' + unique_id );
}

function yop_poll_show_multiple_vote_options( yop_poll_various_config ) {
	poll_location = typeof yop_poll_various_config.poll_location  !== 'undefined' ? yop_poll_various_config.poll_location  : 'page';
	unique_id = typeof yop_poll_various_config.unique_id  !== 'undefined' ? yop_poll_various_config.unique_id  : '';

	var vote_options		= yop_poll_get_vote_options( yop_poll_various_config.public_config.poll_options.vote_permisions_types );
	var vote_options_string	= '<div id="yop_poll_vote_options_div-' + yop_poll_various_config.poll_id + unique_id + '">';
	if ( vote_options.W )
		vote_options_string += '<button class="yop_poll_wordpress_vote_button" id="yop_poll_wordpress-vote-button-' + yop_poll_various_config.poll_id + unique_id + '" onclick="yop_poll_vote_on_multiple_options(\'' + yop_poll_various_config.poll_id + '\', \'wordpress\', \'' + poll_location + '\', \'' + unique_id + '\'); return false;">' + yop_poll_various_config.public_config.poll_options.vote_permisions_wordpress_label + '</button><br>';
	if ( vote_options.F )
		vote_options_string += '<button class="yop_poll_facebook_vote_button" id="yop_poll_facebook-vote-button-' + yop_poll_various_config.poll_id + unique_id + '" onclick="yop_poll_vote_on_multiple_options(\'' + yop_poll_various_config.poll_id + '\',\'facebook\', \'' + poll_location + '\', \'' + unique_id + '\'); return false;">' + yop_poll_various_config.public_config.poll_options.vote_permisions_facebook_label + '</button><br>';
	if ( vote_options.A )
		vote_options_string += '<button class="yop_poll_anonymous_vote_button" id="yop_poll_anonimous-vote-button-' + yop_poll_various_config.poll_id + unique_id + '" onclick="yop_poll_vote_on_multiple_options(\'' + yop_poll_various_config.poll_id + '\',\'anonymous\', \'' + poll_location + '\', \'' + unique_id + '\'); return false;">' + yop_poll_various_config.public_config.poll_options.vote_permisions_anonymous_label + '</button><br>';
	vote_options_string += '<div style="clear:both; height:25px">&nbsp;</div></div>';

	jQuery( '#' + yop_poll_various_config.vote_loading_image_target ).hide();	
	jQuery( '#' + yop_poll_various_config.vote_loading_image_target ).after( vote_options_string );
}

function yop_poll_vote_on_multiple_options( poll_id, vote_type, poll_location, unique_id ) {
	poll_location = typeof poll_location  !== 'undefined' ? poll_location  : 'page';
	unique_id = typeof unique_id  !== 'undefined' ? unique_id  : '';
	var yop_poll_public_config							= window['yop_poll_public_config_' + poll_id + unique_id ];
	var yop_poll_various_config 						= new Object();
	yop_poll_various_config.poll_id						= poll_id;
	yop_poll_various_config.is_modal					= 0;
	yop_poll_various_config.vote_loading_image_target	= 'yop_poll_vote_options_div-'+ poll_id + unique_id ;
	yop_poll_various_config.vote_loading_image_id		= 'yop_poll_vote_button_loading_img-'+ poll_id + unique_id ;
	yop_poll_various_config.vote_loading_image_alt		= yop_poll_public_config.loading_image_alt;
	yop_poll_various_config.vote_type					= vote_type;
	yop_poll_various_config.poll_location				= poll_location;
	yop_poll_various_config.unique_id					= unique_id;
	yop_poll_various_config.public_config				= yop_poll_public_config;

	yop_poll_show_loading( yop_poll_various_config.vote_loading_image_target, yop_poll_various_config.vote_loading_image_id , yop_poll_public_config );

	switch( vote_type ) {
		case 'wordpress':
			yop_poll_vote_with_wordpress( yop_poll_various_config ); 
			break;
		case 'anonymous':
			yop_poll_various_config.vote_type	=  'anonymous';
			yop_poll_do_vote( yop_poll_various_config );
			break;
		case 'facebook':
			yop_poll_vote_with_facebook( yop_poll_various_config );
			break;
	}

	yop_poll_hide_loading( yop_poll_various_config.vote_loading_image_target, yop_poll_various_config.vote_loading_image_id );		
}        

function yop_poll_get_vote_options( vote_permisions_types )  {
	var vote_options	= { W: false, A: false, F: false };
	switch ( vote_permisions_types) {
		case 1:
			vote_options.W	= true;
			break;
		case 2:
			vote_options.A	= true;
			break;
		case 3:
			vote_options.W	= true;
			vote_options.A	= true;
			break;
		case 4:
			vote_options.F	= true;
			break;
		case 5:
			vote_options.W	= true;
			vote_options.F	= true;
			break;
		case 6:
			vote_options.F	= true;
			vote_options.A	= true;
			break;
		case 7:
			vote_options.W	= true;
			vote_options.F	= true;
			vote_options.A	= true;
			break;
	} 
	return vote_options; 
}

function yop_poll_urlencode (str) {
	str = (str + '').toString();

	return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').
	replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
}


