<?php
	function return_yop_poll( $id = -1, $tr_id = '', $offset = 0 ) {
		global $yop_poll_public_admin;
		print $yop_poll_public_admin->return_yop_poll( $id, $tr_id, $offset );
	}
	
	function return_yop_poll_archive( ) {
		global $yop_poll_public_admin;
		print $yop_poll_public_admin->yop_poll_archive_shortcode_function( );
	}