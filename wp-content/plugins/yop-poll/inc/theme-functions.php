<?php
	function return_yop_poll( $id = -1, $tr_id = '', $offset = 0 ) {
		global $yop_poll;
		print $yop_poll->return_yop_poll( $id, $tr_id, $offset );
	}

	function return_yop_poll_archive( ) {
		global $yop_poll;
		print $yop_poll->yop_poll_archive_shortcode_function( );
	}