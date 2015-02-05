function edit_yopp_ban( id, poll_value , ban_type,ban_period,ban_unit,ban_value ) {
    var id_poll = "#yop-poll-edit-ban-" + id;
    var id_hideen = "#yop-poll-ban-idban-" + id;
    jQuery("#yop-poll-bans-polls option").filter(function() {
        return jQuery(this).val() == poll_value;
    }).attr('selected', true);
    jQuery("#yop-poll-bans-type option").filter(function() {
        return jQuery(this).val() == ban_type;
    }).attr('selected', true);
    jQuery("#yop-poll-bans-unit option").filter(function() {
        return jQuery(this).val() == ban_unit;
    }).attr('selected', true);
    jQuery( '#yop-poll-ban-period' ).val( ban_period );
    jQuery( '#yop-poll-ban-value' ).val( ban_value );
    jQuery( "#retain-id" ).val( id );
    jQuery( "#action" ).attr( 'value', "edit-ban" );
    jQuery( '#yop-poll-add-ban-div' ).fadeIn( 'medium' );
    jQuery( "#add-new-ban" ).val( "Edit" );
    jQuery( '#yop-poll-add-ban-close' ).click( function () {
    jQuery( '#yop-poll-add-ban-div' ).fadeOut( 'medium' );
    } );
    jQuery('#message').hide();

}
