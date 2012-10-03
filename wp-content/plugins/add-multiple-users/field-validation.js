//Add Multiple Users by Happy Nuclear
//On-Screen Validation methods
//compiled for AMU version 1.2.2

var holdTitle, tiptarget, timeoutID = 0, fieldVal, fieldKey;

jQuery(document).ready(function() {
	
	//run on addition to page
	if (jQuery('.fieldsetwrap')) {
		addValidationFields();
		checkUsernames();
		checkPasswords();
		checkEmails();
	}
	
	//capture any keystroke in a validation field
	jQuery('.validatefield').live('keyup', function() {
		fieldVal = jQuery(this).val();
		fieldKey = jQuery(this).attr('name');
		clearTimeout(timeoutID);
		timeoutID = setTimeout('processKey(fieldVal,fieldKey)', 500);
	});
	
	//show hovering tooltip
	jQuery('.validatefield').live('mouseover',function(e) {
			tiptarget = jQuery(this).attr('id');
			jQuery('#'+tiptarget+'_tip').show();
			jQuery('#'+tiptarget+'_tip').css('left',e.pageX);
			jQuery('#'+tiptarget+'_tip').css('top',e.pageY-60);
		}).live('mousemove',function(e) {
			jQuery('#'+tiptarget+'_tip').css('left',e.pageX);
			jQuery('#'+tiptarget+'_tip').css('top',e.pageY-60);
		}).live('mouseout',function(e) {
			tiptarget = jQuery(this).attr('id');
			jQuery('#'+tiptarget+'_tip').hide();
		});	
});

function addValidationFields() {
	//add hidden notices
	jQuery('.validatefield').each(function() {
		var fid = jQuery(this).attr('id');
		jQuery('<div id="'+fid+'_tip" class="amutooltip"></div>').insertAfter(jQuery(this));
		if (jQuery(this).hasClass('valusername')) {
			jQuery('#'+fid+'_tip').html('Empty (row will be skipped).');
		}
		if (jQuery(this).hasClass('valpassword')) {
			jQuery('#'+fid+'_tip').html('Empty (password will be generated).');
		}
		if (jQuery(this).hasClass('valemail')) {
			jQuery('#'+fid+'_tip').html('Empty (enter an email address).');
		}
	});
}

function checkUsernames() {
	jQuery('.valusername').each(function() {
		var thisFieldID = jQuery(this).attr('name').substr(8);
		var usernameVal = jQuery(this).val();
		var action = 'UserNameValidation';
		jQuery(this).removeClass('amuvalid amuerror');
		if (jQuery('#validateStrict').attr('checked')) {
			sanitizeStrict = 'yes';
		} else {
			sanitizeStrict = 'no';
		}
		validationU(thisFieldID,usernameVal,action,sanitizeStrict);
	});
}

function checkPasswords() {
	jQuery('.valpassword').each(function() {
		var thisPasswordID = (jQuery(this).attr('name')).substr(8);
		var passwordVal = jQuery(this).val();
		passwordStrength(passwordVal, thisPasswordID);
	});
}

function checkEmails() {
	jQuery('.valemail').each(function() {
		var thisEmailID = (jQuery(this).attr('name')).substr(5);
		var emailVal = jQuery(this).val();
		var action = 'EmailValidation';
		var doValidate = '';
		jQuery(this).removeClass('amuvalid amuerror');
		if (jQuery('#validatemail').attr('checked')) {
			doValidate = 'yes';
		} else {
			doValidate = 'no';
		}
		validateE(thisEmailID,emailVal,action,doValidate);
	});
}

//function to validate username
function validationU(thisFieldID,usernameVal,action,sanitizeStrict) {
	var targetField = '#username'+thisFieldID;
	var targetTip = targetField+'_tip';
	
	if (jQuery('#password'+thisFieldID).val() !== '') {
		var corPsw = jQuery('#password'+thisFieldID).val();
		passwordStrength(corPsw, thisFieldID);
	}
	
	if (usernameVal !== "") {
       	jQuery.post( MySecureAjax.ajaxurl, {action: action, thevars: usernameVal, sanstrict: sanitizeStrict}, function(data) {
			if (data == 'exists') {
				jQuery(targetTip).html('Error - username is already in use.');
				jQuery(targetField).addClass('amuerror');
			} else if (data == 'badchars') {
				jQuery(targetTip).html('Error - username contains unsafe characters.');
				jQuery(targetField).addClass('amuerror');
			} else {
				var i=0;
				jQuery('.valusername').each(function() {
					if (jQuery(this).val() == usernameVal) {
						i++;
					} 
				});
				if (i > 1) {
					jQuery(targetTip).html('Error - already entered elsewhere on form.');
					jQuery(targetField).addClass('amuerror');
				} else {
					jQuery(targetTip).html('Username is valid.');
					jQuery(targetField).addClass('amuvalid');
				}
			}		
       	});
	} else {
		jQuery(targetTip).html('Empty (row will be skipped).');
	}
}

