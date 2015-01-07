var write_console = false;
var yoppolltitlehint;
jQuery(".yop_poll_tooltip-buy_pro" ).click( function(){
    var id=this.id;
    //console.log(id);
    if(id=='add_new_media_question2'){
        jQuery('.yop-poll-buy-template-li').removeClass('yop-poll-buy-template-selected');
        jQuery('#yop-poll-buy-template-2').addClass('yop-poll-buy-template-selected');
    }
    if(id=='add_new_text_question2')
    {
        jQuery('.yop-poll-buy-template-li').removeClass('yop-poll-buy-template-selected');
        jQuery('#yop-poll-buy-template-1').addClass('yop-poll-buy-template-selected');
    }
    if(id=='yop-poll-1'){
        jQuery('.yop-poll-buy-template-li').removeClass('yop-poll-buy-template-selected');
        jQuery('#yop-poll-buy-template-4').addClass('yop-poll-buy-template-selected');
    }
    if(id=='yop-poll-vote-permisions-facebook-integration-yes' || id=='yop-poll-vote-permisions-facebook-no' ||
            id=='yop-poll-facebook-share-after-vote-yes' || id=='yop-poll-facebook-share-after-vote-no'  ||
            id=='yop-poll-vote-permisions-google-yes' || id=='yop-poll-vote-permisions-google-no'||
            id=='yop-poll-vote-permisions-show-g-share-button-yes'|| id=='yop-poll-vote-permisions-show-g-share-button-no' ||
            id=='yop_poll_google_integration-yes' || id=='yop_poll_google_integration-no'
            || id=='yop-poll-for-slider-page-4'||id=='yop-poll-for-slider-page2'
            || id=='yop-poll-for-slider-page3'){



        jQuery('.yop-poll-buy-template-li').removeClass('yop-poll-buy-template-selected');

        jQuery("#yop-poll-buy-template-3").addClass('yop-poll-buy-template-selected');
    }
    if(id=='yop-poll-user-interface-type-beginner' || id=='yop-poll-user-interface-type-advanced'){
        jQuery('.yop-poll-buy-template-li').removeClass('yop-poll-buy-template-selected');
        jQuery("#yop-poll-buy-template-0").addClass('yop-poll-buy-template-selected');
    }
    if(id=='yop-poll-for-slider-page'||id=='yop-poll-for-slider-page1'){
        jQuery('.yop-poll-buy-template-li').removeClass('yop-poll-buy-template-selected');
        jQuery("#yop-poll-buy-template-3").addClass('yop-poll-buy-template-selected');
    }
    if(id=='yop-poll-for-slider-page-7'){
        jQuery('.yop-poll-buy-template-li').removeClass('yop-poll-buy-template-selected');
        jQuery("#yop-poll-buy-template-5").addClass('yop-poll-buy-template-selected');
    }




    jQuery(".yop_poll_pro_feature").dialog({

        height: '650',

        width: 'auto',

        resizable: false,

        modal: true,

        dialogClass: 'fixed-dialog'

        //position:{top:'top+100'}

    });
    jQuery( "#yop-poll-buy-template-slider" ).jcarousel( 'scroll', jQuery( '.yop-poll-buy-template-selected' ) );
    //if (jQuery(this).is(':radio')) {
    //    jQuery(this ).attr('checked', false);

    // }
    // jQuery(".yop_poll_pro_feature").removeClass('yop-poll-buy-template-selected');
});
jQuery('#yop-poll-pro-close').click(function() {

    jQuery(".yop_poll_pro_feature").dialog('close');

});

function cslw( msg ) {
    if ( write_console ) {
        if ( console && console.log ) {
            console.log( msg );
        }
    }
}

