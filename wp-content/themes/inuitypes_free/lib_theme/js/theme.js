jQuery(document).ready(function() {

    // Initiate jQuery Dropdown navigation
    jQuery('ul.sf-menu').superfish();
	
	// Initiate looped slider
	jQuery("#loopedSlider").loopedSlider({
		autoStart: my_fslider.autoStart, 
		slidespeed: my_fslider.slidespeed, 
		autoHeight: my_fslider.autoHeight
	});
	
	// Equals column height s		
	var highestCol = Math.max(jQuery('.mainbar').height(),jQuery('.sidebar').height());
	jQuery('.equalh').css('min-height',highestCol+'px');

});