jQuery( document ).ready( function () {
    if ( yop_poll_do_scroll.doScroll == 1 ) {
        var top = jQuery( '#postbox-container-1' ).position();
        var pos = "-" + top.top + "px";
        jQuery( window ).scroll( function ( event ) {
            // what the y position of the scroll is
            var beginScroll = jQuery( this ).scrollTop();
            //get first column width
            var first = parseInt( jQuery( '#post-body-content' ).css( 'width' ) );

            // whether that's below the form
            if ( beginScroll >= jQuery( '#postbox-container-1' ).position().top ) {
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
                pos = "-" + jQuery( '#postbox-container-1' ).position().top + "px";
            }
        } );
    }
} );