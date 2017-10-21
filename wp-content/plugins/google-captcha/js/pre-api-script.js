;var gglcptch_pre = gglcptch_pre || {};
gglcptch_pre.is_loaded = false;

function gglcptch_alert_fail_message( e ) {
	if ( ! gglcptch_pre.is_loaded ) {
		e.preventDefault();
		e.stopImmediatePropagation();
		alert( gglcptch_pre.messages.timeout );
	}
}

function gglcptch_onload_callback() {
	( function( $ ) {
		if ( '' != gglcptch_pre.custom_callback ) {
			eval( gglcptch_pre.custom_callback );
		}
		gglcptch_pre.is_loaded = true;
		$( document ).ready( function() {
			$( 'form input:submit, form button' ).filter('[data-gglcptch_disabled]')
			.removeAttr( 'disabled' )
			.removeAttr( 'data-gglcptch_disabled' )
			.each( function() {
				$( this ).restoreTitle();
			} );
		} );
	} )( jQuery );
}

( function( $ ) {
	$.fn.storeTitle = function() {
		var title = ( typeof $( this ).attr( 'title' ) != 'undefined' ) ? $( this ).attr( 'title' ) : '';
		this.attr( 'data-storedTitle', title );
	}

	$.fn.restoreTitle = function() {
		var title = this.attr( 'data-storedTitle' );
		if ( '' != title ) {
			this.attr( 'title' ) = title;
		} else {
			this.removeAttr( 'title' );
		}
		this.removeAttr( 'data-storedTitle' );
	}

	$( document ).ready( function() {
		if ( ! gglcptch_pre.is_loaded ) {
			$( '.gglcptch_v2, .gglcptch_invisible' ).each( function() {
				$( this ).closest( 'form' )
				.find( 'input:submit, button' ).filter( ':not(:disabled)' )
				.attr( 'disabled', 'disabled' ).attr( 'data-gglcptch_disabled', 'true' )
				.each( function() {
					$( this ).storeTitle();
					$( this ).attr( 'title', gglcptch_pre.messages.in_progress );
				} );
			} );
		}
	} );

	$( window ).on( 'load', function() {
		if ( ! gglcptch_pre.is_loaded ) {
			$( '[data-gglcptch_disabled]' ).removeAttr( 'disabled' ).one( 'click', gglcptch_alert_fail_message );
		}
	} );
} )( jQuery );