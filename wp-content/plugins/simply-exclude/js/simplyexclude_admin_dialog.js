jQuery(document).ready(function() {
		
	jQuery('a#se-show-actions-panel').click(function(){

		var dialog_buttons = {};
		
		dialog_buttons['Close'] = function() { 
			jQuery("#se-actions-panel").dialog('close'); 
			
		}
		jQuery("#se-actions-panel").dialog({
			title: "Simply Exclude: Manage Actions",
			autoOpen: false,
			width: 650,
			autoResize:true,
			resizable: true,
			buttons: dialog_buttons
		});
		jQuery("#se-actions-panel").dialog('open');		
		
		return false;
	});
	
});
