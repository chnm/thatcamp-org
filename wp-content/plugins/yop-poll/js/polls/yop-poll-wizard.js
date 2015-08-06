/*
 * SmartWizard 3.3.1 plugin
 * jQuery Wizard control Plugin
 * by Dipu
 *
 * Refactored and extended:
 * https://github.com/mstratman/jQuery-Smart-Wizard
 *
 * Original URLs:
 * http://www.techlaboratory.net
 * http://tech-laboratory.blogspot.com
 */

function SmartWizard( target, options ) {
    this.target = target;
    this.options = options;
    this.curStepIdx = options.selected;
    this.steps = jQuery( target ).children( "ul" ).children( "li" ).children( "a" ); // Get all anchors
    this.contentWidth = 0;
    this.msgBox = jQuery( '<div class="msgBox"><div class="content"></div><a href="#" class="close">X</a></div>' );
    this.elmStepContainer = jQuery( '<div></div>' ).addClass( "stepContainer" );
    this.loader = jQuery( '<div>Loading</div>' ).addClass( "loader" );
    this.buttons = {
        next: jQuery( '<a>' + button_yop.next_next + '</a>' ).attr( "href", "#" ).addClass( "buttonNext" ),
        previous: jQuery( '<a>' + button_yop.prev_prev+ '</a>' ).attr( "href", "#" ).addClass( "buttonPrevious" ),
        finish: jQuery( '<a>' + button_yop.savee + '</a>' ).attr( "href", "#" ).addClass( "buttonFinish" )
    };
    jQuery("#yop-poll-edit-add-new-form-submit").hide();
    /*
     * Private functions
     */

    var _init = function( $this ) {
        var elmActionBar = jQuery( '<div></div>' ).addClass( "actionBar" );
        elmActionBar.append( $this.msgBox );
        jQuery( '.close', $this.msgBox ).click( function() {
            $this.msgBox.fadeOut( "normal" );
            return false;
        } );

        var allDivs = $this.target.children( 'div' );
        // CHeck if ul with steps has been added by user, if not add them
        if( $this.target.children( 'ul' ).length == 0 ) {
            var ul = jQuery( "<ul/>" );
            target.prepend( ul )

            // for each div create a li
            allDivs.each( function( i, e ) {
                var title = jQuery( e ).first().children( ".StepTitle" ).text();
                var s = jQuery( e ).attr( "id" )
                // if referenced div has no id, add one.
                if( s == undefined ) {
                    s = "step-" + (i + 1)
                    jQuery( e ).attr( "id", s );
                }
                var span = jQuery( "<span/>" ).addClass( "stepDesc" ).text( title );
                var li = jQuery( "<li></li>" ).append( jQuery( "<a></a>" ).attr( "href",
                        "#" + s ).append( jQuery( "<label></label>" ).addClass( "stepNumber" ).text( i +
                        1 ) ).append( span ) );
                ul.append( li );
            } );
            // (re)initialise the steps property
            $this.steps = jQuery( target ).children( "ul" ).children( "li" ).children( "a" ); // Get all anchors
        }
        $this.target.children( 'ul' ).addClass( "anchor" );
        allDivs.addClass( "content" );

        // highlight steps with errors
        if( $this.options.errorSteps && $this.options.errorSteps.length > 0 ) {
            jQuery.each( $this.options.errorSteps, function( i, n ) {
                $this.setError( { stepnum: n, iserror: true } );
            } );
        }

        $this.elmStepContainer.append( allDivs );
        elmActionBar.append( $this.loader );
        $this.target.append( $this.elmStepContainer );

        if( $this.options.includeFinishButton ) {
            elmActionBar.append( $this.buttons.finish )
        }

        elmActionBar.append( $this.buttons.next ).append( $this.buttons.previous );
        $this.target.append( elmActionBar );
        this.contentWidth = $this.elmStepContainer.width();

        jQuery( $this.buttons.next ).click( function() {
            $this.goForward();
            return false;
        } );
        jQuery( $this.buttons.previous ).click( function() {
            $this.goBackward();
            return false;
        } );
        jQuery( $this.buttons.finish ).click( function() {
            if( ! jQuery( this ).hasClass( 'buttonDisabled' ) ) {
                if( jQuery.isFunction( $this.options.onFinish ) ) {
                    var context = { fromStep: $this.curStepIdx + 1 };
                    if( ! $this.options.onFinish.call( this, jQuery( $this.steps ), context ) ) {
                        return false;
                    }
                }
                else {
                    var frm = $this.target.parents( 'form' );
                    if( frm && frm.length ) {
                        frm.submit();
                    }
                }
            }
            return false;
        } );

        jQuery( $this.steps ).bind( "click", function( e ) {
            if( $this.steps.index( this ) == $this.curStepIdx ) {
                return false;
            }
            var nextStepIdx = $this.steps.index( this );
            var isDone = $this.steps.eq( nextStepIdx ).attr( "isDone" ) - 0;
            if( isDone == 1 ) {
                _loadContent( $this, nextStepIdx );
            }
            return false;
        } );

        // Enable keyboard navigation
        if( $this.options.keyNavigation ) {
            jQuery( document ).keyup( function( e ) {
                if( e.which == 39 ) { // Right Arrow
                    $this.goForward();
                }
                else if( e.which == 37 ) { // Left Arrow
                    $this.goBackward();
                }
            } );
        }
        //  Prepare the steps
        _prepareSteps( $this );
        // Show the first slected step
        _loadContent( $this, $this.curStepIdx );
    };

    var _prepareSteps = function( $this ) {
        if( ! $this.options.enableAllSteps ) {
            jQuery( $this.steps, $this.target ).removeClass( "selected" ).removeClass( "done" ).addClass( "disabled" );
            jQuery( $this.steps, $this.target ).attr( "isDone", 0 );
        }
        else {
            jQuery( $this.steps, $this.target ).removeClass( "selected" ).removeClass( "disabled" ).addClass( "done" );
            jQuery( $this.steps, $this.target ).attr( "isDone", 1 );
        }

        jQuery( $this.steps, $this.target ).each( function( i ) {
            jQuery( jQuery( this ).attr( "href" ).replace( /^.+#/, '#' ), $this.target ).hide();
            jQuery( this ).attr( "rel", i + 1 );
        } );
    };

    var _step = function( $this, selStep ) {
        return jQuery( jQuery( selStep, $this.target ).attr( "href" ).replace( /^.+#/, '#' ), $this.target );
    };

    var _loadContent = function( $this, stepIdx ) {
        var selStep = $this.steps.eq( stepIdx );
        var ajaxurl = $this.options.contentURL;
        var ajaxurl_data = $this.options.contentURLData;
        var hasContent = selStep.data( 'hasContent' );
        var stepNum = stepIdx + 1;
        if( ajaxurl && ajaxurl.length > 0 ) {
            if( $this.options.contentCache && hasContent ) {
                _showStep( $this, stepIdx );
            }
            else {
                var ajax_args = {
                    url: ajaxurl,
                    type: $this.options.ajaxType,
                    data: ({step_number: stepNum}),
                    dataType: "text",
                    beforeSend: function() {
                        $this.loader.show();
                    },
                    error: function() {
                        $this.loader.hide();
                    },
                    success: function( res ) {
                        $this.loader.hide();
                        if( res && res.length > 0 ) {
                            selStep.data( 'hasContent', true );
                            _step( $this, selStep ).html( res );
                            _showStep( $this, stepIdx );
                        }
                    }
                };
                if( ajaxurl_data ) {
                    ajax_args = jQuery.extend( ajax_args, ajaxurl_data( stepNum ) );
                }
                jQuery.ajax( ajax_args );
            }
        }
        else {
            _showStep( $this, stepIdx );
        }
    };

    var _showStep = function( $this, stepIdx ) {
        var selStep = $this.steps.eq( stepIdx );
        var curStep = $this.steps.eq( $this.curStepIdx );
        if( stepIdx != $this.curStepIdx ) {
            if( jQuery.isFunction( $this.options.onLeaveStep ) ) {
                var context = { fromStep: $this.curStepIdx + 1, toStep: stepIdx + 1 };
                if( ! $this.options.onLeaveStep.call( $this, jQuery( curStep ), context ) ) {
                    return false;
                }
            }
        }
        $this.elmStepContainer.height( _step( $this, selStep ).outerHeight() );
        var prevCurStepIdx = $this.curStepIdx;
        $this.curStepIdx = stepIdx;
        if( $this.options.transitionEffect == 'slide' ) {
            _step( $this, curStep ).slideUp( "fast", function( e ) {
                _step( $this, selStep ).slideDown( "fast" );
                _setupStep( $this, curStep, selStep );
            } );
        }
        else if( $this.options.transitionEffect == 'fade' ) {
            _step( $this, curStep ).fadeOut( "fast", function( e ) {
                _step( $this, selStep ).fadeIn( "fast" );
                _setupStep( $this, curStep, selStep );
            } );
        }
        else if( $this.options.transitionEffect == 'slideleft' ) {
            var nextElmLeft = 0;
            var nextElmLeft1 = null;
            var nextElmLeft = null;
            var curElementLeft = 0;
            if( stepIdx > prevCurStepIdx ) {
                nextElmLeft1 = $this.elmStepContainer.width() + 10;
                nextElmLeft2 = 0;
                curElementLeft = 0 - _step( $this, curStep ).outerWidth();
            }
            else {
                nextElmLeft1 = 0 - _step( $this, selStep ).outerWidth() + 20;
                nextElmLeft2 = 0;
                curElementLeft = 10 + _step( $this, curStep ).outerWidth();
            }
            if( stepIdx == prevCurStepIdx ) {
                nextElmLeft1 = jQuery( jQuery( selStep, $this.target ).attr( "href" ), $this.target ).outerWidth() + 20;
                nextElmLeft2 = 0;
                curElementLeft = 0 -
                    jQuery( jQuery( curStep, $this.target ).attr( "href" ), $this.target ).outerWidth();
            }
            else {
                jQuery( jQuery( curStep, $this.target ).attr( "href" ), $this.target ).animate( {left: curElementLeft},
                    "fast", function( e ) {
                        jQuery( jQuery( curStep, $this.target ).attr( "href" ), $this.target ).hide();
                    } );
            }

            _step( $this, selStep ).css( "left", nextElmLeft1 ).show().animate( {left: nextElmLeft2}, "fast",
                function( e ) {
                    _setupStep( $this, curStep, selStep );
                } );
        }
        else {
            _step( $this, curStep ).hide();
            _step( $this, selStep ).show();
            _setupStep( $this, curStep, selStep );
        }
        return true;
    };

    var _setupStep = function( $this, curStep, selStep ) {
        jQuery( curStep, $this.target ).removeClass( "selected" );
        jQuery( curStep, $this.target ).addClass( "done" );

        jQuery( selStep, $this.target ).removeClass( "disabled" );
        jQuery( selStep, $this.target ).removeClass( "done" );
        jQuery( selStep, $this.target ).addClass( "selected" );

        jQuery( selStep, $this.target ).attr( "isDone", 1 );

        _adjustButton( $this );

        if( jQuery.isFunction( $this.options.onShowStep ) ) {
            var context = { fromStep: parseInt( jQuery( curStep ).attr( 'rel' ) ), toStep: parseInt( jQuery( selStep ).attr( 'rel' ) ) };
            if( ! $this.options.onShowStep.call( this, jQuery( selStep ), context ) ) {
                return false;
            }
        }
        if( $this.options.noForwardJumping ) {
            // +2 == +1 (for index to step num) +1 (for next step)
            for( var i = $this.curStepIdx + 2; i <= $this.steps.length; i ++ ) {
                $this.disableStep( i );
            }
        }
    };

    var _adjustButton = function( $this ) {
        if( ! $this.options.cycleSteps ) {
            if( 0 >= $this.curStepIdx ) {
                jQuery( $this.buttons.previous ).addClass( "buttonDisabled" );
                if( $this.options.hideButtonsOnDisabled ) {
                    jQuery( $this.buttons.previous ).hide();
                }
            }
            else {
                jQuery( $this.buttons.previous ).removeClass( "buttonDisabled" );
                if( $this.options.hideButtonsOnDisabled ) {
                    jQuery( $this.buttons.previous ).show();
                }
            }
            if( ($this.steps.length - 1) <= $this.curStepIdx ) {
                jQuery( $this.buttons.next ).addClass( "buttonDisabled" );
                if( $this.options.hideButtonsOnDisabled ) {
                    jQuery( $this.buttons.next ).hide();
                }
            }
            else {
                jQuery( $this.buttons.next ).removeClass( "buttonDisabled" );
                if( $this.options.hideButtonsOnDisabled ) {
                    jQuery( $this.buttons.next ).show();
                }
            }
        }
        // Finish Button
        $this.enableFinish( $this.options.enableFinishButton );
    };

    /*
     * Public methods
     */

    SmartWizard.prototype.goForward = function() {
        var nextStepIdx = this.curStepIdx + 1;
        if( this.steps.length <= nextStepIdx ) {
            if( ! this.options.cycleSteps ) {
                return false;
            }
            nextStepIdx = 0;
        }
        _loadContent( this, nextStepIdx );
    };

    SmartWizard.prototype.goBackward = function() {
        var nextStepIdx = this.curStepIdx - 1;
        if( 0 > nextStepIdx ) {
            if( ! this.options.cycleSteps ) {
                return false;
            }
            nextStepIdx = this.steps.length - 1;
        }
        _loadContent( this, nextStepIdx );
    };

    SmartWizard.prototype.goToStep = function( stepNum ) {
        var stepIdx = stepNum - 1;
        if( stepIdx >= 0 && stepIdx < this.steps.length ) {
            _loadContent( this, stepIdx );
        }
    };
    SmartWizard.prototype.enableStep = function( stepNum ) {
        var stepIdx = stepNum - 1;
        if( stepIdx == this.curStepIdx || stepIdx < 0 || stepIdx >= this.steps.length ) {
            return false;
        }
        var step = this.steps.eq( stepIdx );
        jQuery( step, this.target ).attr( "isDone", 1 );
        jQuery( step, this.target ).removeClass( "disabled" ).removeClass( "selected" ).addClass( "done" );
    }
    SmartWizard.prototype.disableStep = function( stepNum ) {
        var stepIdx = stepNum - 1;
        if( stepIdx == this.curStepIdx || stepIdx < 0 || stepIdx >= this.steps.length ) {
            return false;
        }
        var step = this.steps.eq( stepIdx );
        jQuery( step, this.target ).attr( "isDone", 0 );
        jQuery( step, this.target ).removeClass( "done" ).removeClass( "selected" ).addClass( "disabled" );
    }
    SmartWizard.prototype.currentStep = function() {
        return this.curStepIdx + 1;
    }

    SmartWizard.prototype.showMessage = function( msg ) {
        jQuery( '.content', this.msgBox ).html( msg );
        this.msgBox.show();
    }

    SmartWizard.prototype.enableFinish = function( enable ) {
        // Controll status of finish button dynamically
        // just call this with status you want
        this.options.enableFinishButton = enable;
        if( this.options.includeFinishButton ) {
            if( ! this.steps.hasClass( 'disabled' ) || this.options.enableFinishButton ) {
                jQuery( this.buttons.finish ).removeClass( "buttonDisabled" );
                if( this.options.hideButtonsOnDisabled ) {
                    jQuery( this.buttons.finish ).show();
                }
            }
            else {
               // jQuery( this.buttons.finish ).addClass( "buttonDisabled" );
                if( this.options.hideButtonsOnDisabled ) {
                    jQuery( this.buttons.finish ).show();
                }
            }
        }
        return this.options.enableFinishButton;
    }

    SmartWizard.prototype.hideMessage = function() {
        this.msgBox.fadeOut( "normal" );
    }
    SmartWizard.prototype.showError = function( stepnum ) {
        this.setError( stepnum, true );
    }
    SmartWizard.prototype.hideError = function( stepnum ) {
        this.setError( stepnum, false );
    }
    SmartWizard.prototype.setError = function( stepnum, iserror ) {
        if( typeof stepnum == "object" ) {
            iserror = stepnum.iserror;
            stepnum = stepnum.stepnum;
        }

        if( iserror ) {
            jQuery( this.steps.eq( stepnum - 1 ), this.target ).addClass( 'error' )
        }
        else {
            jQuery( this.steps.eq( stepnum - 1 ), this.target ).removeClass( "error" );
        }
    }

    SmartWizard.prototype.fixHeight = function() {
        var height = 0;

        var selStep = this.steps.eq( this.curStepIdx );
        var stepContainer = _step( this, selStep );
        stepContainer.children().each( function() {
            if( jQuery( this ).is( ':visible' ) ) {
                height += jQuery( this ).outerHeight();
            }
        } );

        // These values (5 and 20) are experimentally chosen.
        stepContainer.height( height + 5 );
        this.elmStepContainer.height( height + 20 );
    }

    _init( this );
};


(function( $ ) {

    jQuery.fn.smartWizard = function( method ) {
        var args = arguments;
        var rv = undefined;
        var allObjs = this.each( function() {
            var wiz = jQuery( this ).data( 'smartWizard' );
            if( typeof method == 'object' || ! method || ! wiz ) {
                var options = jQuery.extend( {}, jQuery.fn.smartWizard.defaults, method || {} );
                if( ! wiz ) {
                    wiz = new SmartWizard( jQuery( this ), options );
                    jQuery( this ).data( 'smartWizard', wiz );
                }
            }
            else {
                if( typeof SmartWizard.prototype[method] == "function" ) {
                    rv = SmartWizard.prototype[method].apply( wiz, Array.prototype.slice.call( args, 1 ) );
                    return rv;
                }
                else {
                    jQuery.error( 'Method ' + method + ' does not exist on jQuery.smartWizard' );
                }
            }
        } );
        if( rv === undefined ) {
            return allObjs;
        }
        else {
            return rv;
        }
    };

    // Default Properties and Events
    jQuery.fn.smartWizard.defaults = {
        selected: 0,  // Selected Step, 0 = first step
        keyNavigation: false, // Enable/Disable key navigation(left and right keys are used if enabled)
        enableAllSteps: false,
        transitionEffect: 'fade', // Effect on navigation, none/fade/slide/slideleft
        contentURL: null, // content url, Enables Ajax content loading
        contentCache: true, // cache step contents, if false content is fetched always from ajax url
        cycleSteps: false, // cycle step navigation
        enableFinishButton: false, // make finish button enabled always
        hideButtonsOnDisabled: false, // when the previous/next/finish buttons are disabled, hide them instead?
        errorSteps: [],    // Array Steps with errors
        labelNext: 'Next',
        labelPrevious: 'Previous',
        labelFinish: 'Finish',
        noForwardJumping: false,
        ajaxType: "POST",
        onLeaveStep: null, // triggers when leaving a step
        onShowStep: null,  // triggers when showing a step
        onFinish: null,  // triggers when Finish button is clicked
        includeFinishButton: true   // Add the finish button
    };

})( jQuery );

jQuery( document ).ready( function() {
    // Smart Wizard
    jQuery( '#yop-poll-wizard' ).smartWizard( {
        selected: 0,
        enableAllSteps: (isdone == - 1 ) ? false : true,
        transitionEffect: 'fade',
        includeFinishButton: true,
        hideButtonsOnDisabled: true,
        labelFinish: 'Save',
        onShowStep: function( object, context ) {
            var toStep = context.toStep;
            if( toStep == 4 && isdone == - 1 ) {
                jQuery( '.buttonNext' ).css( 'display', 'none' );
                jQuery( '#yop-poll-wizard' ).smartWizard( "enableFinish", true );
                jQuery('.actionBar' ).css('display','block');
            }
            else {
                jQuery( '.buttonNext' ).css( 'display', 'inline' );
                jQuery('.actionBar' ).css('display','block');
            }
            if( toStep == 5 ) {
                jQuery( '.buttonNext' ).css( 'display', 'none' );
                jQuery( '.buttonPrevious' ).css( 'display', 'none' );
                jQuery( '.buttonFinish' ).css( 'display', 'none' );
                jQuery('.actionBar' ).css('display','none');
            }

            return true;
        },
        onFinish: onFinishCallback,
        onLeaveStep: leaveAStepCallback
    } );

    function leaveAStepCallback( obj, context ) {
        return validateSteps( context.fromStep ); // return false to stay on step and true to continue navigation
    }

    function onFinishCallback( obj, context ) {
        response = save_poll();
        if( response.success == 1 ) {
            jQuery( 'html, body' ).animate( {scrollTop: '0px'}, 800 );

            jQuery( '#message' ).html( '<p>' + response.message + '</p>' );

            jQuery( "#message" ).removeClass();

            jQuery( '#message' ).addClass( 'updated' );

            jQuery( '#message' ).show();
            jQuery( '#yop-poll-wizard' ).smartWizard( "enableFinish", true );
            jQuery( '#yop-poll-wizard' ).smartWizard("enableStep",2);
            jQuery( '#yop-poll-wizard' ).smartWizard("enableStep",3);
            jQuery( '#yop-poll-wizard' ).smartWizard("enableStep",4);
            jQuery( '#yop-poll-wizard' ).smartWizard("enableStep",5);

            jQuery( '#yop_poll_shortcode' ).val( '[yop_poll id="' + response.id + '"]' );

            console.log( jQuery( '#yop_poll_shortcode' ).position().top );
            window.setTimeout( function() {
                jQuery( 'html, body' ).animate( {scrollTop: jQuery( '#yop_poll_shortcode' ).position().top }, 800 );
            }, 100 );
        }
        else {
            jQuery( 'html, body' ).animate( {scrollTop: '0px'}, 800 );

            jQuery( '#message' ).html( '<p>' + response.message + '</p>' );

            jQuery( "#message" ).removeClass();

            jQuery( '#message' ).addClass( 'updated' );

            jQuery( '#message' ).show();
        }
        return false;
    }

    function validateSteps( stepNumber ) {
        var id_questions = jQuery( 'input[name$="question_id]"]' );
        var returnVal = true;
        var i=0;
        jQuery.each( id_questions, function() {
            id = jQuery( this ).val();
            i++;
            if( jQuery( 'input[name^="yop_poll_question\\[question_' + id + '\\]"][name$="\\[question\\]"]' ).val() ==
                '' ) {
                jQuery( 'input[name^="yop_poll_question\\[question_' + id +
                    '\\]"][name$="\\[question\\]"]' ).css( {'border': "1px solid red"} ).click( function() {
                        jQuery( this ).css( 'border', "" );
                    } );
                jQuery( 'html, body' ).animate( {scrollTop: '0px'}, 800 );

                jQuery( '#message' ).html( '<p>' + "Question "+ jQuery('input[name="yop_poll_question\\[question_'+id+'\\]\\[poll_order\\]"]' ).val() +" is empty!"+ '</p>' );

                jQuery( "#message" ).removeClass();

                jQuery( '#message' ).addClass( 'updated' );

                jQuery( '#message' ).show();
                returnVal = false;
            }
            answers = jQuery( 'input[name^="yop_poll_answer\\[' + id + '\\]\\[answer_"][name$="\\]\\[answer\\]"]' );
            answers_media = jQuery( 'textarea[name^="yop_poll_answer\\[' + id + '\\]\\[answer_"][name$="\\]\\[answer\\]"]' );
            jQuery.each( answers, function() {
                if( jQuery( this ).val() == '' ) {
                    jQuery( this ).css( {'border': "1px solid red"} ).click( function() {
                        jQuery( this ).css( 'border', "" );
                    } );
                    jQuery( 'html, body' ).animate( {scrollTop: '0px'}, 800 );

                    jQuery( '#message' ).html( '<p>' + "Please fill in empty answers from Question "+ jQuery('input[name="yop_poll_question\\[question_'+id+'\\]\\[poll_order\\]"]' ).val()+"!"+ '</p>' );

                    jQuery( "#message" ).removeClass();

                    jQuery( '#message' ).addClass( 'updated' );

                    jQuery( '#message' ).show();
                    returnVal = false;
                }
            } );
            var d=0;
            jQuery.each( answers_media, function() {
                d++;
                if(d%3==0){
                    if( jQuery( this ).val() == '' ) {
                        jQuery( this ).css( {'border': "1px solid red"} ).click( function() {
                            jQuery( this ).css( 'border', "" );
                        } );
                        jQuery( 'html, body' ).animate( {scrollTop: '0px'}, 800 );

                        jQuery( '#message' ).html( '<p>' + "Please fill in empty answers from Question "+ jQuery('input[name="yop_poll_question\\[question_'+id+'\\]\\[poll_order\\]"]' ).val()+"!"+ '</p>' );

                        jQuery( "#message" ).removeClass();

                        jQuery( '#message' ).addClass( 'updated' );

                        jQuery( '#message' ).show();
                        returnVal = false;

                    }
                }
            } );
        } )
        if(i==0){
            jQuery( 'html, body' ).animate( {scrollTop: '0px'}, 800 );

            jQuery( '#message' ).html( '<p>' + "Please add a question!"+ '</p>' );

            jQuery( "#message" ).removeClass();

            jQuery( '#message' ).addClass( 'updated' );

            jQuery( '#message' ).show();
            returnVal = false;
        }
        if(returnVal==true)
            jQuery( '#message' ).hide();
        return returnVal;
    }
} );