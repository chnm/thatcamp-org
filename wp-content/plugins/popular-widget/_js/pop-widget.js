/**
 * Image Store - Front-end
 *
 * @file pop-widget.js
 * @package Popular Widget
 * @author Hafid Trujillo
 * @copyright 20010-2013
 * @filesource  wp-content/plugins/image-store/_js/pop-widget.js
 * @since 0.5.0
 */

(function(e){if(typeof popwid=="undefined")return;setTimeout(function(){e.post(popwid.ajaxurl,{postid:popwid.postid,action:"popwid_page_view_count"})},400)})(jQuery);jQuery(document).ready(function(e){e.noConflict();try{e("div.pop-inside ul").hide();e(".pop-widget-tabs").each(function(){tabid=e(this).attr("id").replace("pop-widget-tabs-","");e("#pop-widget-tabs-"+tabid+" a").eq(0).addClass("active");e(".pop-inside-"+tabid+" ul").eq(0).show()});e(".pop-widget-tabs a").click(function(){tab=e(this).attr("href").replace("#","");id=e(this).parents(".pop-widget-tabs").attr("id").replace("pop-widget-tabs-","");e("#pop-widget-tabs-"+id+" a").removeClass("active");e(this).addClass("active");inx=e("#pop-widget-tabs-"+id+" a").index(e(this));e(".pop-inside-"+id+" ul").hide();e(".pop-inside-"+id+" ul").eq(inx).show();return false})}catch(t){return false}})