( function( $, gglcptch ) {
	gglcptch = gglcptch || {};

	gglcptch.prepare = function() {
		/*
		 * display reCaptcha for plugin`s block
		 */
		$( '.gglcptch_v2, .gglcptch_invisible' ).each( function() {

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
					$( this ).prepend( '<div class="gglcptch_dublicate_error error" style="color: red;">' + gglcptch.options.error + '</div><br />\n' );
				}
			} );
		}
	};

	gglcptch.display = function( container, v1_add_to_last_element, params ) {
		if ( typeof( container ) == 'undefined' || container == '' || typeof( gglcptch.options ) == 'undefined' ) {
			return;
		}

		// add attribute disable to the submit
		if ( 'v2' === gglcptch.options.version && gglcptch.options.disable ) {
			$( '#' + container ).closest( 'form' ).find( 'input:submit, button' ).prop( 'disabled', true );
		}

		function storeEvents( el ) {
			var target = el,
				events = $._data( el.get( 0 ), 'events' );
			/* restoring events */
			if ( typeof events != 'undefined' ) {
				var storedEvents = {};
				$.extend( true, storedEvents, events );
				delete events.submit;
				target.off(events);
				target.data( 'storedEvents', storedEvents );
			}
			/* storing and removing onclick action */
			if ( 'undefined' != typeof target.attr( 'onclick' ) ) {
				target.attr( 'gglcptch-onclick', target.attr( 'onclick') );
				target.removeAttr( 'onclick' );
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
				target.attr( 'onclick', target.attr( 'gglcptch-onclick' ) );
				target.removeAttr( 'gglcptch-onclick' );
			}
		}

		function storeOnSubmit( form, gglcptch_index ) {
			if ( typeof Backbone != "undefined" ) {
				Backbone.Radio.channel( 'bwsrecaptcha' ).request( 'update:execute', gglcptch_index );
			}

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

		if ( 'v2' == gglcptch_version ) {
			if ( 'normal' == gglcptch.options.size ) {
				if ( $( '#' + container ).parent().width() <= 300 && $( '#' + container ).parent().width() != 0 ||
					/* This check is used for WP Foro Add Topic form */
					$( '#' + container ).parent().is(':hidden') && $( window ).width() < 400 ) {
					var size = 'compact';
				} else {
					var size = 'normal';
				}
			} else {
				var size = gglcptch.options.size;
			}
			var parameters = params ? params : { 'sitekey' : gglcptch.options.sitekey, 'theme' : gglcptch.options.theme, 'size' : size },
				block = $( '#' + container ),
				form = block.closest( 'form' );

				/* Callback function works only in frontend */
				if ( ! $( 'body' ).hasClass( 'wp-admin' ) ) {
					parameters['callback'] = function() {
						form.find( 'button, input:submit' ).prop( 'disabled', false );
					};
				}

				gglcptch_index = grecaptcha.render( container, parameters );
			$( '#' + container ).data( 'gglcptch_index', gglcptch_index );
		}

		if ( 'invisible' == gglcptch_version ) {
			var block = $( '#' + container ),
				form = block.closest( 'form' ),
				parameters = params ? params : { 'sitekey' : gglcptch.options.sitekey, 'size' : gglcptch.options.size, 'tabindex' : 9999 },
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
						/* Condition for Ninja forms */
						if ( typeof Marionette == "undefined" ) {
							grecaptcha.reset( gglcptch_index );
						}
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

		/**
		 * WooCommerce compatibility:
		 * Generate the reCAPTCHA into previously hidden tabs content.
		 */
		$( '.woocommerce' ).on( 'click', '.woocommerce-tabs', gglcptch_prepare );

		/**
		 * WooCommerce compatibility:
		 * Reload the reCAPTCHA in case if the checkout form was not submitted.
		 */
		$( document.body ).on( 'checkout_error', function() {
			grecaptcha.reset();
		});

		$( '#recaptcha_widget_div' ).on( 'input paste change', '#recaptcha_response_field', cleanError );

		/* Reload recaptcha on CF7 */
		$( document ).on( "DOMSubtreeModified", 'form.wpcf7-form .wpcf7-response-output', function( gglcptch ) {
			if ( $( this ).text() != '' ) {
				var gglcptch = $( this ).parent( 'form' ).find( '.gglcptch' );
				if ( typeof Recaptcha != "undefined" || typeof grecaptcha != "undefined" ) {
					if ( gglcptch.length > 0 ) {
						if ( gglcptch.hasClass( 'gglcptch_v1' ) ) {
							Recaptcha.reload();
						} else if ( gglcptch.hasClass( 'gglcptch_v3' ) ) {
								grecaptcha.ready(function() {
									grecaptcha.execute( get_site_key(), {action: 'BWS_reCaptcha_pro'}).then(function(token) {
										document.querySelectorAll( "#g-recaptcha-response" ).forEach( elem => ( elem.value = token ) );
									});
								});
						} else {
							grecaptcha.reset( gglcptch.find( '.gglcptch_recaptcha' ).data( 'gglcptch_index' ) );
						}
					}
				}
			}
		} );
	} );

	$(window).on('load', function () {
		$('#gglcptch-nf').on( 'focusin', function() {
 			var resp = grecaptcha.getResponse();
			var fieldID = jQuery( this ).data( 'id' );
			Backbone.Radio.channel( 'bwsrecaptcha' ).request( 'update:response', resp, fieldID );
		});
	});

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

	function get_site_key() {
		return gglcptch.options.sitekey;
	}

	function gglcptch_validate_divi_form(form, messageClass ) {
		form.submit( function (e) {

			var g_recaptcha_response;

			if ( 'v2' == gglcptch.options.version ) {
				g_recaptcha_response = $( this ).find('.g-recaptcha-response');
			} else {
				g_recaptcha_response = $( this ).find('#g-recaptcha-response');
				if ( g_recaptcha_response.val() == "" ) {
					g_recaptcha_response = $( this ).find('#g-recaptcha-response');
				}
			}

			if ( 'v3' == gglcptch.options.version ) {
				grecaptcha.ready(function () {
					grecaptcha.execute(gglcptch.options.sitekey, {action: 'BWS_reCaptcha_pro'}).then(function ( token ) {
						$( this ).find('#g-recaptcha-response').val( token );
					});
				});
			}

			var currentForm = $(this);

			$.ajax({
				async: false,
				cache: false,
				type: 'POST',
				url: gglcptch.ajax_url,
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded'
				},
				data: {
					action: 'gglcptch_captcha_check_for_divi',
					recaptcha_challenge_field: currentForm.find('#recaptcha_challenge_field').val(),
					recaptcha_response_field: currentForm.find('#recaptcha_response_field').val(),
					'g-recaptcha-response': g_recaptcha_response.val(),
				},
				success: function ( data ) {

					if ( data == "success" ) {
						if ( 'v2' == gglcptch.options.version ) {
							grecaptcha.reset();
						}
						/* event.preventDefault for divi contact form */
						if ( true == currentForm.data( "submitted" ) ) {
							currentForm.data( "submitted", false );
						}

					} else {
						currentForm.data( "submitted", true );
						/* list of errors */
						var contact_form_captcha_errors = data;

						/* set errors to divi contact form */
						currentForm.closest('.et_pb_contact_form_container').find( messageClass ).html( contact_form_captcha_errors );
					}
				}
			});
		});
	}

	if ( false == gglcptch.options.whitelisted && 1 == gglcptch.options.divi_contact_form ) {
		var form_divi_builder = $('.et_pb_contact_form');

		gglcptch_validate_divi_form( form_divi_builder, '.et-pb-contact-message' );
	}

	if ( false == gglcptch.options.whitelisted && 1 == gglcptch.options.divi_login ) {
		var form_login_divi_builder = $('.et_pb_newsletter_form').find('form');

		gglcptch_validate_divi_form( form_login_divi_builder, '.gglcptch_et_pb_login_message' );
	}


})( jQuery, gglcptch );

