( function( $ ) {
	$( document ).ready( function() {

		var gglcptch_version = gglcptch.options.version,
			gglcptch_buddypress_comments = function( $form, reload ) {
			var gglcptch_form_recaptcha = $form.find( '.ac-reply-content-gglcptch' ),
			$submit = $form.find( 'input[name="ac_form_submit"]' );
			gglcptch_form_recaptcha.insertBefore( $submit );
			gglcptch_form_recaptcha.removeClass( 'ac-reply-content' );

			var gglcptch_container = $form.find( '.gglcptch_recaptcha' ),
				gglcptch_container_id = gglcptch_container.attr( 'id' );

			if ( gglcptch_container.html() == '' ) {
				gglcptch.display( gglcptch_container_id, true );
			}

			if ( reload == true ) {
				if ( 'v1' == gglcptch_version ) {
					$( '#recaptcha_reload' ).click();
				}
				if ( 'v2' == gglcptch_version || 'invisible' == gglcptch_version ) {
					var index = $form.find( '.gglcptch_recaptcha' ).data( 'gglcptch_index' );
					grecaptcha.reset( index );
				}
			}
		}

		$( 'div.activity' ).off( 'click' );

		/* Activity list event delegation */
		$( 'div.activity' ).on( 'click', function( event ) {
			var target = $( event.target );

			/* Comment / comment reply links */
			if ( target.hasClass( 'acomment-reply' ) || target.parent().hasClass( 'acomment-reply' ) ) {
				if ( target.parent().hasClass( 'acomment-reply' ) ) {
					target = target.parent();
				}

				var id = target.attr( 'id' );
				ids = id.split( '-' );

				var a_id = ids[2]
				var c_id = target.attr( 'href' ).substr( 10, target.attr( 'href' ).length );
				var form = $( '#ac-form-' + a_id );

				form.css( 'display', 'none' );
				form.removeClass( 'root' );
				$( '.ac-form' ).hide();

				/* Hide any error messages */
				form.children( 'div' ).each( function() {
					if ( $( this ).hasClass( 'error' ) ) {
						$( this ).hide();
					}
				});

				var gglcptch_reload = false;

				if ( ids[1] != 'comment' ) {
					$( '#acomment-' + c_id ).append( form );
					gglcptch_reload = true;
				} else {
					$( '#activity-' + a_id + ' .activity-comments' ).append( form );
					gglcptch_reload = false;
				}

				if ( form.parent().hasClass( 'activity-comments' ) ) {
					form.addClass( 'root' );
				}

				form.slideDown( 200 );
				$.scrollTo( form, 500, {
					offset:-100,
					easing:'easeOutQuad'
				} );
				$( '#ac-form-' + ids[2] + ' textarea' ).focus();

				var $form = $( '#ac-form-' + ids[2] );
				gglcptch_buddypress_comments( $form, gglcptch_reload );

				return false;
			}

			/* Activity comment posting */
			if ( target.attr( 'name' ) == 'ac_form_submit' ) {
				var form = target.parents( 'form' );
				var form_parent = form.parent();
				var form_id = form.attr( 'id' ).split( '-' );

				if ( !form_parent.hasClass( 'activity-comments' ) ) {
					var tmp_id = form_parent.attr( 'id' ).split( '-' );
					var comment_id = tmp_id[1];
				} else {
					var comment_id = form_id[2];
				}

				var content = $( '#' + form.attr( 'id' ) + ' textarea' );

				/* Hide any error messages */
				$( '#' + form.attr( 'id' ) + ' div.error' ).hide();
				target.addClass( 'loading' ).prop( 'disabled', true );
				content.addClass( 'loading' ).prop( 'disabled', true );

				/* reCAPTHA validation */
				var gglcptch_data = {
					action: 'gglcptch_captcha_check'
				};

				if ( 'v1' == gglcptch_version ) {
					gglcptch_data['recaptcha_challenge_field'] = form.find( '#recaptcha_challenge_field' ).val();
					gglcptch_data['recaptcha_response_field']  = form.find( '#recaptcha_response_field' ).val();
				}
				if ( 'v2' == gglcptch_version || 'invisible' == gglcptch_version ) {
					gglcptch_data['g-recaptcha-response'] = form.find( '.g-recaptcha-response' ).val();
				}

				$.ajax({
					type: "POST",
					url: ajaxurl,
					headers : {
						'Content-Type' : 'application/x-www-form-urlencoded'
					},
					data: gglcptch_data,
					success:function( msg ) {
						if ( msg != "success" ) {
							var gglcptch_error = '<div id="message" class="error bp-ajax-message"><p>' + gglcptch.vars.error_msg + '</p></div>';
							target.removeClass( 'loading' );
							content.removeClass( 'loading' );
							$( target ).prop( 'disabled', false );
							$( content ).prop( 'disabled', false );
							form.append( $( gglcptch_error ).hide().fadeIn( 200 ) );
							return false;
						} else {
							/* continue buddypress script */
							var ajaxdata = {
								action: 'new_activity_comment',
								'cookie': bp_get_cookies(),
								'_wpnonce_new_activity_comment': $( '#_wpnonce_new_activity_comment' ).val(),
								'comment_id': comment_id,
								'form_id': form_id[2],
								'content': content.val()
							};

							/* Akismet */
							var ak_nonce = $( '#_bp_as_nonce_' + comment_id ).val();
							if ( ak_nonce ) {
								ajaxdata[ '_bp_as_nonce_' + comment_id ] = ak_nonce;
							}

							$.post( ajaxurl, ajaxdata, function( response ) {
								target.removeClass( 'loading' );
								content.removeClass( 'loading' );

								/* Check for errors and append if found. */
								if ( response[0] + response[1] == '-1' ) {
									form.append( $( response.substr( 2, response.length ) ).hide().fadeIn( 200 ) );
								} else {
									var activity_comments = form.parent();
									form.fadeOut( 200, function() {
										if ( 0 == activity_comments.children( 'ul' ).length ) {
											if ( activity_comments.hasClass( 'activity-comments' ) ) {
												activity_comments.prepend( '<ul></ul>' );
											} else {
												activity_comments.append( '<ul></ul>' );
											}
										}

										/* Preceeding whitespace breaks output with jQuery 1.9.0 */
										var the_comment = $.trim( response );

										activity_comments.children( 'ul' ).append( $( the_comment ).hide().fadeIn( 200 ) );
										form.children( 'textarea' ).val( '' );
										activity_comments.parent().addClass( 'has-comments' );
									} );
									$( '#' + form.attr( 'id' ) + ' textarea').val( '' );

									/* Increase the "Reply (X)" button count */
									$( '#activity-' + form_id[2] + ' a.acomment-reply span' ).html( Number( $( '#activity-' + form_id[2] + ' a.acomment-reply span' ).html() ) + 1 );

									/* Increment the 'Show all x comments' string, if present */
									var show_all_a = activity_comments.find( '.show-all' ).find( 'a' );
									if ( show_all_a ) {
										var new_count = $( 'li#activity-' + form_id[2] + ' a.acomment-reply span' ).html();
										show_all_a.html( BP_DTheme.show_x_comments.replace( '%d', new_count ) );
									}
								}

								if ( 'v1' == gglcptch_version ) {
									$( '#recaptcha_reload' ).click();
								}
								if ( 'v2' == gglcptch_version || 'invisible' == gglcptch_version ) {
									var index = form.find( '.gglcptch_recaptcha' ).data( 'gglcptch_index' );
									grecaptcha.reset( index );
								}

								$( target ).prop( 'disabled', false );
								$( content ).prop( 'disabled', false );
								$( '#' + form.attr( 'id' ) + ' input[name="cptchpr_number"]' ).val( '' );
							});
						}
					}
				});
				/* end CAPTHA validation */
				return false;
			}

			/* Deleting an activity comment */
			if ( target.hasClass( 'acomment-delete' ) ) {
				var link_href = target.attr( 'href' );
				var comment_li = target.parent().parent();
				var form = comment_li.parents( 'div.activity-comments' ).children( 'form' );

				var nonce = link_href.split( '_wpnonce=' );
				nonce = nonce[1];

				var comment_id = link_href.split( 'cid=' );
				comment_id = comment_id[1].split( '&' );
				comment_id = comment_id[0];

				target.addClass( 'loading' );

				/* Remove any error messages */
				$( '.activity-comments ul .error' ).remove();

				/* Reset the form position */
				comment_li.parents( '.activity-comments' ).append( form );

				$.post( ajaxurl, {
					action: 'delete_activity_comment',
					'cookie': bp_get_cookies(),
					'_wpnonce': nonce,
					'id': comment_id
				},
				function( response ) {
					/* Check for errors and append if found. */
					if ( response[0] + response[1] == '-1' ) {
						comment_li.prepend( $( response.substr( 2, response.length ) ).hide().fadeIn( 200 ) );
					} else {
						var children = $( '#' + comment_li.attr( 'id' ) + ' ul' ).children( 'li' );
						var child_count = 0;
						$( children ).each( function() {
							if ( ! $( this ).is( ':hidden' ) )
								child_count++;
						});
						comment_li.fadeOut( 200, function() {
							comment_li.remove();
						});

						/* Decrease the "Reply (X)" button count */
						var count_span = $( '#' + comment_li.parents( '#activity-stream > li' ).attr( 'id' ) + ' a.acomment-reply span' );
						var new_count = count_span.html() - ( 1 + child_count );
						count_span.html( new_count );

						/* Change the 'Show all x comments' text */
						var show_all_a = comment_li.siblings( '.show-all' ).find( 'a' );
						if ( show_all_a ) {
							show_all_a.html( BP_DTheme.show_x_comments.replace( '%d', new_count ) );
						}

						/* If that was the last comment for the item, remove the has-comments class to clean up the styling */
						if ( 0 == new_count ) {
							$( comment_li.parents( '#activity-stream > li' ) ).removeClass( 'has-comments' );
						}
					}
				});

				return false;
			}

			/* Spam an activity stream comment */
			if ( target.hasClass( 'spam-activity-comment' ) ) {
				var link_href  = target.attr( 'href' );
				var comment_li = target.parent().parent();

				target.addClass( 'loading' );

				/* Remove any error messages */
				$( '.activity-comments ul div.error' ).remove();

				/* Reset the form position */
				comment_li.parents( '.activity-comments' ).append( comment_li.parents( '.activity-comments' ).children( 'form' ) );

				$.post( ajaxurl, {
					action: 'bp_spam_activity_comment',
					'cookie': encodeURIComponent( document.cookie ),
					'_wpnonce': link_href.split( '_wpnonce=' )[1],
					'id': link_href.split( 'cid=' )[1].split( '&' )[0]
				},

				function ( response ) {
					/* Check for errors and append if found */
					if ( response[0] + response[1] == '-1' ) {
						comment_li.prepend( $( response.substr( 2, response.length ) ).hide().fadeIn( 200 ) );
					} else {
						var children = $( '#' + comment_li.attr( 'id' ) + ' ul' ).children( 'li' );
						var child_count = 0;
						$( children ).each( function() {
							if ( !$( this ).is( ':hidden' ) ) {
								child_count++;
							}
						});
						comment_li.fadeOut( 200 );

						/* Decrease the "Reply (X)" button count */
						var parent_li = comment_li.parents( '#activity-stream > li' );
						$( '#' + parent_li.attr( 'id' ) + ' a.acomment-reply span' ).html( $( '#' + parent_li.attr( 'id' ) + ' a.acomment-reply span' ).html() - ( 1 + child_count ) );
					}
				});

				return false;
			}

			/* Showing hidden comments - pause for half a second */
			if ( target.parent().hasClass( 'show-all' ) ) {
				target.parent().addClass( 'loading' );
				setTimeout( function() {
					target.parent().parent().children( 'li' ).fadeIn( 200, function() {
						target.parent().remove();
					});
				}, 600 );
				return false;
			}

			/* Canceling an activity comment	 */
			if ( target.hasClass( 'ac-reply-cancel' ) ) {
				$( target ).closest( '.ac-form' ).slideUp( 200 );
				return false;
			};
		});

		/* Stream event delegation */
		$( 'div.activity' ).on( 'click', function( event ) {
			var target = jq( event.target );

			/* Favoriting activity stream items */
			if ( target.hasClass( 'fav' ) || target.hasClass( 'unfav' ) ) {
				var type = target.hasClass( 'fav' ) ? 'fav' : 'unfav';
				var parent = target.closest( '.activity-item' );
				var parent_id = parent.attr( 'id' ).substr( 9, parent.attr( 'id' ).length );

				target.addClass( 'loading' );

				jq.post( ajaxurl, {
					action: 'activity_mark_' + type,
					'cookie': bp_get_cookies(),
					'id': parent_id
				},
				function( response ) {
					target.removeClass( 'loading' );

					target.fadeOut( 100, function() {
						jq( this ).html( response );
						jq( this ).attr( 'title', 'fav' == type ? BP_DTheme.remove_fav : BP_DTheme.mark_as_fav );
						jq( this ).fadeIn( 100 );
					});

					if ( 'fav' == type ) {
						if ( !jq( '.item-list-tabs li#activity-favorites' ).length )
							jq( '.item-list-tabs ul li#activity-mentions' ).before( '<li id="activity-favorites"><a href="#">' + BP_DTheme.my_favs + ' <span>0</span></a></li>');

						target.removeClass( 'fav' );
						target.addClass( 'unfav' );

						jq( '.item-list-tabs ul li#activity-favorites span' ).html( Number( jq( '.item-list-tabs ul li#activity-favorites span' ).html() ) + 1 );
					} else {
						target.removeClass( 'unfav' );
						target.addClass( 'fav' );

						jq( '.item-list-tabs ul li#activity-favorites span' ).html( Number( jq( '.item-list-tabs ul li#activity-favorites span' ).html() ) - 1 );

						if ( !Number( jq( '.item-list-tabs ul li#activity-favorites span' ).html() ) ) {
							if ( jq( '.item-list-tabs ul li#activity-favorites' ).hasClass( 'selected' ) )
								bp_activity_request( null, null );

							jq( '.item-list-tabs ul li#activity-favorites' ).remove();
						}
					}

					if ( 'activity-favorites' == jq( '.item-list-tabs li.selected').attr( 'id' ) )
						target.parent().parent().parent().slideUp( 100 );
				});

				return false;
			}

			/* Delete activity stream items */
			if ( target.hasClass( 'delete-activity' ) ) {
				var li        = target.parents( 'div.activity ul li' );
				var id        = li.attr( 'id' ).substr( 9, li.attr( 'id' ).length );
				var link_href = target.attr( 'href' );
				var nonce     = link_href.split( '_wpnonce=' );

				nonce = nonce[1];

				target.addClass( 'loading' );

				jq.post( ajaxurl, {
					action: 'delete_activity',
					'cookie': bp_get_cookies(),
					'id': id,
					'_wpnonce': nonce
				},
				function( response ) {

					if ( response[0] + response[1] == '-1' ) {
						li.prepend( response.substr( 2, response.length ) );
						li.children( 'div#message' ).hide().fadeIn( 300 );
					} else {
						li.slideUp( 300 );
					}
				});

				return false;
			}

			/* Spam activity stream items */
			if ( target.hasClass( 'spam-activity' ) ) {
				var li = target.parents( 'div.activity ul li' );
				target.addClass( 'loading' );

				jq.post( ajaxurl, {
					action: 'bp_spam_activity',
					'cookie': encodeURIComponent( document.cookie ),
					'id': li.attr( 'id' ).substr( 9, li.attr( 'id' ).length ),
					'_wpnonce': target.attr( 'href' ).split( '_wpnonce=' )[1]
				},

				function( response ) {
					if ( response[0] + response[1] === '-1' ) {
						li.prepend( response.substr( 2, response.length ) );
						li.children( 'div#message' ).hide().fadeIn( 300 );
					} else {
						li.slideUp( 300 );
					}
				});

				return false;
			}

			/* Load more updates at the end of the page */
			if ( target.parent().hasClass( 'load-more' ) ) {
				jq( "#content li.load-more" ).addClass( 'loading' );

				if ( null == jq.cookie( 'bp-activity-oldestpage' ) )
					jq.cookie( 'bp-activity-oldestpage', 1, {
						path: '/'
					} );

				var oldest_page = ( jq.cookie( 'bp-activity-oldestpage' ) * 1 ) + 1;

				var just_posted = [];

				jq( '.activity-list li.just-posted' ).each( function(){
					just_posted.push( jq( this ).attr( 'id' ).replace( 'activity-','' ) );
				});

				jq.post( ajaxurl, {
					action: 'activity_get_older_updates',
					'cookie': bp_get_cookies(),
					'page': oldest_page,
					'exclude_just_posted': just_posted.join( ',' )
				},
				function( response ) {
					jq( "#content li.load-more" ).removeClass( 'loading' );
					jq.cookie( 'bp-activity-oldestpage', oldest_page, {
						path: '/'
					} );
					jq( "#content ul.activity-list" ).append( response.contents );

					target.parent().hide();
				}, 'json' );

				return false;
			}
		});
	});
})(jQuery);