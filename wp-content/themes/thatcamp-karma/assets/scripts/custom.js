jQuery(document).ready(function($){
	// Make placeholder work the way it should
	$('input:text, textarea').each(function(){
		var $this = $(this);
		$this.data('placeholder', $this.attr('placeholder'))
		 .focus(function(){$this.removeAttr('placeholder');})
		 .blur(function(){$this.attr('placeholder', $this.data('placeholder'));});
	});
			
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
