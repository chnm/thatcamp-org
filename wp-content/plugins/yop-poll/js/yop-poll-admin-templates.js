jQuery(document).ready(function(jQuery) {

    jQuery(".yop_poll_tooltip-buy_pro" ).click( function(){

        var id=this.id;
        //console.log(id);
        if(id=='add_new_media_question2'){
            jQuery('.yop-poll-buy-template-li').removeClass('yop-poll-buy-template-selected');
            jQuery('#yop-poll-buy-template-2').addClass('yop-poll-buy-template-selected');
        }
        if(id=='add_new_text_question2')
        {
            jQuery('.yop-poll-buy-template-li').removeClass('yop-poll-buy-template-selected');
            jQuery('#yop-poll-buy-template-1').addClass('yop-poll-buy-template-selected');
        }
        if(id=='yop-poll-1'){
            jQuery('.yop-poll-buy-template-li').removeClass('yop-poll-buy-template-selected');
            jQuery('#yop-poll-buy-template-4').addClass('yop-poll-buy-template-selected');
        }
        if(id=='yop-poll-vote-permisions-facebook-integration-yes' || id=='yop-poll-vote-permisions-facebook-no' ||
            id=='yop-poll-facebook-share-after-vote-yes' || id=='yop-poll-facebook-share-after-vote-no'  ||
            id=='yop-poll-vote-permisions-google-yes' || id=='yop-poll-vote-permisions-google-no'||
            id=='yop-poll-vote-permisions-show-g-share-button-yes'|| id=='yop-poll-vote-permisions-show-g-share-button-no' ||
            id=='yop_poll_google_integration-yes' || id=='yop_poll_google_integration-no'
            || id=='yop-poll-for-slider-page-4'||id=='yop-poll-for-slider-page2'
            || id=='yop-poll-for-slider-page3'){



            jQuery('.yop-poll-buy-template-li').removeClass('yop-poll-buy-template-selected');

            jQuery("#yop-poll-buy-template-3").addClass('yop-poll-buy-template-selected');
        }
        if(id=='yop-poll-user-interface-type-beginner' || id=='yop-poll-user-interface-type-advanced'){
            jQuery('.yop-poll-buy-template-li').removeClass('yop-poll-buy-template-selected');
            jQuery("#yop-poll-buy-template-0").addClass('yop-poll-buy-template-selected');
        }
        if(id=='yop-poll-for-slider-page'||id=='yop-poll-for-slider-page1'){
            jQuery('.yop-poll-buy-template-li').removeClass('yop-poll-buy-template-selected');
            jQuery("#yop-poll-buy-template-3").addClass('yop-poll-buy-template-selected');
        }
        if(id=='yop-poll-for-slider-page-7'){
            jQuery('.yop-poll-buy-template-li').removeClass('yop-poll-buy-template-selected');
            jQuery("#yop-poll-buy-template-5").addClass('yop-poll-buy-template-selected');
        }




        jQuery(".yop_poll_pro_feature").dialog({

            height: '700',

            width: '750',

            resizable: false,

            modal: true,

            dialogClass: 'fixed-dialog'

            //position:{top:'top+100'}

        });
        jQuery( "#yop-poll-buy-template-slider" ).jcarousel( 'scroll', jQuery( '.yop-poll-buy-template-selected' ) );
        //if (jQuery(this).is(':radio')) {
        //    jQuery(this ).attr('checked', false);

        // }
        // jQuery(".yop_poll_pro_feature").removeClass('yop-poll-buy-template-selected');
    });
    jQuery('#yop-poll-pro-close').click(function() {

        jQuery(".yop_poll_pro_feature").dialog('close');

    });
    jQuery('#yop-poll-edit-add-new-template-form-save, #yop-poll-edit-add-new-template-form-save1').click( function() {
       //alert(  yop_poll_global_settings.ajax_url);

        jQuery.ajax( {
            type: 'POST',
            url: yop_poll_global_settings.ajax_url,
            data: 'action=yop_poll_add_edit_templates' + '&' + jQuery( "#yop-poll-edit-add-new-template-form").serialize(),
            cache: false,
            beforeSend: function () {
                jQuery( 'html, body' ).animate( {scrollTop: '0px'}, 800 );
                jQuery( '#message' ).html( '<p>' + yop_poll_global_settings.message_before_ajax_send + '</p>' );
                jQuery( "#message" ).removeClass();
                jQuery( '#message' ).addClass( 'updated' );
                jQuery( '#message' ).show();
            },
            error: function () {
                jQuery( 'html, body' ).animate( {scrollTop: '0px'}, 800 );
                jQuery( '#message' ).html( '<p>' + yop_poll_global_settings.error_message_ajax + '</p>' );
                jQuery( "#message" ).removeClass();
                jQuery( '#message' ).addClass( 'error' );
                jQuery( '#message' ).show();
            },
            success: function ( data ) {
                jQuery( 'html, body' ).animate( {scrollTop: '0px'}, 800 );
                jQuery( '#message' ).html( '<p>' + data + '</p>' );
                jQuery( "#message" ).removeClass();
                jQuery( '#message' ).addClass( 'updated' );
                jQuery( '#message' ).show();
            }
        } );
	});


	jQuery('#yop-poll-template-before-start-date-handler').click( function() {
		jQuery('#template-before-start-date').toggle('medium');

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

	jQuery('#yop-poll-edit-add-new-template-form-reset').click( function() {

    jQuery.ajax( {
        type: 'POST',
        url: yop_poll_global_settings.ajax_url,
        data: 'action=yop_poll_reset_templates' + '&' + jQuery( "#yop-poll-edit-add-new-template-form").serialize(),
        cache: false,
        beforeSend: function () {
            jQuery( 'html, body' ).animate( {scrollTop: '0px'}, 800 );
            jQuery( '#message' ).html( '<p>' + yop_poll_global_settings.message_before_ajax_send + '</p>' );
            jQuery( "#message" ).removeClass();
            jQuery( '#message' ).addClass( 'updated' );
            jQuery( '#message' ).show();
        },
        error: function () {
            jQuery( 'html, body' ).animate( {scrollTop: '0px'}, 800 );
            jQuery( '#message' ).html( '<p>' + yop_poll_global_settings.error_message_ajax + '</p>' );
            jQuery( "#message" ).removeClass();
            jQuery( '#message' ).addClass( 'error' );
            jQuery( '#message' ).show();
        },
        success: function ( data ) {
            jQuery( 'html, body' ).animate( {scrollTop: '0px'}, 800 );
            jQuery( '#message' ).html( '<p>' + data + '</p>' );
            jQuery( "#message" ).removeClass();
            jQuery( '#message' ).addClass( 'updated' );
            jQuery( '#message' ).show();
        }
    } );

        });

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