/*For Ninja Forms*/
if ( typeof Marionette != "undefined" ) {
    var bwsrecaptchaFieldController = Marionette.Object.extend({
        fieldType: 'bwsrecaptcha',
        initialize: function() {
            Backbone.Radio.channel( this.fieldType ).reply( 'get:submitData', this.getSubmitData );
			this.listenTo( nfRadio.channel( this.fieldType ), 'init:model',   this.initRecaptcha  );
			this.listenTo( nfRadio.channel( 'form' ), 'render:view', this.renderCaptcha );
        },
        getSubmitData: function( fieldData ) {
            fieldData.value = document.getElementById('g-recaptcha-response').value;
            return fieldData;
        },
		initRecaptcha: function ( model ) {
			if ( model.attributes['recaptcha_version'] == 'v3'||  model.attributes['recaptcha_version'] == 'invisible' ){
				 model.set( 'label_pos', 'hidden' );
			}
			nfRadio.channel( this.fieldType  ).reply( 'update:response', this.updateResponse, this, model.id );
			nfRadio.channel( this.fieldType  ).reply( 'update:execute', this.updateExecute, 0 );
		},
		updateResponse: function( response, fieldID ) {
			var model = nfRadio.channel( 'fields' ).request( 'get:field', fieldID );
			model.set( 'value', response );
			nfRadio.channel( 'fields' ).request( 'remove:error', model.id , 'required-error' );
		},
		renderCaptcha: function() {
			var public_key = jQuery( '#gglcptch-nf' ).data( 'publickey' );
			if (  'v3' == jQuery( '#gglcptch-nf' ).data( 'version' ) ) {
				grecaptcha.ready(function() {
					grecaptcha.execute( public_key, {action: 'BWS_reCaptcha_pro'}).then(function(token) {
						document.getElementById('g-recaptcha-response').value=token;
					});
				});
			}
		},
		updateExecute: function (gglcptch_index) {
			grecaptcha.execute(gglcptch_index);
		}
    });

new bwsrecaptchaFieldController();
}