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
		$tcstate = $('#thatcamp-state');
		$tcprovince = $('#thatcamp-province');
		$tcstate.hide();
		$tcprovince.hide();

		var current_country = $country_selector.val();

		// Remove the province and state, and reset if necessary
		// Don't know why I have to do this
		var current_state = $tcstate.val();
		var current_province = $tcprovince.val();
		$tcstate.find(':selected').each( function(i){ $(this).removeAttr('selected'); } );
		$tcprovince.find(':selected').each( function(i){ $(this).removeAttr('selected'); } );

		if ( 'United States' === current_country ) {
			$tcstate.val(current_state);
			$tcstate.show();
		} else if ( 'Canada' === current_country ) {
			$tcstate.val(current_province);
			$tcprovince.show();
		}
	}
},(jQuery));
