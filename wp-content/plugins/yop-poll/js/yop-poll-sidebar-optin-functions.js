jQuery(document).ready(function(jQuery) {
	jQuery('.wf-button[value="Thank You!"]').waitUntilExists(function() {
		jQuery.ajax({
			type: 'GET',
			url: yop_poll_sidebar_functions_config.ajax.url,
			data: 'action='+yop_poll_sidebar_functions_config.ajax.action,
			success: function(){
				jQuery('#linksubmitdiv3').fadeOut();
			}
		});
	});
});

(function ($) {
	jQuery.fn.waitUntilExists    = function (handler, shouldRunHandlerOnce, isChild) {
		var found       = 'found';
		var $this       = jQuery(this.selector);
		var $elements   = $this.not(function () { return jQuery(this).data(found); }).each(handler).data(found, true);

		if (!isChild)
		{
			(window.waitUntilExists_Intervals = window.waitUntilExists_Intervals || {})[this.selector] =
			window.setInterval(function () { $this.waitUntilExists(handler, shouldRunHandlerOnce, true); }, 500)
			;
		}
		else if (shouldRunHandlerOnce && $elements.length)
		{
			window.clearInterval(window.waitUntilExists_Intervals[this.selector]);
		}

		return $this;
	}
}(jQuery));

