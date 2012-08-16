jQuery(document).ready(function() {
		
	jQuery('td.column-se-actions input.se-term-input').click(function(){
		//alert('click');
		var input_name = jQuery(this).attr('name');
		var is_checked = jQuery(this).attr('checked');
		var label = jQuery(this).parent('td.column-se-actions');


		if (is_checked == 'checked') is_checked = "yes";
		else is_checked = "no";
		
		//alert('input_name=['+input_name+'] checked=['+is_checked+']');
		//se_send_update(input_name, is_checked);
		
		var data = {
			action: 'se_update',
			se_action: 'se_update_terms',
			se_cfg: input_name,
			is_checked: is_checked
		};

		jQuery.post(ajaxurl, data, function(response) {
			if (response == "SUCCESS")	 // SUCCESS is yellow fade to white
				jQuery(label).stop().css("background-color", "#FFFF9C").animate({ backgroundColor: "#FFFFFF"}, 1500);
			else // On !SUCCESS is red fade to white
				jQuery(label).stop().css("background-color", "#FF0000").animate({ backgroundColor: "#FFFFFF"}, 1500);				
		});					
	});

	//jQuery('table.simplyexclude-actions-panel input[type="radio"]').click(function(){
	jQuery('table.simply-exclude-settings-postbox input[type="radio"]').click(function(){
		
		//alert('click');
		var input_name = jQuery(this).attr('name');
		var value = jQuery(this).attr('value');
		var label = jQuery(this).parent('td.inc-excl');
		
		//alert("input_name=["+input_name+"] value=["+value+"]");
		
		var data = {
			action: 'se_update',
			se_action: 'se_update_actions',
			se_cfg: input_name,
			is_checked: value
		};

		jQuery.post(ajaxurl, data, function(response) {			
			if (response == "SUCCESS") // SUCCESS is yellow fade to white
				jQuery(label).stop().css("background-color", "#FFFF9C").animate({ backgroundColor: "#FFFFFF"}, 1500);
			else	// On !SUCCESS is red fade to white
				jQuery(label).stop().css("background-color", "#FF0000").animate({ backgroundColor: "#FFFFFF"}, 1500);			
		});					
	});
				
	function se_show_actions_panel()
	{
		var dialog_buttons = {};
		
		dialog_buttons['Close'] = function() { jQuery("#se-actions-panel").dialog('close'); }
		jQuery("#se-actions-panel").dialog({
			title: "Simply Exclude: Manage Actions",
			autoOpen: false,
			width: 650,
			autoResize:true,
			resizable: true,
			buttons: dialog_buttons
		});
		jQuery("#se-actions-panel").dialog('open');		
	}
});