//function to validate username
function validateE(thisEmailID,emailVal,action,doValidate) {
	var targetField = '#email'+thisEmailID;
	var targetTip = targetField+'_tip';
	
	if (emailVal !== "") {
		jQuery.post( MySecureAjax.ajaxurl, {action: action, thevars: emailVal, isValidated: doValidate}, function(data) {
			if (doValidate == 'yes') {
				if (data == 'emailinvalid') {
					jQuery(targetField).addClass('amuerror');
					jQuery(targetTip).html('Error - not a valid email address.');
					return;
				}
			}
			if (data == 'exists') {
				jQuery(targetField).addClass('amuerror');
				jQuery(targetTip).html('Error - email address already in use.');
			} else {
				var j=0;
				jQuery('.valemail').each(function() {
					if (jQuery(this).val() == emailVal) {
						j++;
					}
				});
				if (j > 1) {
					jQuery(targetField).addClass('amuerror');
					jQuery(targetTip).html('Error - already entered elsewhere on form.');
				} else {
					jQuery(targetField).addClass('amuvalid');
					jQuery(targetTip).html('Email address is available for use.');
				}
			}		
		});
	} else {
		jQuery(targetTip).html('Empty (enter an email address).');
	}
}

// Password strength meter
//adapted from wordpress standard strength meter
function passwordStrength(password1, fieldID) {
	var score = 0;
	var username = jQuery('#username'+fieldID).val();
	var targetField = '#password'+fieldID;
	var targetPTip = targetField+'_tip';
	
	jQuery(targetField).removeClass('amushort amubad amugood amustrong amumatchuser');
	
	if ( password1 !== '') {
		if ( password1.toLowerCase() == username.toLowerCase() ) {
			jQuery(targetField).addClass('amumatchuser');
			jQuery(targetPTip).html('Password should not be the same as the username.');
		} else if ( password1.length < 4 ) {
			//return red bg class - too short
			jQuery(targetField).addClass('amushort');
			jQuery(targetPTip).html('Password is too short.');
		} else {
			//calculate strength
			if ( password1.match(/[0-9]/) ){
				score +=10;
			}
			if ( password1.match(/[a-z]/) ) {
				score +=26;
			}
			if ( password1.match(/[A-Z]/) ) {
				score +=26;
			}
			if ( password1.match(/[^a-zA-Z0-9]/) ) {
				score +=31;
			}
			if (score < 40 ) {
				jQuery(targetField).addClass('amubad');
				jQuery(targetPTip).html('Password score: '+score+' - Poor.');
			} else if (score < 56 ) {
				jQuery(targetField).addClass('amugood');
				jQuery(targetPTip).html('Password score: '+score+' - Good.');
			} else {
				jQuery(targetField).addClass('amustrong');
				jQuery(targetPTip).html('Password score: '+score+' - Strong.');
			}
		}
	} else {
		jQuery(targetPTip).html('Empty (password will be generated).');
	}
}
function processKey(fieldVal,fieldKey) {	
		
	var doValidate = '';
	var sanitizeStrict = '';
	
	if (jQuery('#validatemail').attr('checked')) {
		doValidate = 'yes';
	} else {
		doValidate = 'no';
	}
	
	if (jQuery('#validateStrict').attr('checked')) {
		sanitizeStrict = 'yes';
	} else {
		sanitizeStrict = 'no';
	}
	
	if (jQuery('#'+fieldKey).hasClass('valusername')) {
		var thisFieldID = fieldKey.substr(8);
		var action = 'UserNameValidation';
		jQuery('#username'+thisFieldID).removeClass('amuvalid amuerror');
		validationU(thisFieldID,fieldVal,action,sanitizeStrict);
	}
	
	if (jQuery('#'+fieldKey).hasClass('valpassword')) {
		var thisFieldID = fieldKey.substr(8);
		passwordStrength(fieldVal, thisFieldID);
	}

	//if is email field
	if (jQuery('#'+fieldKey).hasClass('valemail')) {
		var thisEmailID = fieldKey.substr(5);
		var action = 'EmailValidation';
		jQuery('#email'+thisEmailID).removeClass('amuvalid amuerror');
		validateE(thisEmailID,fieldVal,action,doValidate);
	}
}