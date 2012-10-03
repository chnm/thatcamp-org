//Add Multiple Users by Happy Nuclear
//Multiadd Actions
//compiled for AMU version 1.2.2

jQuery(document).ready(function() {
	
	//FADE OUT WORDPRESS MESSAGES
	if (jQuery('#message')) {
		jQuery('#message').delay(3000).slideUp();
	}
	
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
	
	//add dynamic emailer button to customisation box for testing email
	if (jQuery('.emailcustbox')) {
		jQuery('<input type="button" name="testCustomEmail" id="testCustomEmail" class="button-primary button-right" value="Send Test Email" />').appendTo('.emailcustbox');
		jQuery('<p><span class="important">Test emails will be sent to your administrator email.</span></p>').appendTo('.emailcustbox');
	}
	
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
	
	//add extras row button
	if (jQuery('.fieldsetwrap')) {
		jQuery('<input type="button" name="addNewRow" class="button-primary button-right addNewRow" value="Add Extra Row" />').appendTo('.addextrasbutton');
	}
	//if that button is clicked...
	jQuery('.addNewRow').click(function() {
		
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
		jQuery('<div class="formwrap '+wrapcolor+'"><div class="formline"><span class="countline">'+order+'</span><label for="username'+order+'">Username</label><input type="text" name="username'+order+'" id="username'+order+'"  class="valusername validatefield" value="" /><div id="username'+order+'_tip" class="amutooltip">Empty (row will be skipped.</div><label for="password'+order+'">Password</label><input type="text" name="password'+order+'" id="password'+order+'" class="valpassword validatefield" value="" /><div id="password'+order+'_tip" class="amutooltip">Empty (password will be generated).</div><label for="email'+order+'">Email</label><input type="text" name="email'+order+'" id="email'+order+'" class="valemail validatefield" value="" /><div id="email'+order+'_tip" class="amutooltip">Empty (enter an email address).</div><label for="roleSetter'+order+'">UserRole</label><select name="roleSetter'+order+'" id="roleSetter'+order+'"><option value="subscriber" selected="selected">subscriber</option><option value="contributor">contributor</option><option value="author">author</option><option value="editor">editor</option><option value="administrator">administrator</option></select></div><div class="formline"><span class="countline">&nbsp;</span><label for="firstname'+order+'">FirstName</label><input type="text" name="firstname'+order+'" id="firstname'+order+'" value="" /><label for="lastname'+order+'">LastName</label><input type="text" name="lastname'+order+'" id="lastname'+order+'" value="" /><label for="website'+order+'">Website</label><input type="text" name="website'+order+'" id="website'+order+'" value="" /></div></div>').appendTo('.fieldsetwrap');
	});
	
	//LOAD INITIAL DATA FOR SORTING FUNCTION
	if (document.getElementById('sortfield')) {
		//add activation checkbox
		jQuery('<label for="opentextsorter">Customize column order: </label><input name="opentextsorter" id="opentextsorter" type="checkbox" value="open" />').appendTo('#sortfield');
		//add textsorter wrap
		jQuery('<div id="textsorter"></div>').appendTo('#sortfield');
		
	}
	if(document.getElementById('textsorter')) {
		//create array of default values
		var loadarray = new Array();
		//push values
		loadarray.push("username","password","email","role","firstname","lastname","website");
		//push to creator
		makeColumnsList(loadarray);
		//hide
		jQuery('#textsorter').hide();
	}
	//end opening function
	
	//OPEN or CLOSE #textsorter on option change
	jQuery('#opentextsorter').live('change', function() {
		if (jQuery('#opentextsorter').attr('checked')) {
			jQuery('#textsorter').slideDown();
		} else {
			jQuery('#textsorter').slideUp();
		}
	});
	
	//on drop box changes
	jQuery('#textsorter select').live('change', function() {
		var chosen = jQuery(this).val();
		checkDuplicates(chosen);
		jQuery(this).attr('id',chosen);
	});
	
	//ADD NEW COLUMN
	jQuery('.coladdnew').live('click', function() {
		
		//id of clicked addnew span
		var thisColID = jQuery(this).attr('id');
		var thisColNum = parseInt(thisColID.substr(3));
		
		//create array
		var allCols = new Array();
		//make array of existing cols
		
		var countCols = 1;
		
		jQuery('#textsorter select').each(function(i) {
			var colval = jQuery(this).val();
			
			if(countCols == thisColNum) {
				allCols.push(colval);
				allCols.push('ignore');
			} else {
				allCols.push(colval);
			}
			countCols++;
		});
		//push to creator
		makeColumnsList(allCols);
	});
	
	//DELETE COLUMN
	jQuery('.coldelete').live('click', function() {
		
		//id of clicked addnew span
		var thisColID = jQuery(this).attr('id');
		var thisColNum = parseInt(thisColID.substr(3));
		
		//create array
		var allCols = new Array();
		//make array of existing cols
		
		jQuery('#textsorter select').each(function(i) {
			var colval = jQuery(this).val();
			allCols.push(colval);
		});
		
		//remove deleted entry
		allCols.splice(thisColNum-1,1);
		//push to creator
		makeColumnsList(allCols);
	});
	
	//SHIFT COLUMN LEFT
	jQuery('.colleft').live('click', function() {
		//id of clicked addnew span
		var thisColID = jQuery(this).attr('id');
		var thisColNum = parseInt(thisColID.substr(3));
		var allCols = new Array();
		
		//countcolumns
		jQuery('#textsorter select').each(function(i) {
			var colval = jQuery(this).val();
			allCols.push(colval);
		});
		
		//if far left column has been chosen, show error
		if(thisColNum == 1) {
			alert('Column cannot be shifted left!');
		} else {
			//swap position of chosen column with next
			var prevColumn = allCols[thisColNum-2];
			var thisColumn = allCols[thisColNum-1];
			allCols.splice(thisColNum-2,2,thisColumn,prevColumn);
			makeColumnsList(allCols);
		}
	});
	
	//SHIFT COLUMN RIGHT
	jQuery('.colright').live('click', function() {
		//id of clicked addnew span
		var thisColID = jQuery(this).attr('id');
		var thisColNum = parseInt(thisColID.substr(3));
		var allCols = new Array();
		
		//countcolumns
		var totalCols = 0;
		jQuery('#textsorter select').each(function(i) {
			var colval = jQuery(this).val();
			allCols.push(colval);
			totalCols++;
		});
		
		//if far right column has been chosen, show error
		if (thisColNum == totalCols) {
			alert('Column cannot be shifted right!');
		} else {
			//swap position of chosen column with next
			var thisColumn = allCols[thisColNum-1];
			var nextColumn = allCols[thisColNum];
			allCols.splice(thisColNum-1,2,nextColumn,thisColumn);
			makeColumnsList(allCols);
		}
	});
	
});

