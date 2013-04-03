//Add Multiple Users by Happy Nuclear
//Multiadd Actions
//compiled for AMU version 2.0.0

jQuery(document).ready(function() {
	
	//FADE OUT WORDPRESS MESSAGES
	if (jQuery('#message').length > 0) {
		jQuery('#message').delay(3000).slideUp();
	}
	
	//hide quickref
	jQuery('#amuQuickRef').hide();
	//show quickref on class 
	jQuery('.quickref').click(function() {
		if(jQuery('#amuQuickRef').is(':visible')) {
			jQuery('#amuQuickRef').slideUp();
		} else {
			jQuery('#amuQuickRef').slideDown();
		}
	});
	
	//select all existing users
	jQuery('.checkallex').show();
	jQuery('#checkallexisting').change(function() {
		if(this.checked){
			jQuery('.userbox').attr('checked', true);
		} else {
			jQuery('.userbox').attr('checked', false);
		}
	});
	
	//prevent pressing enter from triggering general options
	jQuery('.amuform').bind("keypress", function(e) {
    	if (e.keyCode == 13) {
			if (jQuery('.textfillbox').is(':focus')) {
				//allow enter
			} else {
				//disallow enter
	        	return false;
			}
    	}
    });
	
	//clear field styles on reset
	jQuery('.formresetter').click(function() {
		//remove coloring
		jQuery('.fieldsetwrap input').removeClass('amuvalid amuerror amushort amubad amugood amustrong amumatchuser');
		//reset tooltips
		jQuery('.validatefield').each(function() {
			var fid = jQuery(this).attr('id');
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
	});
	
	//send test email
	jQuery('#testCustomEmail').click(function() {
		//an action that matches the php function
		var action = 'OptionTestEmail';
		//get current variables from email customisation form
		var test_custademail = jQuery('#custademail').val();
		var test_custlogurl = jQuery('#custlogurl').val();
		var test_custemailhead = jQuery('#custemailhead').val();
		var test_customemailtext = jQuery('#customemailtext').val();
		
		//send vars to php function in multiadd.php
		jQuery.post( MySecureAjax.ajaxurl, {action: action, test_email: test_custademail, test_loginurl: test_custlogurl, test_mailhead: test_custemailhead, test_mailtext: test_customemailtext}, function() {
			alert('Message sent! Please check your email. Your current settings have NOT YET been saved.');
		});
	});
	
	//if that button is clicked...
	jQuery('.addNewRow').click(function() {
		
		//functionality enhanced in v2.0.0
		
		//updated process number
		var currentProcs = parseInt(jQuery('#processes').val());
		jQuery('#processes').val(currentProcs+1)
		
		//get next row number
		var order = jQuery('.fieldsetwrap .formwrap').length +1;
		var wrapcolor;
		//define color
		if (order & 1) {
			wrapcolor = 'wrapwhite';
		} else {
			wrapcolor = 'wrapgrey';
		}
		
		//clone the html from the first line
		var formlinehtml = jQuery('.formwrapnum1').html();
		
		//add it to a new linewrap at the end of the form
		jQuery('<div class="formwrap formwrapnum'+order+' '+wrapcolor+'">'+formlinehtml+'</div>').appendTo('.fieldsetwrap');
		
		//change the listing number
		jQuery('.formwrapnum'+order+' span.countline').html(order+'.');
		
		//strip validation styles from input fields of new row
		jQuery('.formwrapnum'+order+' input').removeClass('amuerror amuvalid amushort amubad amugood amustrong amumatchuser');
		
		//strip values from input fields of new row
		jQuery('.formwrapnum'+order+' input').val('');
		
		//update label names on new rows
		jQuery('.formwrapnum'+order+' label').each(function() {
			var currentFor = jQuery(this).attr('for');
			var currentForChars = parseInt(currentFor.length)-1;
			var currentForName = currentFor.substr(0, currentForChars);
			var newFor = currentForName+order;
			jQuery(this).attr('for',newFor);
		});
		
		//update input fields
		jQuery('.formwrapnum'+order+' input').each(function() {
			var currentID = jQuery(this).attr('id');
			var currentIDChars = parseInt(currentID.length)-1;
			var currentIDName = currentID.substr(0, currentIDChars);
			var newID = currentIDName+order;
			jQuery(this).attr('id',newID);
			jQuery(this).attr('name',newID);
		});
		
		//update select fields
		jQuery('.formwrapnum'+order+' select').each(function() {
			var currentID = jQuery(this).attr('id');
			var currentIDChars = parseInt(currentID.length)-1;
			var currentIDName = currentID.substr(0, currentIDChars);
			var newID = currentIDName+order;
			jQuery(this).attr('id',newID);
			jQuery(this).attr('name',newID);
		});
		
	});
	
	
	// -------------------------
	// DYNAMIC SORTER
	// -------------------------
		
	//if dynamic sorter exists, run sort list function
	if(document.getElementById('dynamicsorter')) {
		
		var removeIntent = false;
		
		//make list sortable, delete if dragged out
		jQuery( "#sorterlist" ).sortable({
		
			over: function() {
				removeIntent = false;
				recreateOrder();
			},
			out: function() {
				removeIntent = true;
				recreateOrder();
			},
			beforeStop: function(event,ui){
				if(removeIntent == true) {
					var putBack = ui.item.html();
					if(document.getElementById('dyn_'+putBack)) {
						jQuery('#dyn_'+putBack).show();
					}				
					ui.item.remove();
				}
			}
		
		});
		//prevent text selection
		jQuery( "#sorterlist" ).disableSelection();
		
	}
	
	//add to sort field from name click
	jQuery('#dyn_add_standard ul li a').live('click', function() {
		
		var columnName = jQuery(this).html();
		
		jQuery('<li>'+columnName+'</li>').appendTo('#sorterlist');
		
		if(columnName !== 'ignore') {
			jQuery(this).parent('li').hide();
			recreateOrder();
		}
		return false;
		
	});
	
	//add to sort field from name click
	jQuery('#dyn_add_custom #submitcustom').live('click', function() {
		
		var custColName = jQuery('#customcolumn').val();
		var safeCustName = custColName.replace(' ','');
		
		if(safeCustName !== '') {
			jQuery('<li>'+safeCustName+'</li>').appendTo('#sorterlist');
			jQuery('#customcolumn').val('');
			recreateOrder();
		}
		
		return false;
		
	});
	
	//on submit, compile thoughts...
	jQuery('#sendcsvtoform').live('click',function(event) {
		compileColumnOrder(event);
	});
	jQuery('#sendcsvtoreg').live('click',function(event) {
		compileColumnOrder(event);
	});
	
	
	
});

//prevent submission of empty column order
function compileColumnOrder(event) {
	if(document.getElementById('dynamicsorter')) {
		if(jQuery('#sorterlist').children('li').length == 0) {
			event.preventDefault();
			alert('Your column order is empty!');
		}
	}
}

//recreate hidden order column on add/move/remove
function recreateOrder() {
	
	//total iterations
	var allChildren = jQuery('#sorterlist').children('li').length;
	var count = 0;
	var newsorter = '';
	
	jQuery.each(jQuery('#sorterlist').children('li'), function(e) {
		count++;
		newsorter = newsorter + jQuery(this).html();
		if(count < allChildren) {
			newsorter = newsorter + ',';
		} else if (count == allChildren) {
			jQuery('#finalsort').val(newsorter);
		}
	});
}