jQuery( document ).ready( function ( jQuery ) {



    var top = jQuery( '#postbox-container-1' ).position();
    if( top !== undefined ) {
        var pos = "-" + top.top + "px";
        jQuery( window ).scroll( function ( event ) {
            // what the y position of the scroll is
            var beginScroll = jQuery( this ).scrollTop();
            //get first column width
            var first = parseInt( jQuery( '#post-body-content' ).css( 'width' ) );

            // whether that's below the form
            if ( beginScroll >= top.top ) {
                //make div's position fixed
                jQuery( '#postbox-container-1' ).css( {
                    'position': 'fixed',
                    'margin-left': (first + 20) + "px",
                    'margin-top': pos
                } );
            }
            else {
                //restore div to initial position
                jQuery( '#postbox-container-1' ).css( {
                    'position': '',
                    'margin-left': '',
                    'margin-top': "0px"
                } );
            }
        } );

    }
    var totWidth = jQuery('.set-upgrade-to-pro-width2').width();
    jQuery('.set-upgrade-to-pro-width').css({
        'width': totWidth + 'px'
    });
    var p=totWidth/2 -  jQuery('.set-slider-margin').width()/2;
    var oo=totWidth - 800;
    oo=oo/2;
    var g=oo-40;

    var s=totWidth-p;
    jQuery('.set-upgrade-to-pro-width5').css({
        'width': s + 'px'
    });
    jQuery('.set-upgrade-to-pro-width4').css({
        'margin-left': p + 'px'
    });
    oo=oo+820;
    jQuery('.align-next-button').css({
        'left': oo + 'px'

    });
    jQuery('.align-prev-button').css({
        'left': g + 'px'

    });

    togglehandlediv = function () {
        jQuery( ".stuffbox .handlediv" ).unbind( "click" );
        jQuery( ".stuffbox .handlediv" ).click( function () {
            jQuery( this ).parent().toggleClass( "closed" );

        } );

    }

    yoppolltitlehint = function ( id ) {
        id = id || 'yop-poll-title';

        var title = jQuery( '.' + id ), titleprompt = jQuery( '.' + id + '-prompt-text' );

        title.each( function ( index ) {
            if ( jQuery( this ).val() == '' ) {
                jQuery( this ).parent().children( '.' + id + '-prompt-text' ).removeClass( 'screen-reader-text' );
            }
            else {
                jQuery( this ).parent().children( '.' + id + '-prompt-text' ).addClass( 'screen-reader-text' );
            }
        } );

        titleprompt.click( function () {
            jQuery( this ).addClass( 'screen-reader-text' );
            jQuery( this ).parent().children( '.' + id ).focus();
        } );

        title.blur(function () {
            if ( this.value == '' ) {
                jQuery( this ).parent().children( '.' + id + '-prompt-text' ).removeClass( 'screen-reader-text' );
            }
        } ).focus(function () {
                jQuery( this ).parent().children( '.' + id + '-prompt-text' ).addClass( 'screen-reader-text' );
            } ).keydown( function ( e ) {
                jQuery( this ).parent().children( '.' + id + '-prompt-text' ).addClass( 'screen-reader-text' );
                jQuery( this ).unbind( e );
            } );
    }

    yoppolltitlehint( 'yop-poll-title' );
    yoppolltitlehint( 'yop-poll-subtitle' );
    togglehandlediv();
    jQuery(function() {
        jQuery('.hascolorpicker').colorpicker();
    });
    jQuery( '.hasDatePicker' ).datetimepicker( {
        showSecond: true,
        timeFormat: 'hh:mm:ss',
        dateFormat: yop_poll_global_settings.date

    } );

} );

function yop_poll_update_bar_style( obj, property, value ) {
    if (
            'background-color' == property ||
                    'height' == property ||
                    'border-color' == property ||
                    'border-width' == property ||
                    'border-style' == property
            ) {
        if ( jQuery( obj ).length > 0 ) {
            if ( '' != value )
                jQuery( obj ).css( property, value );
        }
    }
}
;
function end_date_option(){
    if( jQuery('#yop-poll-never-expire').is(':checked') ) {
        jQuery('#yop-poll-end-date-input').hide();
    }
    else{
        if(yop_poll_global_settings.date_format=="UE"){
            var date=jQuery('#yop-poll-start-date').val();
            var data2=date.split("-",3);
            data2[2]=data2[2].split(" ",1);
            if(data2[1]<12){
                data2[1]=parseInt(data2[1])+1;
            }
            else{
                data2[0]="01";
                data2[1]="01";
                data2[2]=parseInt(data2[2])+1;
            }
            var dateForInput=data2[0]+"-"+data2[1]+"-"+data2[2]+" 23:59:59";
            jQuery('#yop-poll-end-date-input').val(dateForInput);
        }
        else
        {
            if(yop_poll_global_settings.date_format=="US"){
                var date=jQuery('#yop-poll-start-date').val();

                var data2=date.split("-",3);
                data2[2]=data2[2].split(" ",1);
                if(data2[0]<12){
                    data2[0]=parseInt(data2[0])+1;
                }
                else{
                    data2[0]="01";
                    data2[1]="01";
                    data2[2]=parseInt(data2[2])+1;
                }

                var dateForInput=data2[0]+"-"+data2[1]+"-"+data2[2]+" 23:59:59";
                jQuery('#yop-poll-end-date-input').val(dateForInput);
        }
        }
        jQuery('#yop-poll-end-date-input').show();
    }
}



