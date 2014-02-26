jQuery(document).ready(function(jQuery) {
		var yopPollCustomFieldStartDateTextBox = jQuery('#yop-poll-custom-field-start-date-input');
		var yopPollCustomFieldEndDateTextBox = jQuery('#yop-poll-custom-field-end-date-input');
		
		yopPollCustomFieldStartDateTextBox.datepicker({ 			
			dateFormat: 'yy-mm-dd'
		});
		yopPollCustomFieldEndDateTextBox.datepicker({ 
			dateFormat: 'yy-mm-dd'
		}); 
});