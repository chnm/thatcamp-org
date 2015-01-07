/*
 * Transposh v0.9.3
 * http://transposh.org/
 *
 * Copyright 2013, Team Transposh
 * Licensed under the GPL Version 2 or higher.
 * http://transposh.org/license
 *
 * Date: Mon, 06 May 2013 02:15:55 +0300
 */
(function(a){a(".tp_help").live("click",function(b){b.preventDefault();window.scrollTo(0,0);a("#tab-link-"+jQuery(this).attr("rel")+" a").trigger("click");a("#contextual-help-link").hasClass("screen-meta-active")||a("#contextual-help-link").trigger("click")})})(jQuery);
