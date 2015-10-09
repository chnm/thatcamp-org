/**
 * Image Store - Admin
 *
 * @file admin.js
 * @package Popular Widget
 * @author Hafid Trujillo
 * @copyright 20010-2013
 * @filesource  wp-content/plugins/image-store/_js/admin.js
 * @since 0.5.0
 */
 
jQuery(document).ready(function($){ 
	
	$.noConflict();
	
	var popw_init = function( ){
		$( ".popw-sortable" ).sortable({ items: '.sort-tabs' })//.disableSelection();
	};
	
	$( '#widgets-right').delegate( '.popw-collapse', 'click', function( ){
		if( $(this).next( ).is(':hidden')) 
			$(this).next( ).show( ); 
		else $(this).next( ).hide( );
	});
	
	$("#widgets-right").delegate( '.rename', 'click', function(){
		tab = $(this).attr( 'href' ).replace( '#', '');
		$( ".rename-" + tab ).toggle( );
		return false;
	});
	
	$("#widgets-right").delegate( '.sort-tabs input[type=checkbox]', 'click', function(){
		if( $(this).is(":checked") ) 
			$(this).parent( ).addClass( 'disabled' );
		else $(this).parent( ).removeClass( 'disabled' );
	});
	
	popw_init( ); $( "#widgets-right" ).ajaxSuccess( popw_init );
});