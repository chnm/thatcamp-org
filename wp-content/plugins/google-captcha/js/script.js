( function( $, gglcptch ) {
	gglcptch = gglcptch || {};

	gglcptch.prepare = function() {
		/*
		 * display reCaptcha for plugin`s block
		 */
		$( '.gglcptch_v1, .gglcptch_v2, .gglcptch_invisible' ).each( function() {
			var container = $( this ).find( '.gglcptch_recaptcha' );
			if (
				container.is( ':empty' ) &&
				( gglcptch.vars.visibility || $( this ).is( ':visible' ) === $( this ).is( ':not(:hidden)' ) )
			) {
				var containerId = container.attr( 'id' );
				gglcptch.display( containerId );
			}
		} );

		/*
		 * display reCaptcha for others blocks
		 * this part is neccessary because
		 * we have disabled the connection to Google reCaptcha API from other plugins
		 * via plugin`s php-functionality
		 */
		if ( 'v2' == gglcptch.options.version || 'invisible' == gglcptch.options.version ) {
			$( '.g-recaptcha' ).each( function() {

				/* reCAPTCHA will be generated into the empty block only */
				if ( $( this ).html() === '' && $( this ).text() === '' ) {

					/* get element`s ID */
					var container = $( this ).attr( 'id' );

					if ( typeof container == 'undefined' ) {
						container = get_id();
						$( this ).attr( 'id', container );
					}

					/* get reCapatcha parameters */
					var sitekey  = $( this ).attr( 'data-sitekey' ),
						theme    = $( this ).attr( 'data-theme' ),
						lang     = $( this ).attr( 'data-lang' ),
						size     = $( this ).attr( 'data-size' ),
						type     = $( this ).attr( 'data-type' ),
						tabindex = $( this ).attr( 'data-tabindex' ),
						callback = $( this ).attr( 'data-callback' ),
						ex_call  = $( this ).attr( 'data-expired-callback' ),
						stoken   = $( this ).attr( 'data-stoken' ),
						params   = [];

					params['sitekey'] = sitekey ? sitekey : gglcptch.options.sitekey;
					if ( !! theme ) {
						params['theme'] = theme;
					}
					if ( !! lang ) {
						params['lang'] = lang;
					}
					if ( !! size ) {
						params['size'] = size;
					}
					if ( !! type ) {
						params['type'] = type;
					}
					if ( !! tabindex ) {
						params['tabindex'] = tabindex;
					}
					if ( !! callback ) {
						params['callback'] = callback;
					}
					if ( !! ex_call ) {
						params['expired-callback'] = ex_call;
					}
					if ( !! stoken ) {
						params['stoken'] = stoken;
					}

					gglcptch.display( container, false, params );
				}
			} );

			/*
			 * count the number of reCAPTCHA blocks in the form
			 */
			$( 'form' ).each( function() {
				if ( $( this ).contents().find( 'iframe[title="recaptcha widget"]' ).length > 1 && ! $( this ).children( '.gglcptch_dublicate_error' ).length ) {
					$( this ).prepend( '<div class="gglcptch_dublicate_error error" style="color: red;">'+ gglcptch.options.error + '</div><br />\n' );
				}
			} );
		}
	};

	gglcptch.display = function( container, v1_add_to_last_element, params ) {
		if ( typeof( container ) == 'undefined' || container == '' || typeof( gglcptch.options ) == 'undefined' ) {
			return;
		}

		function storeEvents( el ) {
			var target = el,
				events = $._data( el.get(0), 'events' );
			/* restoring events */
			if ( typeof events != 'undefined' ) {
				var storedEvents = {};
				$.extend( true, storedEvents, events );
				target.off();
				target.data( 'storedEvents', storedEvents );
			}
			/* storing and removing onclick action */
			if ( 'undefined' != typeof target.attr( 'onclick') ) {
				target.attr( 'gglcptch-onclick', target.attr( 'onclick') );
				target.removeAttr('onclick');
			}
		}

		function restoreEvents( el ) {
			var target = el,
				events = target.data( 'storedEvents' );
			/* restoring events */
			if ( typeof events != 'undefined' ) {
				for ( var event in events ) {
					for ( var i = 0; i < events[event].length; i++ ) {
						target.on( event, events[event][i] );
					}
				}
			}
			/* reset stored events */
			target.removeData( 'storedEvents' );
			/* restoring onclick action */
			if ( 'undefined' != typeof target.attr( 'gglcptch-onclick' ) ) {
				target.attr('onclick', target.attr( 'gglcptch-onclick') );
				target.removeAttr('gglcptch-onclick');
			}
		}

		function storeOnSubmit( form, gglcptch_index ) {
			form.on( 'submit', function( e ) {
				if ( '' == form.find( '.g-recaptcha-response' ).val() ) {
					e.preventDefault();
					e.stopImmediatePropagation();
					targetObject = $( e.target || e.srcElement || e.targetObject );
					targetEvent = e.type;
					grecaptcha.execute( gglcptch_index );
				}
			} ).find( 'input:submit, button' ).on( 'click', function( e ) {
				if ( '' == form.find( '.g-recaptcha-response' ).val() ) {
					e.preventDefault();
					e.stopImmediatePropagation();
					targetObject = $( e.target || e.srcElement || e.targetObject );
					targetEvent = e.type;
					grecaptcha.execute( gglcptch_index );
				}
			} );
		}

		var gglcptch_version = gglcptch.options.version;
		v1_add_to_last_element = v1_add_to_last_element || false;

		if ( 'v1' == gglcptch_version ) {
			if ( Recaptcha.widget == null || v1_add_to_last_element == true ) {
				Recaptcha.create( gglcptch.options.sitekey, container, { 'theme' : gglcptch.options.theme } );
			}
		}

		if ( 'v2' == gglcptch_version ) {
				if ( $( '#' + container ).parent().width() <= 300 ) {
					var size = 'compact';
				} else {
					var size = 'normal';
				}
			var parameters = params ? params : { 'sitekey' : gglcptch.options.sitekey, 'theme' : gglcptch.options.theme, 'size' : size },
				gglcptch_index = grecaptcha.render( container, parameters );
			$( '#' + container ).data( 'gglcptch_index', gglcptch_index );
		}

		if ( 'invisible' == gglcptch_version ) {
			var block = $( '#' + container ),
				form = block.closest( 'form' ),
				parameters = params ? params : { 'sitekey' : gglcptch.options.sitekey, 'size' : 'invisible', 'tabindex' : 9999 },
				targetObject = false,
				targetEvent = false;

			if ( form.length ) {
				storeEvents( form );
				form.find( 'button, input:submit' ).each( function() {
					storeEvents( $( this ) );
				} );

				/* Callback function works only in frontend */
				if ( ! $( 'body' ).hasClass( 'wp-admin' ) ) {
					parameters['callback'] = function( token ) {
						form.off();
						restoreEvents( form );
						form.find( 'button, input:submit' ).off().each( function() {
							restoreEvents( $( this ) );
						} );
						if ( targetObject && targetEvent ) {
							targetObject.trigger( targetEvent );
						}
						form.find( 'button, input:submit' ).each( function() {
							storeEvents( $( this ) );
						} );
						storeEvents( form );
						storeOnSubmit( form, gglcptch_index );
						grecaptcha.reset( gglcptch_index );
					};
				}

				var gglcptch_index = grecaptcha.render( container, parameters );
				block.data( { 'gglcptch_index' : gglcptch_index } );

				if ( ! $( 'body' ).hasClass( 'wp-admin' ) ) {
					storeOnSubmit( form, gglcptch_index );
				}
			}
		}
	};

	$( document ).ready( function() {
		var tryCounter = 0,
			/* launching timer so that the function keeps trying to display the reCAPTCHA again and again until google js api is loaded */
			gglcptch_timer = setInterval( function() {
				if ( typeof Recaptcha != "undefined" || typeof grecaptcha != "undefined" ) {
					try {
						gglcptch.prepare();
					} catch ( e ) {
						console.log( 'Unexpected error occurred: ', e );
					}
					clearInterval( gglcptch_timer );
				}
				tryCounter++;
				/* Stop trying after 10 times */
				if ( tryCounter >= 10 ) {
					clearInterval( gglcptch_timer );
				}
			}, 1000 );

		function gglcptch_prepare() {
			if ( typeof Recaptcha != "undefined" || typeof grecaptcha != "undefined" ) {
				try {
					gglcptch.prepare();
				} catch ( err ) {
					console.log( err );
				}
			}
		}

		$( window ).on( 'load', gglcptch_prepare );

		$( '.woocommerce' ).on( 'click', '.woocommerce-tabs', gglcptch_prepare );

		$( '#recaptcha_widget_div' ).on( 'input paste change', '#recaptcha_response_field', cleanError );
	} );

	function cleanError() {
		$error = $( this ).parents( '#recaptcha_widget_div' ).next( '#gglcptch_error' );
		if ( $error.length ) {
			$error.remove();
		}
	}

	function get_id() {
		var id = 'gglcptch_recaptcha_' + Math.floor( Math.random() * 1000 );
		if ( $( '#' + id ).length ) {
			id = get_id();
		} else {
			return id;
		}
	}

	if ( gglcptch.options.version == 'v2' ) {
		var width = $( window ).width();
		$( window ).on( 'resize', function( ) {
			if( $( window ).width() != width ) {
				width = $( window ).width();
				if ( typeof grecaptcha != "undefined" ) {
					$( '.gglcptch_recaptcha' ).html( '' );
					$('script[src^="https://www.google.com/recaptcha/api.js"], script[src^="https://www.gstatic.com/recaptcha/api2"]').remove();
					var src = "https://www.google.com/recaptcha/api.js";
					$.getScript( {
						url : src,
						success : function() {
							setTimeout( function() {
								try {
									gglcptch.prepare();
								} catch ( e ) {
									console.log( e );
								}
							}, 500 );
						}
					} );
				}
			}
		} );
	}

} )( jQuery, gglcptch );