//makes or remakes all columns in the list
function makeColumnsList(colsarray) {
	
	//clear existing text boxes
	jQuery('#textsorter').html('');
	
	//total number of cols in array
	var arrayEntries = colsarray.length;
	var i=0;

	for (i=0; i<arrayEntries; i++) {
		var j = i+1;
		jQuery('<div class="optcol"><div class="optcolline"><select class="sortoption'+j+'"><option value="username">username</option><option value="password">password</option><option value="email">email</option><option value="role">role</option><option value="firstname">firstname</option><option value="lastname">lastname</option><option value="website">website</option><option value="ignore">ignore</option></select></div><div class="optcolline"><span id="lft'+j+'" class="colleft" title="Shift Column Left">&larr;</span><span id="rgt'+j+'" class="colright" title="Shift Column Right">&rarr;</span><span id="del'+j+'" class="coldelete" title="Delete Column">x</span><span id="add'+j+'" class="coladdnew" title="Add New Column">+</span></div></div>').appendTo('#textsorter');
		jQuery('select.sortoption'+j).val(colsarray[i]);
	}
	setIDs();
}

//set ids and names on fields
function setIDs() {
	var namer = 1;
	jQuery('#textsorter select').each(function(i) {
		var targetSetID = jQuery(this).val();
		jQuery(this).attr('id', targetSetID);
		jQuery(this).attr('name', namer);
		namer++;
	});
}

//function to replace found duplicates
function checkDuplicates(chosen) {
	var targetid = chosen;
	jQuery('#'+targetid).val('ignore');
	jQuery('#'+targetid).attr('id','ignore');
}