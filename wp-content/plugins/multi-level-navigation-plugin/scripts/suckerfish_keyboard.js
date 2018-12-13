/*
jQuery sfhover Keyboard accessibility script
http://www.transientmonkey.com/

Copyright (c) 2008 malcalevak

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.
*/

(function( $ ){
	$(document).ready(function(){
		$("#suckerfishnav li").hover(
			function() { $(this).addClass("sfhover");},
			function() { $(this).removeClass("sfhover");}
		);
		$("#suckerfishnav a").focus(function(){$(this).parents("li").addClass("sfhover");}).blur(function(){$(this).parents("li").removeClass("sfhover");});
		$("#suckerfishnav_2 a").focus(function(){$(this).parents("li").addClass("sfhover");}).blur(function(){$(this).parents("li").removeClass("sfhover");});
	});
})( jQuery );
