jQuery(document).ready(function(jQuery) {
		var yopPollLogStartDateTextBox = jQuery('#yop-poll-logs-start-date-input');
		var yopPollLogEndDateTextBox = jQuery('#yop-poll-logs-end-date-input');
		
		yopPollLogStartDateTextBox.datepicker({ 			
			dateFormat: 'yy-mm-dd'
		});
		yopPollLogEndDateTextBox.datepicker({ 
			dateFormat: 'yy-mm-dd'
		}); 
});