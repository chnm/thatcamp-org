/**
 * Image Store - Admin
 *
 * @file admin.js
 * @package Popular Widget
 * @author Hafid Trujillo
 * @copyright 20010-2013
 * @filesource  wp-content/plugins/image-store/_js/admin.js
 * @since 0.5.0
 */
 
jQuery(document).ready(function(e){e.noConflict();var t=function(){e(".popw-sortable").sortable({items:".sort-tabs"});e(".popw-sortable").disableSelection()};e("#widgets-right").delegate(".popw-collapse","click",function(){if(e(this).next().is(":hidden"))e(this).next().show();else e(this).next().hide()});e("#widgets-right").delegate(".rename","click",function(){tab=e(this).attr("href").replace("#","");e(".rename-"+tab).toggle();return false});e("#widgets-right").delegate(".sort-tabs input[type=checkbox]","click",function(){if(e(this).is(":checked"))e(this).parent().addClass("disabled");else e(this).parent().removeClass("disabled")});t();e("#widgets-right").ajaxSuccess(t)})