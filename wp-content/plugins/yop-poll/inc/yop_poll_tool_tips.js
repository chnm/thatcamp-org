jQuery( document ).ready( function ( jQuery ) {
alert(yop_poll_tool_tips)
 if(yop_poll_tool_tips!=undefined){
      jQuery(".yop_poll_pro_function"  ).tooltip({
          position: {
              my: "center bottom-20",
              height:1000,
              at: "center top",
              using: function( position, feedback ) {
                  jQuery( this ).css( position );
                  jQuery( "<div>" )
                          .addClass( "arrow" )
                          .addClass( feedback.vertical )
                          .addClass( feedback.horizontal )
                          .appendTo( this );
              }
          }
      })

  }
})