( function( $ ) {
	$( document ).ready( function() {
		/*
		* Hide/show checkboxes for network settings on network settings page
		*/
		function gglcptch_network_apply() {
			if ( $( 'input[name="gglcptch_network_apply"]:checked' ).val() != 'all' ) {
				$( '.gglcptch_network_settings .bws_network_apply_all, #gglcptch_network_notice' ).hide();
				if ( 'off' == $( 'input[name="gglcptch_network_apply"]:checked' ).val() ) {
					$( '.gglcptch_settings_form' ).hide();
				} else
					$( '.gglcptch_settings_form' ).show();
			} else {
				$( '#gglcptch_network_notice, .gglcptch_network_settings .bws_network_apply_all, .gglcptch_settings_form' ).show();
			}
		}
		if ( $( 'input[name="gglcptch_network_apply"]' ).length ) {
			gglcptch_network_apply();
			$( 'input[name="gglcptch_network_apply"]' ).change( function() { gglcptch_network_apply() });
		}

		$( 'input[name="gglcptch_recaptcha_version"]' ).change( function() {
			var versions =  $( 'input[name="gglcptch_recaptcha_version"]' );
			versions.each( function() {
				if ( $( this ).is( ':checked' ) ) {
					$( '.gglcptch_theme_' + $( this ).val() ).show();
				} else {
					$( '.gglcptch_theme_' + $( this ).val() ).hide();
				}
			} );
		} ).trigger( 'change' );

		$( 'input[name="gglcptch_private_key"], input[name="gglcptch_public_key"]' ).change( function() {
			$( '.gglcptch_verified, #gglcptch-test-keys, #gglcptch-test-block' ).hide();
		} );

		$( '.gglcptch-settings-accordion' ).accordion(
			{
				collapsible: true,
				heightStyle: "content"
			}
		);

		/* Prevent jQuery accordion collapsing on link click */
		$( ".gglcptch-settings-accordion a" ).on( "click", function( event ) {
			event.stopPropagation();
		} );

		/**
		 * Handle the "Whitelist" tab on the plugins option page
		 */
		$( 'button[name="gglcptch_show_whitelist_form"]' ).click( function() {
			$( this ).closest( 'form' ).hide();
			$( '.gglcptch_whitelist_form' ).show();
			return false;
		} );

		/*  add my ip to the whitelist */
		$( 'input[name="gglcptch_add_to_whitelist_my_ip"]' ).change( function() {
			if ( $( this ).is( ':checked' ) ) {
				var reason = $( this ).parent().text();
				var my_ip = $( 'input[name="gglcptch_add_to_whitelist_my_ip_value"]' ).val();
				$( 'textarea[name="gglcptch_add_to_whitelist"]' ).val( my_ip ).attr( 'readonly', 'readonly' );
				$( 'textarea[name="gglcptch_add_to_whitelist_reason"]' ).val( $.trim( reason ) );
			} else {
				$( 'textarea[name="gglcptch_add_to_whitelist_reason"]' ).val( '' );
				$( 'textarea[name="gglcptch_add_to_whitelist"]' ).val( '' ).removeAttr( 'readonly' );
			}
		} );

		/* Putting initial value of each textarea into data 'default-value' attr */
		$( '.gglcptch-add-reason-textarea' ).each( function( e ) {
			$( this ).data( 'default-value', $( this ).val() );
		} );

		$( '.gglcptch-add-reason-textarea' ).css( {"overflow": "hidden"} );
		/* Hiding display and edit link and showing textarea field with buttons for edit add_reason for whitelist/blacklist by click on edit link */
		$( '.gglcptch_edit_reason_link' ).on( "click", function( event ) {
			event.preventDefault();
			var parent = $( this ).closest( 'td' );
			parent.find( '.gglcptch-add-reason, .gglcptch_edit_reason_link' ).hide();
			parent.find( '.gglcptch-add-reason-button' ).show();
			parent.find( '.gglcptch-add-reason-textarea' ).show().trigger( 'focus' );
		} );

		/* preparing arguments and calling gglcptch_update_reason() function */
		$( '.gglcptch-add-reason-button[name=gglcptch_reason_submit]' ).on( "click", function( event ) {
			event.preventDefault();
			var parent = $( this ).parent(),
				ip = $( this ).closest( 'tr' ).find( '.check-column input' ).val(),
				reason = parent.find( '.gglcptch-add-reason-textarea' ).val();
			gglcptch_update_reason( ip, reason );
			parent.find( '.gglcptch-add-reason-button, .gglcptch-add-reason-textarea' ).hide();
			parent.find( '.gglcptch-add-reason, .gglcptch_edit_reason_link' ).show();
		} );

		/* restoring initial value of textarea from data 'default-value' by click on cancel button */
		$( '.gglcptch-add-reason-button[name=gglcptch_reason_cancel]' ).on( "click", function( event ) {
			event.preventDefault();
			var parent = $( this ).parent(),
				default_data = $( this ).parent().find( '.gglcptch-add-reason-textarea' ).data( 'default-value' );
			parent.find( '.gglcptch-add-reason-textarea' ).val( default_data );
			parent.find( '.gglcptch-add-reason-button, .gglcptch-add-reason-textarea' ).hide();
			parent.find( '.gglcptch-add-reason, .gglcptch_edit_reason_link' ).show();
		} );

		/* function to resize textarea according to the 'add_reason' content */
		$( '.gglcptch-autoexpand' ).on( "focus input", function() {
			var el = this;
			el.style.cssText = 'height:auto; padding:0; overflow:hidden';
			el.style.cssText = 'height:' + el.scrollHeight + 'px; overflow:hidden';
		} );
	} );

	$( document ).on( 'click', '#gglcptch-test-keys a', function( e ) {
		e.preventDefault();

		if ( ! $( '#gglcptch-test-block' ).length )
			$( '#gglcptch-test-keys' ).after( '<div id="gglcptch-test-block"></div>' );

		$( '.gglcptch-test-results' ).remove();
		$( '#gglcptch-test-block' ).load( $( this ).prop( 'href' ), function() {
			$( '.gglcptch_v1, .gglcptch_v2, .gglcptch_invisible' ).each( function() {
				var container = $( this ).find( '.gglcptch_recaptcha' ).attr( 'id' );
				if ( $( this ).is( ':visible' ) ) {
					gglcptch.display( container );
					if ( $( this ).hasClass( 'gglcptch_invisible' ) ) {
						var gglcptch_index = $( this ).find( '.gglcptch_recaptcha' ).data( 'gglcptch_index' );
						grecaptcha.execute( gglcptch_index );
					}
				}
			} );
		} );

		e.stopPropagation();
		$( '#gglcptch-test-keys' ).hide();
		return false;
	} );

	$( document ).on( 'click', '#gglcptch_test_keys_verification', function( e ) {
		e.preventDefault();
		$.ajax( {
			async   : false,
			cache   : false,
			type    : 'POST',
			url     : ajaxurl,
			headers : {
				'Content-Type' : 'application/x-www-form-urlencoded'
			},
			data    : {
				action: 'gglcptch_test_keys_verification',
				recaptcha_challenge_field : $( '#recaptcha_challenge_field' ).val(),
				recaptcha_response_field  : $( '#recaptcha_response_field' ).val(),
				'g-recaptcha-response'  : $( '.g-recaptcha-response' ).val(),
				'is_network'  : $( 'input[name="gglcptch_is_network"]' ).val(),
				_wpnonce : $( 'input[name="gglcptch_test_keys_verification-nonce"]' ).val()
			},
			success: function( data ) {
				$( '#gglcptch-test-block' ).after( data );
				$( '#gglcptch-test-block' ).html( '' );
				if ( $( '.gglcptch-test-results' ).hasClass( 'updated' ) ) {
					$( '.gglcptch_verified' ).show();
				} else {
					$( '.gglcptch_verified' ).hide();
					if (
						'v2' == $( 'input[name="gglcptch_recaptcha_version"]:checked' ).val() ||
						'invisible' == $( 'input[name="gglcptch_recaptcha_version"]:checked' ).val()
					) {
						$( '#gglcptch-test-keys' ).show();
					}
				}
			}
		} );

		e.stopPropagation();
		return false;
	} );
} )( jQuery );

