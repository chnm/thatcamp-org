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

	function replace_dates($post, $string) {
		
		// PHP 5.3 and upwards compatible, use preg_replace_callback for regular expressions with /e parameter instead of preg_replace
		// http://wordpress.org/support/topic/php-55-preg_replace-e-modifier-depricated?replies=1
		$post_date_gmt = $post->post_date_gmt;
		$string = preg_replace("/\%post_date_gmt\(\)/", mysql2date('F jS, Y', $post_date_gmt), $string);	
		$callback = 
			function ($matches) use ($post_date_gmt)
			{
				return mysql2date($matches[1], $post_date_gmt);
			};	
		$string = preg_replace_callback("/\%post_date_gmt\(([a-zA-Z\s\\\\:,]*)\)/", $callback, $string);
		$string = str_replace( "%post_date_gmt", 	$post_date_gmt, 	$string);

		$post_date = $post->post_date;
		$string = preg_replace("/\%post_date\(\)/", mysql2date('F jS, Y', $post_date), $string);	
		$callback = 
			function ($matches) use ($post_date)
			{
				return mysql2date($matches[1], $post_date);
			};
		$string = preg_replace_callback("/\%post_date\(([a-zA-Z\s\\\\:,]*)\)/", $callback, $string);
		$string = str_replace( "%post_date", 	$post_date, 	$string);

		$string = str_replace( "%post_status", 	$post->post_status, 	$string);

		$post_modified_gmt = $post->post_modified_gmt;
		$string = preg_replace("/\%post_modified_gmt\(\)/", mysql2date('F jS, Y', $post_modified_gmt), $string);	
		$callback = 
			function ($matches) use ($post_modified_gmt)
			{
				return mysql2date($matches[1], $post_modified_gmt);
			};
		$string = preg_replace_callback("/\%post_modified_gmt\(([a-zA-Z\s\\\\:,]*)\)/", $callback, $string);
		$string = str_replace( "%post_modified_gmt", 	$post_modified_gmt, 	$string);

		$post_modified = $post->post_modified;
		$string = preg_replace("/\%post_modified\(\)/", mysql2date('F jS, Y', $post_modified), $string);
		$callback = 
			function ($matches) use ($post_modified)
			{
				return mysql2date($matches[1], $post_modified);
			};
		$string = preg_replace_callback("/\%post_modified\(([a-zA-Z\s\\\\:,]*)\)/", $callback, $string);
		$string = str_replace( "%post_modified", 	$post_modified, 	$string);
		
	}

?>