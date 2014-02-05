jQuery(document).ready(function($) {
	var options = false, delBefore, delAfter;

	delAfter = function( r, settings ) {
		var id = $('cat', r).attr('id'), o;
		for ( o = 0; o < options.length; o++ )
			if ( id == options[o].value )
				options[o] = null;
	};

	delBefore = function(s) {
		if ( 'undefined' != showNotice )
			return showNotice.warn() ? s : false;

		return s;
	};

	$('#the-list').wpList({ delBefore: delBefore });

	$('.delete a[class^="delete"]').live('click', function(){return false;});
});