jQuery( document ).ready( function() {


    jQuery(".yop_poll_tooltip-buy_pro" ).click( function(){
        jQuery(".yop_poll_pro_feature").dialog({

            height: '700',

            width: '750',

            resizable: false,

            modal: true,

            //position: { my: 'top', at: 'top+130' }
            dialogClass: 'fixed-dialog'

            //position:{top:'top+100'}

        });
        //if (jQuery(this).is(':radio')) {
         //   jQuery(this ).attr('checked', false);

       // }
    });
    jQuery('#yop-poll-pro-close').click(function() {

        jQuery(".yop_poll_pro_feature").dialog('close');
        if (jQuery(this).is(':radio')) {
            jQuery(this ).attr('checked', false);

        }
    });
    if(jQuery('#yop-poll-view-results-custom').is( ':checked' ) ==false){
        jQuery("#yop-poll-display-view-results-div").hide();

    }
    if(jQuery('#yop-poll-view-results-link-no').is( ':checked' ) ==true){
        jQuery("#yop-poll-view-results-link-div").hide();

    }
    if(jQuery('#yop-poll-view-back-to-vote-link-no').is( ':checked' ) ==true){
        jQuery("#yop-poll-view-back-to-vote-link-div").hide();

    }
    if(jQuery('#yop-poll-view-total-votes-no').is( ':checked' ) ==true){
        jQuery("#yop-poll-view-total-votes-div").hide();

    }
    if(jQuery('#yop-poll-view-total-answers-no').is( ':checked' ) ==true){
        jQuery("#yop-poll-view-total-answers-div").hide();

    }
    if(jQuery('#yop-poll-vote-permisions-wordpress-no').is( ':checked' ) ==true){
        jQuery("#yop-poll-vote-permisions-wordpress-div").hide();

    }
    if(jQuery('#yop-poll-vote-permisions-anonymous-no').is( ':checked' ) ==true){
        jQuery("#yop-poll-vote-permisions-anonymous-div").hide();

    }
    if(jQuery('#yop-poll-blocking-voters-dont-block').is( ':checked' ) ==true){
        jQuery("#yop-poll-blocking-voters-interval-div").hide();

    }

    if(jQuery('#yop-poll-limit-number-of-votes-per-user-no').is( ':checked' ) ==true){
        jQuery("#yop-poll-number-of-votes-per-user-div").hide();

    }
    if(jQuery('#yop-poll-use-default-loading-image-yes').is( ':checked' ) ==true){
        jQuery("#yop-poll-use-default-loading-image-div").hide();

    }
    if(jQuery('#yop-poll-redirect-after-vote-no').is( ':checked' ) ==true){
        jQuery("#yop-poll-redirect-after-vote-url-div").hide();

    }
    if(jQuery('#yop-poll-show-poll-in-archive-no').is( ':checked' ) ==true){
        jQuery("#yop-poll-show-poll-in-archive-div").hide();

    }
    //<editor-fold desc="View Results Checkboxes">
    jQuery( '#yop-poll-view-results-custom' ).change( function() {
        if( jQuery( this ).attr( 'checked' ) == "checked" ) {
            jQuery( '#yop-poll-display-view-results-div' ).css( "display", "block" );
            jQuery( '.yop-poll-view-results-uncheck-nc, #yop-poll-view-results-never' ).attr( 'checked', false );
        }
        else {
            jQuery( '#yop-poll-display-view-results-div' ).css( "display", "none" );
        }
    } );
    jQuery( '.yop-poll-view-results-uncheck-nc' ).click( function() {
        jQuery( '#yop-poll-view-results-never, #yop-poll-view-results-custom' ).attr( 'checked', false );
        jQuery( '#yop-poll-display-view-results-div' ).css( "display", "none" );
    } );
    jQuery( '#yop-poll-view-results-never' ).click( function() {
        jQuery( '.yop-poll-view-results-uncheck-nc, #yop-poll-view-results-custom' ).attr( 'checked', false );
        jQuery( '#yop-poll-display-view-results-div' ).css( "display", "none" );
    } );
    //</editor-fold>

    //<editor-fold desc="Vote Permissions">
    jQuery( '#yop-poll-vote-permisions-guest' ).change( function() {
        if( jQuery( this ).attr( 'checked' ) == "checked" ) {
            if( jQuery( '#yop-poll-vote-permisions-registered' ).attr( "checked" ) != 'checked' ) {
                jQuery( '.yop-poll-vote-as-div' ).hide();
            }
        }
        if( jQuery( '#yop-poll-vote-permisions-guest' ).attr( "checked" ) != 'checked' &&jQuery( '#yop-poll-vote-permisions-registered' ).attr( "checked" ) != 'checked')
            jQuery( '#yop-poll-vote-permisions-anonymous-label-div' ).hide();
        else
            jQuery( '#yop-poll-vote-permisions-anonymous-label-div' ).show();
    } );
    jQuery( '#yop-poll-vote-permisions-registered' ).change( function() {
        if( jQuery( this ).attr( 'checked' ) == "checked" ) {
            jQuery( '.yop-poll-vote-as-div' ).show();
            if( true == jQuery( '#yop-poll-vote-permisions-wordpress-yes' ).is( ':checked' ) ) {
                jQuery( '#yop-poll-vote-permisions-wordpress-div' ).show();
            }
            else {
                jQuery( '#yop-poll-vote-permisions-wordpress-div' ).hide();
            }
            if( true == jQuery( '#yop-poll-vote-permisions-anonymous-yes' ).is( ':checked' ) ) {
                jQuery( '#yop-poll-vote-permisions-anonymous-div' ).show();
            }
            else {
                jQuery( '#yop-poll-vote-permisions-anonymous-div' ).hide();
            }
        }
        else {
            jQuery( '.yop-poll-vote-as-div' ).hide();
        }
        if( jQuery( '#yop-poll-vote-permisions-guest' ).attr( "checked" ) != 'checked' &&jQuery( '#yop-poll-vote-permisions-registered' ).attr( "checked" ) != 'checked')
            jQuery( '#yop-poll-vote-permisions-anonymous-label-div' ).hide();
        else
            jQuery( '#yop-poll-vote-permisions-anonymous-label-div' ).show();
    } );
    //</editor-fold>

    //<editor-fold desc="Blocking Voters">
    jQuery( ".yop-poll-blocking-voters-hide-interval" ).click( function() {
        jQuery( '#yop-poll-blocking-voters-interval-div' ).hide();
        jQuery( '.yop-poll-blocking-voters-show-interval' ).attr( 'checked', false )
    } );
    jQuery( ".yop-poll-blocking-voters-show-interval" ).click( function() {
        jQuery( '#yop-poll-blocking-voters-interval-div' ).show();
        jQuery( '.yop-poll-blocking-voters-hide-interval' ).attr( 'checked', false );
    } );
    //</editor-fold">

    //<editor-fold desc="Limit number of votes per user">
    jQuery( "#yop-poll-limit-number-of-votes-per-user-no" ).click( function() {
        jQuery( '#yop-poll-number-of-votes-per-user-div' ).hide();
    } );
    jQuery( "#yop-poll-limit-number-of-votes-per-user-yes" ).click( function() {
        jQuery( '#yop-poll-number-of-votes-per-user-div' ).show();
    } );
    //</editor-fold>

    //<editor-fold desc="Social Integration">

    //</editor-fold>

    //<editor-fold desc="Vote As Region">


    jQuery( "#yop-poll-vote-permisions-wordpress-yes" ).click( function() {
        jQuery( '#yop-poll-vote-permisions-wordpress-div' ).show();
    } );
    jQuery( "#yop-poll-vote-permisions-wordpress-no" ).click( function() {
        jQuery( '#yop-poll-vote-permisions-wordpress-div' ).hide();
    } );

    jQuery( "#yop-poll-vote-permisions-anonymous-yes" ).click( function() {
        jQuery( '#yop-poll-vote-permisions-anonymous-div' ).show();
    } );
    jQuery( "#yop-poll-vote-permisions-anonymous-no" ).click( function() {
        jQuery( '#yop-poll-vote-permisions-anonymous-div' ).hide();
    } );
    //</editor-fold>

    jQuery( "#yop-poll-view-results-link-no" ).click( function() {
        jQuery( '#yop-poll-view-results-link-div' ).hide();
    } );
    jQuery( "#yop-poll-view-results-link-yes" ).click( function() {
        jQuery( '#yop-poll-view-results-link-div' ).show();
    } );

    jQuery( "#yop-poll-view-back-to-vote-link-no" ).click( function() {
        jQuery( '#yop-poll-view-back-to-vote-link-div' ).hide();
    } );
    jQuery( "#yop-poll-view-back-to-vote-link-yes" ).click( function() {
        jQuery( '#yop-poll-view-back-to-vote-link-div' ).show();
    } );


    jQuery( "#yop-poll-view-total-answers-no" ).click( function() {
        jQuery( '#yop-poll-view-total-answers-div' ).hide();
    } );
    jQuery( "#yop-poll-view-total-answers-yes" ).click( function() {
        jQuery( '#yop-poll-view-total-answers-div' ).show();
    } );

    jQuery( "#yop-poll-view-total-votes-no" ).click( function() {
        jQuery( '#yop-poll-view-total-votes-div' ).hide();
    } );
    jQuery( "#yop-poll-view-total-votes-yes" ).click( function() {
        jQuery( '#yop-poll-view-total-votes-div' ).show();
    } );

    jQuery( "#yop-poll-view-total-voters-no" ).click( function() {
        jQuery( '#yop-poll-view-total-voters-div' ).hide();
    } );
    jQuery( "#yop-poll-view-total-voters-yes" ).click( function() {
        jQuery( '#yop-poll-view-total-voters-div' ).show();
    } );

    jQuery( "#yop-poll-use-default-loading-image-no" ).click( function() {
        jQuery( '#yop-poll-use-default-loading-image-div' ).show();
    } );
    jQuery( "#yop-poll-use-default-loading-image-yes" ).click( function() {
        jQuery( '#yop-poll-use-default-loading-image-div' ).hide();
    } );

    jQuery( "#yop-poll-redirect-after-vote-yes" ).click( function() {
        jQuery( '#yop-poll-redirect-after-vote-url-div' ).show();
    } );
    jQuery( "#yop-poll-redirect-after-vote-no" ).click( function() {
        jQuery( '#yop-poll-redirect-after-vote-url-div' ).hide();
    } );

    jQuery( "#yop-poll-view-poll-archive-link-no" ).click( function() {
        jQuery( '#yop-poll-view-poll-archive-link-div' ).hide();
    } );
    jQuery( "#yop-poll-view-poll-archive-link-yes" ).click( function() {
        jQuery( '#yop-poll-view-poll-archive-link-div' ).show();
    } );

    jQuery( "#yop-poll-allow-other-answers-yes" ).click( function() {
        jQuery( '.yop-poll-other-answers-label-div' ).show();
        jQuery( '#yop-poll-display-other-answers-values-div' ).show();
        jQuery( '#yop-poll-is-default-other-answers-values-div' ).show();
    } );
    jQuery( "#yop-poll-allow-other-answers-no" ).click( function() {
        jQuery( '.yop-poll-other-answers-label-div' ).hide();
        jQuery( '#yop-poll-display-other-answers-values-div' ).hide();
        jQuery( '#yop-poll-is-default-other-answers-values-div' ).hide();
    } );

    jQuery( "#yop-poll-allow-multiple-answers-yes" ).click( function() {
        jQuery( '#yop-poll-allow-multiple-answers-div' ).show();
        jQuery( '#yop-poll-allow-multiple-answers-div1' ).show();
    } );
    jQuery( "#yop-poll-allow-multiple-answers-no" ).click( function() {
        jQuery( '#yop-poll-allow-multiple-answers-div' ).hide();
        jQuery( '#yop-poll-allow-multiple-answers-div1' ).hide();
    } );


    jQuery( "#yop-poll-display-answers-vertical" ).click( function() {
        jQuery( '#yop-poll-display-answers-tabulated-div' ).hide();
    } );
    jQuery( "#yop-poll-display-answers-orizontal" ).click( function() {
        jQuery( '#yop-poll-display-answers-tabulated-div' ).hide();
    } );
    jQuery( "#yop-poll-display-answers-tabulated" ).click( function() {
        jQuery( '#yop-poll-display-answers-tabulated-div' ).show();
    } );

    jQuery( "#yop-poll-display-results-vertical" ).click( function() {
        jQuery( '#yop-poll-display-results-tabulated-div' ).hide();
    } );
    jQuery( "#yop-poll-display-results-orizontal" ).click( function() {
        jQuery( '#yop-poll-display-results-tabulated-div' ).hide();
    } );
    jQuery( "#yop-poll-display-results-tabulated" ).click( function() {
        jQuery( '#yop-poll-display-results-tabulated-div' ).show();
    } );

    jQuery( "#yop_poll_sorting_archive_polls_as_defined" ).click( function() {
        jQuery( "#yop-poll-sort-archive-polls-rule-div" ).hide();
    } );
    jQuery( "#yop_poll_sorting_archive_polls_database" ).click( function() {
        jQuery( "#yop-poll-sort-archive-polls-rule-div" ).show();
    } );
    jQuery( "#yop_poll_sorting_archive_polls_votes" ).click( function() {
        jQuery( "#yop-poll-sort-archive-polls-rule-div" ).show();
    } );

    jQuery( "#yop-poll-view-results-votes-number" ).click( function() {
        jQuery( "#yop-poll-answer-result-label" ).val( "%POLL-ANSWER-RESULT-VOTES%" );
    } );
    jQuery( "#yop-poll-view-results-percentages" ).click( function() {
        jQuery( "#yop-poll-answer-result-label" ).val( "%POLL-ANSWER-RESULT-PERCENTAGES%" );
    } );
    jQuery( "#yop-poll-view-results-votes-number-and-percentages" ).click( function() {
        jQuery( "#yop-poll-answer-result-label" ).val( "%POLL-ANSWER-RESULT-PERCENTAGES% - ( %POLL-ANSWER-RESULT-VOTES% )" );
    } );

    jQuery( '.yop-poll-sorting-results-as-defined' ).click( function() {
        jQuery('#yop-poll-sorting-results-custom-div' ).hide();
    } );
    jQuery( '.yop-poll-sorting-results-custom' ).click( function() {
        jQuery('#yop-poll-sorting-results-custom-div' ).show();
    } );

    String.prototype.insert = function (index, string) {
        if (index > 0)
            return this.substring(0, index) + string + this.substring(index, this.length);
        else
            return string + this;
    };
    jQuery('#yop-poll-email-notifications-body').textcomplete([

        { // tech companies
            words: ['%VOTE_DATE%', '%POLL_NAME%','%POLL_TOTAL_VOTES%','%WP_USERNAME%','%WP_FIRST_NAME%','%WP_LAST_NAME%', '[ QUESTION ] ','[/QUESTION]', '[ ANSWERS ]', ' %ANSWER_VALUE%', '[/ANSWERS]','[ CUSTOM_FIELDS ]','%CUSTOM_FIELD_NAME%','%CUSTOM_FIELD_VALUE%','[/CUSTOM_FIELDS]'],
            match: /(\[|%)(\w*)$/,
            search: function (term, callback) {
                callback(jQuery.map(this.words, function (word) {
                    return word.indexOf(term) === 0 ? word : null;
                }));
            },
            index: 1,
            replace: function (word) {
                if(word[0]=="[" && word[1]!="/")  {

                return word + ' '+ word.insert(1,"/");
                }
                else
                return word + ' ';
            }
        }

    ]);





} );
