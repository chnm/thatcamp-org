jQuery(document).ready(function($){
			
	$( '#ri-grid' ).gridrotator( {
		rows		: 2,
		columns		: 15,
		animType	: 'fadeInOut',
		animSpeed	: 1000,
		interval	: 600,
		step		: 1,
		w320		: {
			rows	: 2,
			columns	: 4
		},
		w240		: {
			rows	: 2,
			columns	: 4
		}
	} );

			});
			
jQuery(document).ready(function($) {
    $('#show-menu').click(function(){
    $('div#responsive-menu').toggle();
   });
 });
