jQuery(document).ready(function($){
	$('#thatcamp_start_date').datepicker();
	$('#thatcamp_end_date').datepicker();

	var $country_selector = $('#country-selector');
	$country_selector.selectToAutocomplete();
	$('#state-selector').selectToAutocomplete();
	$('#province-selector').selectToAutocomplete();

	refresh_state_province_selectors();

	$country_selector.on( 'change', function() { refresh_state_province_selectors(); } );

	$('.remove-selector-value').on('click', function(e){
		var sibling_input = $(this).siblings('input');

		if ( ! sibling_input ) {
			sibling_input = $(this).siblings('select');
		}

		$(sibling_input).val('');

		return false;
	});

	function refresh_state_province_selectors() {
		$('#thatcamp-state').hide();
		$('#thatcamp-province').hide();

		var current_country = $country_selector.val();

		if ( 'United States' === current_country ) {
			$('#thatcamp-state').show();
		} else if ( 'Canada' === current_country ) {
			$('#thatcamp-province').show();
		}
	}
},(jQuery));
