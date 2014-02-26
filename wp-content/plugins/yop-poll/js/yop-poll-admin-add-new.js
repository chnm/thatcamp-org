jQuery(document).ready(function(jQuery) {
	jQuery( "#yop-poll-add-answer-button" ).click( function () {
		yop_poll_add_table_answer( jQuery( "#yop-poll-answer-table" ), yop_poll_count_number_of_answer_without_other ( "#yop-poll-answer-table" ) + 1 );
		return false;
	});
	jQuery( "#yop-poll-add-customfield-button" ).click( function () {
		yop_poll_add_table_customfield( jQuery( "#yop-poll-customfields-table" ), yop_poll_count_number_of_customfields ( "#yop-poll-customfields-table" ) + 1 );
		return false;
	});

    jQuery( "#yop-poll-allow-other-answers-yes" ).click( function () {
		jQuery( '#yop-poll-other-answers-label-div' ).show();
		jQuery( '#yop-poll-other-answers-to-results-div' ).show();
		jQuery( '#yop-poll-display-other-answers-values-div' ).show();
		jQuery( '#yop-poll-is-default-other-answers-values-div' ).show();
	});
	jQuery( "#yop-poll-allow-other-answers-no" ).click( function () {
		jQuery( '#yop-poll-other-answers-label-div' ).hide();
        jQuery( '#yop-poll-other-answers-to-results-div' ).hide();
		jQuery( '#yop-poll-display-other-answers-values-div' ).hide();
		jQuery( '#yop-poll-is-default-other-answers-values-div' ).hide();
	});

    jQuery( "#yop-poll-display-answers-vertical" ).click( function () {
		jQuery( '#yop-poll-display-answers-tabulated-div' ).hide();
	});
	jQuery( "#yop-poll-display-answers-orizontal" ).click( function () {
		jQuery( '#yop-poll-display-answers-tabulated-div' ).hide();
	});
	jQuery( "#yop-poll-display-answers-tabulated" ).click( function () {
		jQuery( '#yop-poll-display-answers-tabulated-div' ).show();
	});

    jQuery( "#yop-poll-display-results-vertical" ).click( function () {
		jQuery( '#yop-poll-display-results-tabulated-div' ).hide();
	});
	jQuery( "#yop-poll-display-results-orizontal" ).click( function () {
		jQuery( '#yop-poll-display-results-tabulated-div' ).hide();
	});
	jQuery( "#yop-poll-display-results-tabulated" ).click( function () {
		jQuery( '#yop-poll-display-results-tabulated-div' ).show();
	});

    jQuery( "#yop-poll-allow-multiple-answers-yes" ).click( function () {
		jQuery( '#yop-poll-allow-multiple-answers-div' ).show();
		jQuery( '#yop-poll-allow-multiple-answers-div1' ).show();
	});
	jQuery( "#yop-poll-allow-multiple-answers-no" ).click( function () {
		jQuery( '#yop-poll-allow-multiple-answers-div' ).hide();
		jQuery( '#yop-poll-allow-multiple-answers-div1' ).hide();
	});

    jQuery( ".yop-poll-view-results-hide-custom" ).click( function () {
		jQuery( '#yop-poll-display-view-results-div' ).hide();
	});
	jQuery( ".yop-poll-view-results-show-custom" ).click( function () {
		jQuery( '#yop-poll-display-view-results-div' ).show();
	});

    jQuery( ".yop-poll-blocking-voters-hide-interval" ).click( function () {
		jQuery( '#yop-poll-blocking-voters-interval-div' ).hide();
	});
	jQuery( ".yop-poll-blocking-voters-show-interval" ).click( function () {
		jQuery( '#yop-poll-blocking-voters-interval-div' ).show();
	});
	
	jQuery( "#yop-poll-limit-number-of-votes-per-user-no" ).click( function () {
		jQuery( '#yop-poll-number-of-votes-per-user-div' ).hide();
	});
	jQuery( "#yop-poll-limit-number-of-votes-per-user-yes" ).click( function () {
		jQuery( '#yop-poll-number-of-votes-per-user-div' ).show();
	});
	
	jQuery( "#yop-poll-schedule-reset-poll-stats-no" ).click( function () {
		jQuery( '.yop-poll-schedule-reset-poll-stats-options-div' ).hide();
	});
	jQuery( "#yop-poll-schedule-reset-poll-stats-yes" ).click( function () {
		jQuery( '.yop-poll-schedule-reset-poll-stats-options-div' ).show();
	});
	
	jQuery( "#yop-poll-view-results-link-no" ).click( function () {
		jQuery( '#yop-poll-view-results-link-div' ).hide();
	});
	jQuery( "#yop-poll-view-results-link-yes" ).click( function () {
		jQuery( '#yop-poll-view-results-link-div' ).show();
	});

	jQuery( "#yop-poll-view-back-to-vote-link-no" ).click( function () {
		jQuery( '#yop-poll-view-back-to-vote-link-div' ).hide();
	});
	jQuery( "#yop-poll-view-back-to-vote-link-yes" ).click( function () {
		jQuery( '#yop-poll-view-back-to-vote-link-div' ).show();
	});

	jQuery( "#yop-poll-view-total-votes-no" ).click( function () {
		jQuery( '#yop-poll-view-total-votes-div' ).hide();
	});
	jQuery( "#yop-poll-view-total-votes-yes" ).click( function () {
		jQuery( '#yop-poll-view-total-votes-div' ).show();
	});

	jQuery( "#yop-poll-view-total-answers-no" ).click( function () {
		jQuery( '#yop-poll-view-total-answers-div' ).hide();
	});
	jQuery( "#yop-poll-view-total-answers-yes" ).click( function () {
		jQuery( '#yop-poll-view-total-answers-div' ).show();
	});

	jQuery( "#yop-poll-view-total-voters-no" ).click( function () {
		jQuery( '#yop-poll-view-total-voters-div' ).hide();
	});
	jQuery( "#yop-poll-view-total-voters-yes" ).click( function () {
		jQuery( '#yop-poll-view-total-voters-div' ).show();
	});

	jQuery( "#yop-poll-use-default-loading-image-no" ).click( function () {
		jQuery( '#yop-poll-use-default-loading-image-div' ).show();
	});
	jQuery( "#yop-poll-use-default-loading-image-yes" ).click( function () {
		jQuery( '#yop-poll-use-default-loading-image-div' ).hide();
	});

	jQuery( "#yop-poll-redirect-after-vote-yes" ).click( function () {
		jQuery( '#yop-poll-redirect-after-vote-url-div' ).show();
	});
	jQuery( "#yop-poll-redirect-after-vote-no" ).click( function () {
		jQuery( '#yop-poll-redirect-after-vote-url-div' ).hide();
	});

	jQuery( "#yop-poll-view-poll-archive-link-no" ).click( function () {
		jQuery( '#yop-poll-view-poll-archive-link-div' ).hide();
	});
	jQuery( "#yop-poll-view-poll-archive-link-yes" ).click( function () {
		jQuery( '#yop-poll-view-poll-archive-link-div' ).show();
	});

	jQuery( "#yop-poll-share-after-vote-no" ).click( function () {
		jQuery( '#yop-poll-share-after-vote-name-tr' ).hide();
		jQuery( '#yop-poll-share-after-vote-caption-tr' ).hide();
		jQuery( '#yop-poll-share-after-vote-description-tr' ).hide();
		jQuery( '#yop-poll-share-after-vote-picture-tr' ).hide();
	});
	jQuery( "#yop-poll-share-after-vote-yes" ).click( function () {
		jQuery( '#yop-poll-share-after-vote-name-tr' ).show();
		jQuery( '#yop-poll-share-after-vote-caption-tr' ).show();
		jQuery( '#yop-poll-share-after-vote-description-tr' ).show();
		jQuery( '#yop-poll-share-after-vote-picture-tr' ).show();
	});

	jQuery( "#yop-poll-show-in-archive-no" ).click( function () {
		jQuery( '#yop-poll-show-in-archive-div' ).hide();
	});
	jQuery( "#yop-poll-show-in-archive-yes" ).click( function () {
		jQuery( '#yop-poll-show-in-archive-div' ).show();
	});
	
	jQuery( "#yop-poll-send-email-notifications-no" ).click( function () {
		jQuery( '.yop-poll-email-notifications-div' ).hide();
	});
	jQuery( "#yop-poll-send-email-notifications-yes" ).click( function () {
		jQuery( '.yop-poll-email-notifications-div' ).show();
	});

	jQuery( "#yop-poll-answers-advanced-options-button" ).click( function () {
		jQuery( '#yop-poll-answers-advanced-options-div' ).toggle( 'medium' );
		return false;
	});

	jQuery( "#yop-poll-customfield-advanced-options-button" ).click( function () {
		jQuery( '#yop-poll-custom-fields-advanced-options-div' ).toggle( 'medium' );
		return false;
	});

	jQuery( "#yop-poll-use-template-bar-no" ).click( function () {
		jQuery( '.yop-poll-custom-result-bar-table' ).show();
	});
	jQuery( "#yop-poll-use-template-bar-yes" ).click( function () {
		jQuery( '.yop-poll-custom-result-bar-table' ).hide();
	});

	jQuery( "#yop-poll-vote-permisions-quest-only" ).click( function () {
		jQuery( '.yop-poll-vote-as-div' ).hide(); 
	});
	jQuery( "#yop-poll-vote-permisions-registered-only" ).click( function () {
		jQuery( '.yop-poll-vote-as-div' ).show();
		if ( true == jQuery( '#yop-poll-vote-permisions-facebook-yes' ).is(':checked') )
			jQuery( '#yop-poll-vote-permisions-facebook-div' ).show();	
		if ( true == jQuery( '#yop-poll-vote-permisions-facebook-no' ).is(':checked') )
			jQuery( '#yop-poll-vote-permisions-facebook-div' ).hide();

		if ( true == jQuery( '#yop-poll-vote-permisions-wordpress-yes' ).is(':checked') )
			jQuery( '#yop-poll-vote-permisions-wordpress-div' ).show();	
		if ( true == jQuery( '#yop-poll-vote-permisions-wordpress-no' ).is(':checked') )
			jQuery( '#yop-poll-vote-permisions-wordpress-div' ).hide();

		if ( true == jQuery( '#yop-poll-vote-permisions-anonymous-yes' ).is(':checked') )
			jQuery( '#yop-poll-vote-permisions-anonymous-div' ).show();	
		if ( true == jQuery( '#yop-poll-vote-permisions-anonymous-no' ).is(':checked') )
			jQuery( '#yop-poll-vote-permisions-anonymous-div' ).hide();
	});
	jQuery( "#yop-poll-vote-permisions-guest-registered" ).click( function () {
		jQuery( '.yop-poll-vote-as-div' ).show();
		if ( true == jQuery( '#yop-poll-vote-permisions-facebook-yes' ).is(':checked') )
			jQuery( '#yop-poll-vote-permisions-facebook-div' ).show();	
		if ( true == jQuery( '#yop-poll-vote-permisions-facebook-no' ).is(':checked') )
			jQuery( '#yop-poll-vote-permisions-facebook-div' ).hide();

		if ( true == jQuery( '#yop-poll-vote-permisions-wordpress-yes' ).is(':checked') )
			jQuery( '#yop-poll-vote-permisions-wordpress-div' ).show();	
		if ( true == jQuery( '#yop-poll-vote-permisions-wordpress-no' ).is(':checked') )
			jQuery( '#yop-poll-vote-permisions-wordpress-div' ).hide();

		if ( true == jQuery( '#yop-poll-vote-permisions-anonymous-yes' ).is(':checked') )
			jQuery( '#yop-poll-vote-permisions-anonymous-div' ).show();	
		if ( true == jQuery( '#yop-poll-vote-permisions-anonymous-no' ).is(':checked') )
			jQuery( '#yop-poll-vote-permisions-anonymous-div' ).hide();
	});

	jQuery( "#yop-poll-vote-permisions-facebook-yes" ).click( function () {
		jQuery( '#yop-poll-vote-permisions-facebook-div' ).show();
	});
	jQuery( "#yop-poll-vote-permisions-facebook-no" ).click( function () {
		jQuery( '#yop-poll-vote-permisions-facebook-div' ).hide();
	});

	jQuery( "#yop-poll-vote-permisions-wordpress-yes" ).click( function () {
		jQuery( '#yop-poll-vote-permisions-wordpress-div' ).show();
	});
	jQuery( "#yop-poll-vote-permisions-wordpress-no" ).click( function () {
		jQuery( '#yop-poll-vote-permisions-wordpress-div' ).hide();
	});

	jQuery( "#yop-poll-vote-permisions-anonymous-yes" ).click( function () {
		jQuery( '#yop-poll-vote-permisions-anonymous-div' ).show();
	});
	jQuery( "#yop-poll-vote-permisions-anonymous-no" ).click( function () {
		jQuery( '#yop-poll-vote-permisions-anonymous-div' ).hide();
	});

	jQuery( "#yop-poll-never-expire" ).click( function () {
		if ( true == jQuery( this ).is(':checked') ) {
			jQuery( "#yop-poll-end-date-input" ).attr("disabled", "disabled");
			jQuery( "#yop-poll-end-date-input" ).hide();
		}
		else {
			jQuery( "#yop-poll-end-date-input" ).removeAttr("disabled", "disabled");
			jQuery( "#yop-poll-end-date-input" ).show();
		}
	});
	jQuery ( "#message").hide();

	var yopPollStartDateTextBox = jQuery('#yop-poll-start-date-input');
	var yopPollEndDateTextBox = jQuery('#yop-poll-end-date-input');
	var yopPollViewResultStartDateTextBox = jQuery('#yop-poll-view-results-start-date').datetimepicker({
		showSecond: true,
		timeFormat: 'hh:mm:ss',
		dateFormat: 'yy-mm-dd'}
	);
	
	var yopPollResetPollDateTextBox	= jQuery('#yop-poll-schedule-reset-poll-stats-date').datetimepicker({
		showSecond: false,
		showMinute: false,
		showHour: true,
		timeFormat: 'hh:00:00',
		dateFormat: 'yy-mm-dd'}
	);

	yopPollStartDateTextBox.datetimepicker({
		showSecond: true,
		timeFormat: 'hh:mm:ss',
		dateFormat: 'yy-mm-dd'
	});
	yopPollEndDateTextBox.datetimepicker({
		showSecond: true,
		timeFormat: 'hh:mm:ss',
		dateFormat: 'yy-mm-dd'
	});

	jQuery('#yop-poll-edit-add-new-form-submit').click( function () {
		savePoll();
	});                                         
	jQuery('#yop-poll-edit-add-new-form-submit1').click( function () {
		savePoll();
	});

	function savePoll() {
        var x = {
            'action' : yop_poll_add_new_config.ajax.action
        };
        var toSend = jQuery.param(x) + "&" + jQuery('#yop-poll-edit-add-new-form' ).serialize();
		jQuery.ajax({
			type: 'POST',
			url: yop_poll_add_new_config.ajax.url,
			data: toSend,
			cache: false,
			beforeSend: function() {
				jQuery('html, body').animate({scrollTop: '0px'}, 800);
				jQuery('#message').html('<p>' + yop_poll_add_new_config.ajax.beforeSendMessage + '</p>');
				jQuery("#message").removeClass();
				jQuery('#message').addClass('updated');
				jQuery('#message').show();
			},
			error: function() {
				jQuery('html, body').animate({scrollTop: '0px'}, 800);
				jQuery('#message').html('<p>' + yop_poll_add_new_config.ajax.errorMessage + '</p>');
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
	}

});
function yop_poll_add_table_answer( table, ans_no ) {
	var answer_id = yop_poll_add_new_config.default_number_of_answers;
	var bar_border_style_solid_checked	= '';
	if ( 'solid' == yop_poll_add_new_config.poll_bar_default_options.border)
		bar_border_style_solid_checked	= 'selected="selected"';

	var bar_border_style_dashed_checked	= '';
	if ( 'dashed' == yop_poll_add_new_config.poll_bar_default_options.border)
		bar_border_style_dashed_checked	= 'selected="selected"';

	var bar_border_style_dotted_checked	= '';
	if ( 'dotted' == yop_poll_add_new_config.poll_bar_default_options.border)
		bar_border_style_dotted_checked	= 'selected="selected"';

	var jQuerytr = '<tr class="yop_poll_tr_answer" id="yop_poll_tr_answer' + answer_id + '"><th scope="row"><label class="yop_poll_answer_label" for="yop-poll-answer' + answer_id + '">' + yop_poll_add_new_config.text_answer + ' ' + ans_no + '</label></th><td><input type="text" value="" id="yop-poll-answer'+ answer_id +'" name="yop_poll_answer[answer'+ answer_id +']" /></td><td align="right"><input type="button" value="' + yop_poll_add_new_config.text_customize_answer + '" onclick="yop_poll_toogle_customize_answer(\'#yop-poll-answer-table\', ' + answer_id + ' ); return false;" class="button" /> <input onclick="yop_poll_remove_answer( \'#yop-poll-answer-table\', ' + answer_id + ' ); return false;" type="button" value="' + yop_poll_add_new_config.text_remove_answer + '" class="button" /></td></tr>';
	jQuerytr += '<tr class="yop_poll_tr_customize_answer" id="yop_poll_tr_customize_answer' + answer_id +'" style="display:none;">' +
	'<td colspan="3">' +
	
	'<table cellspacing="0" width="100%"><tbody>' +
	'<tr>' +
	'<th>' + yop_poll_add_new_config.text_is_default_answer +
	'</th>' +
	'<td>' +
	'<input checked="checked" id="yop-poll-is-default-answer-no-' + answer_id + '" ';
	jQuerytr += ' type="radio" name="yop_poll_answer_options[answer' + answer_id + '][is_default_answer]" value="no" /> <label for="yop-poll-is-default-answer-no-' + answer_id + '">' + yop_poll_add_new_config.text_poll_bar_style.use_template_bar_no_label + '</label>&nbsp;|&nbsp;' +
	'<input id="yop-poll-is-default-answer-yes-' + answer_id + '" ';
	jQuerytr += 'type="radio" name="yop_poll_answer_options[answer' + answer_id + '][is_default_answer]" value="yes" /> <label for="yop-poll-is-default-answer-yes-' + answer_id + '">' + yop_poll_add_new_config.text_poll_bar_style.use_template_bar_yes_label + '</label>' +
	'</td>' +
	'</tr>' +
	'</tbody>' +
	'</table>' +
	
	'<table cellspacing="0" width="100%"><tbody>' +
	'<tr>' +
	'<th>' + yop_poll_add_new_config.text_poll_bar_style.use_template_bar_label +
	'</th>' +
	'<td>' +
	'<input onclick="jQuery(\'#yop-poll-answer-use-template-bar-table-' + answer_id + '\').show();" id="yop-poll-answer-use-template-bar-no-' + answer_id + '" ';
	if ( 'no' == yop_poll_add_new_config.poll_bar_default_options.use_template_bar )
		jQuerytr += 'checked="checked"';
	jQuerytr += ' type="radio" name="yop_poll_answer_options[answer' + answer_id + '][use_template_bar]" value="no" /> <label for="yop-poll-answer-use-template-bar-no-' + answer_id + '">' + yop_poll_add_new_config.text_poll_bar_style.use_template_bar_no_label + '</label>&nbsp;|&nbsp;' +
	'<input onclick="jQuery(\'#yop-poll-answer-use-template-bar-table-' + answer_id + '\').hide();" id="yop-poll-answer-use-template-bar-yes-' + answer_id + '" ';
	if ( 'yes' == yop_poll_add_new_config.poll_bar_default_options.use_template_bar )
		jQuerytr += 'checked="checked"';
	jQuerytr += 'type="radio" name="yop_poll_answer_options[answer' + answer_id + '][use_template_bar]" value="yes" /> <label for="yop-poll-answer-use-template-bar-yes-' + answer_id + '">' + yop_poll_add_new_config.text_poll_bar_style.use_template_bar_yes_label + '</label>' +
	'</td>' +
	'</tr>' +
	'</tbody>' +
	'</table>' +
	'<table cellspacing="0" width="100%" id="yop-poll-answer-use-template-bar-table-' + answer_id + '" style="';
	if ( 'yes' == yop_poll_add_new_config.poll_bar_default_options.use_template_bar )
		jQuerytr += 'display:none';
	jQuerytr += '">' +
	'<tbody>' +
	'<tr>' +
	'<th>' +
	'<label>' + yop_poll_add_new_config.text_poll_bar_style.poll_bar_style_label + '</label>' +
	'</th>' +
	'<td>' +
	'<table cellspacing="0" style="margin-left:0px;" style="width:100%"><tbody>' +
	'<tr>' +
	'<th>' +
	'<label for="yop-poll-answer-option-bar-background-answer' + answer_id + '">' + yop_poll_add_new_config.text_poll_bar_style.poll_bar_style_background_label + '</label>' +
	'</th>' +
	'<td>' +
	'#<input id="yop-poll-answer-option-bar-background-answer' + answer_id + '" value="' + yop_poll_add_new_config.poll_bar_default_options.background_color + '" onblur="yop_poll_update_bar_style(\'#yop-poll-bar-preview' + answer_id + '\', \'background-color\', \'#\'+this.value)" type="text" name="yop_poll_answer_options[answer' + answer_id + '][bar_background]" />' +
	'</td>' +
	'</tr>' +
	'<tr>' +
	'<th>' +
	'<label for="yop-poll-answer-option-bar-height-answer' + answer_id + '">' + yop_poll_add_new_config.text_poll_bar_style.poll_bar_style_height_label + '</label>' +
	'</th>' +
	'<td>' +
	'<input id="yop-poll-answer-option-bar-height-answer' + answer_id + '" value="' + yop_poll_add_new_config.poll_bar_default_options.height + '" onblur="yop_poll_update_bar_style(\'#yop-poll-bar-preview' + answer_id + '\', \'height\', this.value+\'px\')" type="text" name="yop_poll_answer_options[answer' + answer_id + '][bar_height]" /> px' +
	'</td>' +
	'</tr>' +
	'<tr>' +
	'<th>' +
	'<label for="yop-poll-answer-option-bar-border-color-answer' + answer_id + '">' + yop_poll_add_new_config.text_poll_bar_style.poll_bar_style_border_color_label + '</label>' +
	'</th>' +
	'<td>' +
	'#<input id="yop-poll-answer-option-bar-border-color-answer' + answer_id + '" value="' + yop_poll_add_new_config.poll_bar_default_options.border_color + '" onblur="yop_poll_update_bar_style(\'#yop-poll-bar-preview' + answer_id + '\', \'border-color\', \'#\'+this.value)" type="text" name="yop_poll_answer_options[answer' + answer_id + '][bar_border_color]" />' +
	'</td>' +
	'</tr>' +
	'<tr>' +
	'<th>' +
	'<label for="yop-poll-answer-option-bar-border-width-answer' + answer_id + '">' + yop_poll_add_new_config.text_poll_bar_style.poll_bar_style_border_width_label + '</label>' +
	'</th>' +
	'<td>' +
	'<input id="yop-poll-answer-option-bar-border-width-answer' + answer_id + '" value="' + yop_poll_add_new_config.poll_bar_default_options.border_width + '" onblur="yop_poll_update_bar_style(\'#yop-poll-bar-preview' + answer_id + '\', \'border-width\', this.value+\'px\')" type="text" name="yop_poll_answer_options[answer' + answer_id + '][bar_border_width]" /> px' +
	'</td>' +
	'</tr>' +
	'<tr>' +
	'<th>' +
	'<label for="yop-poll-answer-option-bar-border-style-answer' + answer_id + '">' + yop_poll_add_new_config.text_poll_bar_style.poll_bar_style_border_style_label + '</label>' +
	'</th>' +
	'<td>' +
	'<select id="yop-poll-answer-option-bar-border-style-answer' + answer_id + '" onchange="yop_poll_update_bar_style(\'#yop-poll-bar-preview' + answer_id + '\', \'border-style\', this.value)" name="yop_poll_answer_options[answer' + answer_id + '][bar_border_style]">' +
	'<option ' + bar_border_style_solid_checked + ' value="solid">Solid</option>' +
	'<option ' + bar_border_style_dashed_checked + ' value="dashed">Dashed</option>' +
	'<option ' + bar_border_style_dotted_checked + ' value="dotted">Dotted</option>' +
	'</select>' +
	'</td>' +
	'</tr>' +
	'</tbody></table>' +
	'</td>' +
	'</tr>' +
	'<tr>' +
	'<th>' +
	'<label>' + yop_poll_add_new_config.text_poll_bar_style.poll_bar_preview_label + '</label>' +
	'</th>' +
	'<td>' +
	'<div id="yop-poll-bar-preview' + answer_id + '" style="width: 100px; height: ' + yop_poll_add_new_config.poll_bar_default_options.height + 'px; background-color: #' + yop_poll_add_new_config.poll_bar_default_options.background_color +'; border-style: '+ yop_poll_add_new_config.poll_bar_default_options.border + '; border-width: ' + yop_poll_add_new_config.poll_bar_default_options.border_width + 'px; border-color: #' + yop_poll_add_new_config.poll_bar_default_options.border_color + ';"></div>' +
	'</td>' +
	'</tr>' +
	'</tbody>' +
	'</table>' +
	'</td>' +
	'</tr>';
	if ( 1 == ans_no ) {
		jQuery( table ).children( 'tbody' ).html( jQuerytr );
	}
	else {
		jQuery( table ).children( 'tbody' ).children( 'tr:last' ).after( jQuerytr );
	}
	jQuery( '#yop_poll_tr_answer' + yop_poll_add_new_config.default_number_of_answers ).hide().fadeIn( 'medium' );
	yop_poll_add_new_config.default_number_of_answers++;
	yop_poll_reorder_answer( table );
};

function yop_poll_add_table_customfield( table, custfield_no ) {
	var jQuerytr = '<tr class="yop_poll_tr_customfields" id="yop_poll_tr_customfield' + yop_poll_add_new_config.default_number_of_customfields + '"><th scope="row"><label class="yop_poll_customfield_label" for="yop_poll_customfield' + yop_poll_add_new_config.default_number_of_customfields + '">' + yop_poll_add_new_config.text_customfield + ' ' + custfield_no + '</label></th><td><input type="text" value="" id="yop-poll-customfield' + yop_poll_add_new_config.default_number_of_customfields + '" name="yop_poll_customfield[customfield' + yop_poll_add_new_config.default_number_of_customfields +']" /> <input value="yes" id="yop-poll-customfield-required-' + yop_poll_add_new_config.default_number_of_customfields + '" type="checkbox" name="yop_poll_customfield_required[customfield' + yop_poll_add_new_config.default_number_of_customfields +']" /> <label for="yop-poll-customfield-required-' + yop_poll_add_new_config.default_number_of_customfields + '">' + yop_poll_add_new_config.text_requiered_customfield + '</label></td><td align="right"><input onclick="yop_poll_remove_customfield( \'#yop-poll-customfields-table\', ' + yop_poll_add_new_config.default_number_of_customfields + ' ); return false;" type="button" value="' + yop_poll_add_new_config.text_remove_customfield + '" class="button" /></td></tr>';
	if ( 1 == custfield_no ) {
		jQuery( table ).children( 'tbody' ).html( jQuerytr );
	}
	else {
		jQuery( table ).children( 'tbody' ).children( 'tr:last' ).after( jQuerytr );
	}
	jQuery( '#yop_poll_tr_customfield' + yop_poll_add_new_config.default_number_of_customfields ).hide().fadeIn( 'medium' );
	yop_poll_add_new_config.default_number_of_customfields++;
	yop_poll_reorder_customfields( table );
};

function yop_poll_count_number_of_answer ( table ) {
	var jQuerycount = 0;
	jQuerycount = jQuery( table ).find( "tbody .yop_poll_tr_answer" ).length;
	if ( jQuery( '#yop-poll-allow-other-answers-yes' ).attr('checked') == 'checked' )
		jQuerycount = jQuerycount + 1;
	return jQuerycount;
}

function yop_poll_count_number_of_answer_without_other ( table ) {
	var jQuerycount = 0;
	jQuerycount = jQuery( table ).find( "tbody .yop_poll_tr_answer" ).length;
	return jQuerycount;
}

function yop_poll_count_number_of_customfields ( table ) {
	var jQuerycount = jQuery( table ).find( "tbody .yop_poll_tr_customfields" ).length;
	return jQuerycount;
}

function yop_poll_reorder_answer( table ) {
	jQuerytr = jQuery( table ).find( "tbody .yop_poll_tr_answer" );
	jQuerytr.each( function ( index, value ) {
		jQuery( this ).find(".yop_poll_answer_label").html( yop_poll_add_new_config.text_answer + ' ' + parseInt(index + 1) ) ;
	});
	return false;
}

function yop_poll_reorder_customfields( table ) {
	jQuerytr = jQuery( table ).find( "tbody .yop_poll_tr_customfields" );
	jQuerytr.each( function ( index, value ) {
		jQuery( this ).find(".yop_poll_customfield_label").html( yop_poll_add_new_config.text_customfield + ' ' + parseInt(index + 1) ) ;
	});
	return false;
}

function yop_poll_remove_answer( table, answer_id ) {
	if ( yop_poll_count_number_of_answer ( table ) >= 2 ) {
		jQuery( '#yop_poll_tr_answer' + answer_id ).fadeOut( 'medium', function () {
			jQuery( this ).remove();
			yop_poll_reorder_answer( table );
			return false;
		});
		jQuery( '#yop_poll_tr_customize_answer' + answer_id ).fadeOut( 'medium', function () {
			jQuery( this ).remove();
			return false;
		});
	}
	return false;
}

function yop_poll_remove_customfield( table, customfield_id ) {
	jQuery( '#yop_poll_tr_customfield' + customfield_id ).fadeOut( 'medium', function () {
		jQuery( this ).remove();
		yop_poll_reorder_customfields( table );
		return false;
	});
	return false;
}

function yop_poll_update_bar_style( obj, property, value ) {
	if(
		'background-color' == property ||
		'height' == property ||
		'border-color' == property ||
		'border-width' == property ||
		'border-style' == property ) {
		if( jQuery( obj ).length > 0 )
		{
			if( '' != value )
				jQuery( obj ).css( property , value );
		}
	}
}

function yop_poll_toogle_customize_answer( table, answer_id ) {
	jQuery( '#yop_poll_tr_customize_answer' + answer_id ).toggle( 'medium' );
	return false;
}

function yop_poll_show_change_votes_number_answer( answer_id ) {
	jQuery.fn.modalBox({
		directCall: {
			source : yop_poll_add_new_config.ajax.url + '?action=yop_poll_show_change_votes_number_answer&answer_id=' + answer_id
		},
		disablingTheOverlayClickToClose : true
	});
	return false;
}

function yop_poll_show_change_total_number_poll( poll_id, type ) {
	jQuery.fn.modalBox({
		directCall: {
			source : yop_poll_add_new_config.ajax.url + '?action=yop_poll_show_change_total_number_poll&poll_id=' + poll_id + '&type=' + type
		},
		disablingTheOverlayClickToClose : true
	});
	return false;
}

function yop_poll_show_change_poll_author( poll_id ) {
	jQuery.fn.modalBox({
		directCall: {
			source : yop_poll_add_new_config.ajax.url + '?action=yop_poll_show_change_poll_author&poll_id=' + poll_id
		},
		disablingTheOverlayClickToClose : true
	});
	return false;
}

function yop_poll_do_change_votes_number_answer( answer_id ) {
	jQuery.ajax({
		type: 'POST',
		url: yop_poll_add_new_config.ajax.url,
		data: 'action=yop_poll_do_change_votes_number_answer'+'&'+jQuery( "#yop-poll-change-answer-no-votes-form" ).serialize(),
		cache: false,
		beforeSend: function() {
			jQuery('#yop-poll-change-no-votes-error').html('<p>' + yop_poll_add_new_config.ajax.beforeSendMessage + '</p>');
		},
		error: function() {
			jQuery('#yop-poll-change-no-votes-error').html('<p>' + yop_poll_add_new_config.ajax.errorMessage + '</p>');
		},
		success:
		function( data ){
			data = yop_poll_extractApiResponse( data );
			jQuery('#yop-poll-change-no-votes-error').html('<p>' + data + '</p>');
			if ( ! jQuery('#yop-poll-update-answers-with-logs').prop('checked' ) ) {
				jQuery('#yop-poll-change-no-votes-button-' + answer_id).val( yop_poll_add_new_config.text_change_votes_number_answer + ' (' + jQuery('#yop-poll-answer-no-votes' ).val() + ')' );
				if ( jQuery('#yop-poll-change-to-all-poll-answers').prop('checked') )
					jQuery('.yop-poll-change-no-votes-buttons').val( yop_poll_add_new_config.text_change_votes_number_answer + ' (' + jQuery('#yop-poll-answer-no-votes' ).val() + ')' );
			}
			if ( jQuery('#yop-poll-update-answers-with-logs').prop('checked') )
				setTimeout('location.reload();', 100 );
		}
	});
}

function yop_poll_do_change_poll_author( poll_id ) {
	jQuery.ajax({
		type: 'POST',
		url: yop_poll_add_new_config.ajax.url,
		data: 'action=yop_poll_do_change_poll_author'+'&'+jQuery( "#yop-poll-change-poll-author-form" ).serialize(),
		cache: false,
		beforeSend: function() {
			jQuery('#yop-poll-change-poll-author-error').html('<p>' + yop_poll_add_new_config.ajax.beforeSendMessage + '</p>');
		},
		error: function() {
			jQuery('#yop-poll-change-poll-author-error').html('<p>' + yop_poll_add_new_config.ajax.errorMessage + '</p>');
		},
		success:
		function( data ){
			data = yop_poll_extractApiResponse( data );
			jQuery('#yop-poll-change-poll-author-error').html('<p>' + data + '</p>');
			jQuery('#yop-poll-change-poll-author-container-' + poll_id).html( '<b>' + jQuery('#yop-poll-author-select option[value='+jQuery('#yop-poll-author-select').val()+']' ).text() + '</b>' );
		}
	});
}

function yop_poll_do_change_total_number_poll( poll_id, type ) {
	jQuery.ajax({
		type: 'POST',
		url: yop_poll_add_new_config.ajax.url,
		data: 'action=yop_poll_do_change_total_number_poll'+'&'+jQuery( "#yop-poll-change-poll-total-no-form" ).serialize(),
		cache: false,
		beforeSend: function() {
			jQuery('#yop-poll-change-total-no-error').html('<p>' + yop_poll_add_new_config.ajax.beforeSendMessage + '</p>');
		},
		error: function() {
			jQuery('#yop-poll-change-total-no-error').html('<p>' + yop_poll_add_new_config.ajax.errorMessage + '</p>');
		},
		success:
		function( data ){
			data = yop_poll_extractApiResponse( data );
			jQuery('#yop-poll-change-total-no-error').html('<p>' + data + '</p>');
			if ( ! jQuery('#yop-poll-update-poll-with-logs').prop('checked') && ! jQuery('#yop-poll-update-poll-with-answers').prop('checked' ) ) {
				if ( 'votes' == type )
					jQuery('#yop-poll-change-no-votes-poll-container-' + poll_id ).html( '<b>' +jQuery('#yop-poll-total-votes' ).val() + '</b>' );
				if ('answers' == type )
					jQuery('#yop-poll-change-no-answers-poll-container-' + poll_id ).html( '<b>' +jQuery('#yop-poll-total-answers' ).val() + '</b>' );
			}
			if ( jQuery('#yop-poll-update-poll-with-logs').prop('checked') )
				setTimeout('location.reload();', 100 );
			if ( jQuery('#yop-poll-update-poll-with-answers').prop('checked') )
				setTimeout('location.reload();', 100 );
		}
	});
}

function yop_poll_extractApiResponse( str ) {
	var patt	= /\[response\](.*)\[\/response\]/m;
	resp 		= str.match( patt )
	return resp[1];
}

function yop_poll_return_template_preview( template_id, destination, location) {
	dest = jQuery(destination);
	if( '' == template_id )	{
		dest.html('');
	}
	else {
		var t_data = {
			action : 'yop_poll_preview_template',
			template_id: template_id,
			loc: location
		}
		jQuery.ajax({
			type: 'POST',
			url: yop_poll_add_new_config.ajax.url,
			data: t_data,
			beforeSend: function() {
				dest.html('<p>' + yop_poll_add_new_config.ajax.beforeSendMessage + '</p>');
			},
			error: function() {
				dest.html('<p>' + yop_poll_add_new_config.ajax.errorMessage + '</p>');
			},
			success: function( data ) {
				dest.html(data);
			}
		});
	}
}