jQuery(document).ready(function() {
	      	jQuery('#yop-poll-add-new-ban').click( function() {
			jQuery('#yop-poll-add-ban-div').fadeIn('medium');
                jQuery( "#add-new-ban" ).val( "Add Ban" );
		    jQuery('#yop-poll-ban-period').val("");
            jQuery( '#yop-poll-ban-value' ).val("");
            jQuery("#yop-poll-bans-type option").filter(function() {
                return jQuery(this).val() == "";
            }).attr('selected', true);
            jQuery("#yop-poll-bans-unit option").filter(function() {
                return jQuery(this).val() == "";
            }).attr('selected', true);
            jQuery("#yop-poll-bans-polls option").filter(function() {
                return jQuery(this).val() ==0;
            }).attr('selected', true);
                jQuery('#message').hide();
            });
		    jQuery('#yop-poll-add-ban-close').click( function() {
	        jQuery('#yop-poll-add-ban-div').fadeOut('medium');
		    });


});		