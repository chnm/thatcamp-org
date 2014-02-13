<?php

/*  Copyright 2012 Diana Koenraadt (email : diana at dianakoenraadt dot nl)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

	function replace_dates($post, $string){
		
		// PHP 5.2 and downwards compatible, use preg_replace for all regular expressions
		$post_date_gmt = $post->post_date_gmt;
		$string = preg_replace("/\%post_date_gmt\(\)/", mysql2date('F jS, Y', $post_date_gmt), $string);
		$string = preg_replace("/\%post_date_gmt\(([a-zA-Z\s\\\\:,]*)\)/e", "mysql2date('$1', '$post_date_gmt')", $string);
		$string = str_replace( "%post_date_gmt", 	$post_date_gmt, 	$string);

		$post_date = $post->post_date;
		$string = preg_replace("/\%post_date\(\)/", mysql2date('F jS, Y', $post_date), $string);
		$string = preg_replace("/\%post_date\(([a-zA-Z\s\\\\:,]*)\)/e", "mysql2date('$1', '$post_date')", $string);
		$string = str_replace( "%post_date", 	$post_date, 	$string);

		$string = str_replace( "%post_status", 	$post->post_status, 	$string);

		$post_modified_gmt = $post->post_modified_gmt;
		$string = preg_replace("/\%post_modified_gmt\(\)/", mysql2date('F jS, Y', $post_modified_gmt), $string);
		$string = preg_replace("/\%post_modified_gmt\(([a-zA-Z\s\\\\:,]*)\)/e", "mysql2date('$1', '$post_modified_gmt')", $string);
		$string = str_replace( "%post_modified_gmt", 	$post_modified_gmt, 	$string);

		$post_modified = $post->post_modified;
		$string = preg_replace("/\%post_modified\(\)/", mysql2date('F jS, Y', $post_modified), $string);
		$string = preg_replace("/\%post_modified\(([a-zA-Z\s\\\\:,]*)\)/e", "mysql2date('$1', '$post_modified')", $string);
		$string = str_replace( "%post_modified", 	$post_modified, 	$string);
		
	}


?>