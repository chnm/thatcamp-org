jQuery(document).ready(function($){
			
			var gridRows = 3;

				$( '#ri-grid' ).gridrotator( {
					rows		: gridRows,
					columns		: 15,
					animType	: 'fadeInOut',
					animSpeed	: 500,
					interval	: 500,
					step		: 1,
					w320		: {
						rows	: 3,
						columns	: 4
					},
					w240		: {
						rows	: 3,
						columns	: 4
					}
				} );
			
			});