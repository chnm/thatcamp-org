function insertYopPoll( destination, poll_id, tr_id ) {
	tr_id  = typeof tr_id  !== 'undefined' ? tr_id  : '';
	if ( isNaN( poll_id ) ) {
		edInsertContent( destination, '[yop_poll]' );
	}
	else {
		if ( poll_id != null && poll_id != '' ) {
			if ( tr_id != '' )
			edInsertContent( destination, '[yop_poll id="'+poll_id+'" tr_id="'+tr_id+'"]' );
			else 
			edInsertContent( destination, '[yop_poll id="'+poll_id+'"]' );
		}
		else {
			edInsertContent( destination, '[yop_poll]' );
		}
		tb_remove();
	}
}

function show_tb() {
	var openURL = yop_poll_editor_config.dialog_html_url + '&height=170&width=300&inlineId=yop_poll_html_dialog';
	tb_show( yop_poll_editor_config.title, openURL, '' );
	jQuery(function($) {
			tb_position = function() {
				var tbWindow = $('#TB_window');
				var H = tbWindow.height();
				var W = tbWindow.width();

				if ( tbWindow.size() ) {
					$('#TB_iframeContent').width( W - 50 ).height( H - 75 );
					tbWindow.css({'margin-left': '-' + parseInt((( W - 50 ) / 2),10) + 'px'});
					if ( typeof document.body.style.maxWidth != 'undefined' )
						tbWindow.css({'top':( ( $(window).height() / 2) - 100 ) + 'px'});
				};
			};

			$(window).resize( function() { tb_position() } );
	});
}

jQuery(document).ready(function($){

		if(document.getElementById("ed_toolbar")){
			edButtons[edButtons.length] = new edButton("ed_yop_poll", yop_poll_editor_config.name, "", "", "");
			jQuery(document).ready(function($){
					jQuery('#qt_content_ed_yop_poll').replaceWith('<input onclick="show_tb();" type="button" id="qt_content_ed_yop_poll" accesskey="" class="ed_button" value="' + yop_poll_editor_config.name + '" title="' + yop_poll_editor_config.title + '" />');
			});
		}
});