/**
 * Update add reason for whitelist/blacklist
 * @param		string		ip				reason of which ip is edited
 * @param		string		table			what table need to be updated (blacklist, whitelist)
 * @param		string		reason			reason text
 * @return		void
 */
function gglcptch_update_reason( ip, reason ) {
	( function( $ ) {
		$.ajax( {
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'gglcptch_update_reason',
				gglcptch_edit_ip:	ip,
				gglcptch_reason:	reason,
				gglcptch_nonce:	gglcptchScriptVars.gglcptch_ajax_nonce
			},
			success: function( result ) {
				var parent_row	= $( '.check-column input[value="' + ip + '"]' ).closest( 'tr' );
				var reason_display = parent_row.find( '.gglcptch-add-reason' );
				var reason_textarea = parent_row.find( '.gglcptch-add-reason-textarea' );
				var old_color = reason_display.css( 'color' );
				try {
					result		= $.parseJSON( result );
					if ( result['success'] != '' ){
						reason_textarea.val( result['reason'] );
						reason_textarea.data( 'default-value', result['reason'] );
						reason_display.html( result['reason-html'] );
						reason_display
							.animate(
								{ color: "#46b450" },
								250
							)
							.animate(
								{ color: old_color },
								250
							);
					} else {
						if ( result['no_changes'] != '' ) {
						} else {
							var str = reason_display.html();
							reason_textarea.val( str.replace( /<br>/g, "" ) );
							reason_display
								.animate(
									{ color: "#dc3232" },
									250
								)
								.animate(
									{ color: old_color },
									250
								);
						}
					}
				} catch( e ) {
					var str = reason_display.html();
					reason_textarea.val( str.replace( /<br>/g, "" ) );
					reason_display
						.animate(
							{ color: "#dc3232" },
							250
						)
						.animate(
							{ color: old_color },
							250
						);
				}
			},
			error : function ( xhr, ajaxOptions, thrownError ) {
				alert( xhr.status );
				alert( thrownError );
			}
		} );
		return false;
	} )( jQuery );
}