/*
 * Transposh v0.9.0
 * http://transposh.org/
 *
 * Copyright 2012, Team Transposh
 * Licensed under the GPL Version 2 or higher.
 * http://transposh.org/license
 *
 * Date: Wed, 12 Dec 2012 22:23:17 +0200
 */
(function(a){a(function(){a(".warning-close").click(function(){a(this).parents("div:first").hide();a.post(ajaxurl,{action:"tp_close_warning",id:a(this).attr("id")})})})})(jQuery);
