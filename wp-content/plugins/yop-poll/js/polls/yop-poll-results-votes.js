
jQuery( document ).ready( function( jQuery ) {
    jQuery(".yop_poll_tooltip-buy_pro" ).click( function(){
        jQuery(".yop_poll_pro_feature").dialog({

            height: '700',

            width: '750',

            resizable: false,


            modal: true,

            dialogClass: 'fixed-dialog'



            //position:{top:'top+100'}

        });
      //  if (jQuery(this).is(':radio')) {
         //   jQuery(this ).attr('checked', false);

       // }
    });
    jQuery('#yop-poll-pro-close').click(function() {

        jQuery(".yop_poll_pro_feature").dialog('close');
    });
    if(jQuery("#yop-poll-answer-votes-print").is(":checked"))
        jQuery(".yop-poll-print-votes-votes").css('display','inline');
    else
        jQuery(".yop-poll-print-votes-votes").css('display','none');
});
function show_poll_to_vote(){


    jQuery( "#success>p" ).remove();
    jQuery( "#error>p" ).remove();
    jQuery("#yop_poll_add_votes").dialog({

        minHeight: 'auto',

        minWidth: 'auto',



        modal: true

    });
}
function yop_poll_print_values(){
    if(jQuery("#yop-poll-answer-votes-print").is(":checked"))
        jQuery(".yop-poll-print-votes-votes").css('display','block');
    else
        jQuery(".yop-poll-print-votes-votes").css('display','none');
}
function show_poll_print_options(){


    jQuery( "#success>p" ).remove();
    jQuery( "#error>p" ).remove();
    jQuery("#yop_poll_print_votes").dialog({

        minHeight: 'auto',

        minWidth: 'auto',



        modal: true

    });
}

function add_votes( ) {



    var response = new Object();

    jQuery.ajax( {

        type: 'POST',

        url: yop_poll_global_settings.ajax_url,

        data: 'action=yop_poll_add_votes' + '&' + jQuery( "#yop_poll_add_votes" ).serialize(),

        cache: false,

        async: false,

        error: function( data ) {alert(234)},
        success: function( data ) {
            data = JSON.parse( data );
            if(data.success!=null){

                jQuery( '#success' ).html( '<p>' + data.success + '</p>' );
                jQuery( "#error>p" ).remove();
                jQuery( '#success' ).show();

                jQuery(data.append_row ).appendTo( "#table_results").bind( "mouseenter mouseleave",function(){

                    jQuery(".more_details").click( function(event)

                    {

                        var clicked = jQuery(this); // jQuery wrapper for clicked element



                        if(jQuery(clicked).closest('tr').next('tr').is(":visible") ){

                            jQuery(clicked).closest('tr').next('tr').hide();

                        }

                        else

                        {

                            jQuery(".results_details").each(function( i ) {

                                jQuery(this).hide()

                            });



                            jQuery(clicked).closest('tr').next('tr').show();

                        }



                        jQuery(".less_details").each(function( i ) {

                            jQuery(this).hide();

                            jQuery(this).prev('td').show();

                        });



                        jQuery(clicked).closest('td').next('td').show();

                        jQuery(clicked).hide();

                    })
                    jQuery(".less_details").click( function(event)

                    {

                        var clicked = jQuery(this); // jQuery wrapper for clicked element



                        if(jQuery(clicked).closest('tr').next('tr').is(":visible") ){

                            jQuery(clicked).closest('tr').next('tr').hide();

                        }



                        else{

                            jQuery(".results_details").each(function( i ) {

                                jQuery(this).hide();

                            });

                            jQuery(clicked).closest('tr').next('tr').show();

                        }



                        jQuery(clicked).prev('td').show();

                        jQuery(clicked).hide();

                    })
                } );
                jQuery('.hidden_tds' ).hide()
                jQuery('.results_details' ).hide()
                jQuery('.less_details' ).hide()
                jQuery(".checkboxes").each(function( i ){
                    if( jQuery( this ).attr( 'checked' ) == "checked" ) {
                        ans_id=jQuery(this ).val();
                        $old_votes=parseInt(jQuery("#number_of_votes_"+ans_id ).text(),10);
                        new_votes=$old_votes+ parseInt(jQuery("#yop_poll_no_of_votes_"+ans_id+"_per_answer" ).val());
                        jQuery("#number_of_votes_"+ans_id ).text(parseInt(new_votes));
                    }
                });
                jQuery( '#success>p' ).css({'font-size':"20px"});
                jQuery( '#success>p' ).css({"font-weight":"bold"});
                jQuery("#yop-poll-bans-type").change(function(){

                    var index=jQuery("#index_ban_div").val();

                    var ban_type=jQuery("#yop-poll-bans-type").val();

                    jQuery("#yop-poll-ban-value").val(jQuery("#yop-poll-results-"+ban_type+"_"+index).val());

                })
            }
            else{
                jQuery( '#error' ).html( '<p>' + data.error + '</p>' );

                jQuery( "#success>p" ).remove();
                jQuery( '#error>p' ).css({'font-size':"20px"});
                jQuery( '#error>p' ).css({"font-weight":"bold"});
                jQuery( '#add_votes' ).css({'text-align':"center"});

                jQuery( '#error' ).show();
            }
        }


    } );


    return response;

}



jQuery( document ).ready( function (){





});


function show_details(){

    var clicked = jQuery(this);
    jQuery(this).hide()// jQuery wrapper for clicked element
    console.log(jQuery(clicked).closest('tr').next('tr').val());
    jQuery(clicked).closest('tr').next().show();
    if(jQuery(this).closest('tr').next('tr').is(":visible") ){

        jQuery(this).closest('tr').next('tr').hide();
    }

    else

    {

        jQuery(".results_details").each(function( i ) {

            jQuery(this).hide()

        });



        jQuery(clicked).closest('tr').next('tr').show();

    }

}
jQuery(".more_details").click( function(event)

{

    var clicked = jQuery(this); // jQuery wrapper for clicked element



    if(jQuery(clicked).closest('tr').next('tr').is(":visible") ){

        jQuery(clicked).closest('tr').next('tr').hide();

    }

    else

    {

        jQuery(".results_details").each(function( i ) {

            jQuery(this).hide()

        });



        jQuery(clicked).closest('tr').next('tr').show();

    }



    jQuery(".less_details").each(function( i ) {

        jQuery(this).hide();

        jQuery(this).prev('td').show();

    });



    jQuery(clicked).closest('td').next('td').show();

    jQuery(clicked).hide();

})





jQuery(".less_details").click( function(event)

{

    var clicked = jQuery(this); // jQuery wrapper for clicked element



    if(jQuery(clicked).closest('tr').next('tr').is(":visible") ){

        jQuery(clicked).closest('tr').next('tr').hide();

    }



    else{

        jQuery(".results_details").each(function( i ) {

            jQuery(this).hide();

        });

        jQuery(clicked).closest('tr').next('tr').show();

    }



    jQuery(clicked).prev('td').show();

    jQuery(clicked).hide();

})




jQuery("#yop-poll-bans-type").change(function(){
    var index=jQuery("#index_ban_div").val();

    var ban_type=jQuery("#yop-poll-bans-type").val();

    jQuery("#yop-poll-ban-value").val(jQuery("#yop-poll-results-"+ban_type+"_"+index).val());

})



jQuery('#yop-poll-add-ban-close').click(function() {

    jQuery("#yop-poll-add-ban-div-dialog").dialog('close');

});

function close_add_new_votes(){
    jQuery("#show_poll_to_vote").dialog('close');

}



