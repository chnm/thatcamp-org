function yop_poll_show_modal_box( element ) {
	jQuery.fn.modalBox({
		directCall: {
			source : jQuery( element ).attr('href')
		},
		disablingTheOverlayClickToClose : true,
		selectorModalboxCloseContainer: "#yop-poll-close-modal-box",
		selectorCloseModalBox: "#yop-poll-close-modal-box span",
		selectorModalboxBodyContainer: "#WFItem394041"
	});
    return false;
}

jQuery(document).ready(function(jQuery) {
	yop_poll_show_modal_box( '#yop-poll-show-modal-box' );
	jQuery('.wf-button[value="Thank You!"]').waitUntilExists(function() {
		jQuery.ajax({
			type: 'GET',
			url: yop_poll_modal_functions_config.ajax.url,
			data: 'action='+yop_poll_modal_functions_config.ajax.action,
			success: function(){
				jQuery.fn.modalBox("close");
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

