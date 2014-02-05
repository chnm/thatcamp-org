$ = jQuery.noConflict();
jQuery(document).ready(function($) {
	$('.photonic-helper-box input[type="button"]').click(function() {
		$('.photonic-waiting').show();
		var formValues = $('#photonic-helper-form').serialize();
		var result = $(this).parent('.photonic-helper-box').find('.result');
		$.post(ajaxurl, "action=photonic_invoke_helper&helper=" + this.id + '&' + formValues, function(data) {
			$(result).html(data);
			$('.photonic-waiting').hide();
		});
	});
});
