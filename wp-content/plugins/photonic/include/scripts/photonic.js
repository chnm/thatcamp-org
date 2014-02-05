/*
* waitForImages 1.3
* -----------------
* Provides a callback when all images have loaded in your given selector.
* http://www.alexanderdickson.com/
*
*
* Copyright (c) 2011 Alex Dickson
* Licensed under the MIT licenses.
* See website for more info.
*
*/

;(function($) {
    $.fn.waitForImages = function(finishedCallback, eachCallback) {

        eachCallback = eachCallback || function() {};

        if ( ! $.isFunction(finishedCallback) || ! $.isFunction(eachCallback)) {
            throw {
                name: 'invalid_callback',
                message: 'An invalid callback was supplied.'
            };
        }

        var objs = $(this),
            allImgs = objs.find('img'),
            allImgsLength = allImgs.length,
            allImgsLoaded = 0;

        if (allImgsLength == 0) {
            finishedCallback.call(this);
        }

        return objs.each(function() {
            var obj = $(this);
			var imgs = obj.find('img');

            if (imgs.length == 0) {
                return true;
            }

            imgs.each(function() {
                var image = new Image,
                    imgElement = this;

                image.onload = function() {
                    allImgsLoaded++;
                    eachCallback.call(imgElement, allImgsLoaded, allImgsLength);
                    if (allImgsLoaded == allImgsLength) {
                        finishedCallback.call(obj[0]);
                        return false;
                    }
                }

                image.src = this.src;
            });
        });
    };
})(jQuery);

/*
 * jQuery Tooltip plugin 1.3
 *
 * http://bassistance.de/jquery-plugins/jquery-plugin-tooltip/
 * http://docs.jquery.com/Plugins/Tooltip
 *
 * Copyright (c) 2006 - 2008 JÃƒÂ¶rn Zaefferer
 *
 * $Id: jquery.tooltip.js 5741 2008-06-21 15:22:16Z joern.zaefferer $
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */
;(function($){var helper={},current,title,tID,IE=$.browser.msie&&/MSIE\s(5\.5|6\.)/.test(navigator.userAgent),track=false;$.tooltip={blocked:false,defaults:{delay:200,fade:false,showURL:true,extraClass:"",top:15,left:15,id:"tooltip"},block:function(){$.tooltip.blocked=!$.tooltip.blocked;}};$.fn.extend({tooltip:function(settings){settings=$.extend({},$.tooltip.defaults,settings);createHelper(settings);return this.each(function(){$.data(this,"tooltip",settings);this.tOpacity=helper.parent.css("opacity");this.tooltipText=this.title;/*$(this).data('title', this.title);*/$(this).removeAttr("title");this.alt="";}).mouseover(save).mouseout(hide).click(hide);},fixPNG:IE?function(){return this.each(function(){var image=$(this).css('backgroundImage');if(image.match(/^url\(["']?(.*\.png)["']?\)$/i)){image=RegExp.$1;$(this).css({'backgroundImage':'none','filter':"progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true, sizingMethod=crop, src='"+image+"')"}).each(function(){var position=$(this).css('position');if(position!='absolute'&&position!='relative')$(this).css('position','relative');});}});}:function(){return this;},unfixPNG:IE?function(){return this.each(function(){$(this).css({'filter':'',backgroundImage:''});});}:function(){return this;},hideWhenEmpty:function(){return this.each(function(){$(this)[$(this).html()?"show":"hide"]();});},url:function(){return this.attr('href')||this.attr('src');}});function createHelper(settings){if(helper.parent)return;helper.parent=$('<div id="'+settings.id+'"><h3></h3><div class="body"></div><div class="url"></div></div>').appendTo(document.body).hide();if($.fn.bgiframe)helper.parent.bgiframe();helper.title=$('h3',helper.parent);helper.body=$('div.body',helper.parent);helper.url=$('div.url',helper.parent);}function settings(element){return $.data(element,"tooltip");}function handle(event){if(settings(this).delay)tID=setTimeout(show,settings(this).delay);else
show();track=!!settings(this).track;$(document.body).bind('mousemove',update);update(event);}function save(){if($.tooltip.blocked||this==current||(!this.tooltipText&&!settings(this).bodyHandler))return;current=this;title=this.tooltipText;if(settings(this).bodyHandler){helper.title.hide();var bodyContent=settings(this).bodyHandler.call(this);if((bodyContent.nodeType||bodyContent.jquery)){helper.body.empty().append(bodyContent)}else{helper.body.html(bodyContent);}helper.body.show();}else if(settings(this).showBody){var parts=title.split(settings(this).showBody);helper.title.html(parts.shift()).show();helper.body.empty();for(var i=0,part;(part=parts[i]);i++){if(i>0)helper.body.append("<br/>");helper.body.append(part);}helper.body.hideWhenEmpty();}else{helper.title.html(title).show();helper.body.hide();}if(settings(this).showURL&&$(this).url())helper.url.html($(this).url().replace('http://','')).show();else
helper.url.hide();helper.parent.addClass(settings(this).extraClass);if(settings(this).fixPNG)helper.parent.fixPNG();handle.apply(this,arguments);}function show(){tID=null;if((!IE||!$.fn.bgiframe)&&settings(current).fade){if(helper.parent.is(":animated"))helper.parent.stop().show().fadeTo(settings(current).fade,current.tOpacity);else
helper.parent.is(':visible')?helper.parent.fadeTo(settings(current).fade,current.tOpacity):helper.parent.fadeIn(settings(current).fade);}else{helper.parent.show();}update();}function update(event){if($.tooltip.blocked)return;if(event&&event.target.tagName=="OPTION"){return;}if(!track&&helper.parent.is(":visible")){$(document.body).unbind('mousemove',update)}if(current==null){$(document.body).unbind('mousemove',update);return;}helper.parent.removeClass("viewport-right").removeClass("viewport-bottom");var left=helper.parent[0].offsetLeft;var top=helper.parent[0].offsetTop;if(event){left=event.pageX+settings(current).left;top=event.pageY+settings(current).top;var right='auto';if(settings(current).positionLeft){right=$(window).width()-left;left='auto';}helper.parent.css({left:left,right:right,top:top});}var v=viewport(),h=helper.parent[0];if(v.x+v.cx<h.offsetLeft+h.offsetWidth){left-=h.offsetWidth+20+settings(current).left;helper.parent.css({left:left+'px'}).addClass("viewport-right");}if(v.y+v.cy<h.offsetTop+h.offsetHeight){top-=h.offsetHeight+20+settings(current).top;helper.parent.css({top:top+'px'}).addClass("viewport-bottom");}}function viewport(){return{x:$(window).scrollLeft(),y:$(window).scrollTop(),cx:$(window).width(),cy:$(window).height()};}function hide(event){if($.tooltip.blocked)return;if(tID)clearTimeout(tID);current=null;var tsettings=settings(this);function complete(){helper.parent.removeClass(tsettings.extraClass).hide().css("opacity","");}if((!IE||!$.fn.bgiframe)&&tsettings.fade){if(helper.parent.is(':animated'))helper.parent.stop().fadeTo(tsettings.fade,0,complete);else
helper.parent.stop().fadeOut(tsettings.fade,complete);}else
complete();if(settings(this).fixPNG)helper.parent.unfixPNG();}})(jQuery);

/*
 * SimpleModal 1.4.3 - jQuery Plugin
 * http://simplemodal.com/
 * Copyright (c) 2012 Eric Martin
 * Licensed under MIT and GPL
 * Date: Sat, Sep 8 2012 07:52:31 -0700
 */
(function(b){"function"===typeof define&&define.amd?define(["jquery"],b):b(jQuery)})(function(b){var j=[],l=b(document),m=b.browser.msie&&6===parseInt(b.browser.version)&&"object"!==typeof window.XMLHttpRequest,o=b.browser.msie&&7===parseInt(b.browser.version),n=null,k=b(window),h=[];b.modal=function(a,d){return b.modal.impl.init(a,d)};b.modal.close=function(){b.modal.impl.close()};b.modal.focus=function(a){b.modal.impl.focus(a)};b.modal.setContainerDimensions=function(){b.modal.impl.setContainerDimensions()};
	b.modal.setPosition=function(){b.modal.impl.setPosition()};b.modal.update=function(a,d){b.modal.impl.update(a,d)};b.fn.modal=function(a){return b.modal.impl.init(this,a)};b.modal.defaults={appendTo:"body",focus:!0,opacity:50,overlayId:"simplemodal-overlay",overlayCss:{},containerId:"simplemodal-container",containerCss:{},dataId:"simplemodal-data",dataCss:{},minHeight:null,minWidth:null,maxHeight:null,maxWidth:null,autoResize:!1,autoPosition:!0,zIndex:1E3,close:!0,closeHTML:'<a class="modalCloseImg" title="Close"></a>',
		closeClass:"simplemodal-close",escClose:!0,overlayClose:!1,fixed:!0,position:null,persist:!1,modal:!0,onOpen:null,onShow:null,onClose:null};b.modal.impl={d:{},init:function(a,d){if(this.d.data)return!1;n=b.browser.msie&&!b.support.boxModel;this.o=b.extend({},b.modal.defaults,d);this.zIndex=this.o.zIndex;this.occb=!1;if("object"===typeof a){if(a=a instanceof b?a:b(a),this.d.placeholder=!1,0<a.parent().parent().size()&&(a.before(b("<span></span>").attr("id","simplemodal-placeholder").css({display:"none"})),
		this.d.placeholder=!0,this.display=a.css("display"),!this.o.persist))this.d.orig=a.clone(!0)}else if("string"===typeof a||"number"===typeof a)a=b("<div></div>").html(a);else return alert("SimpleModal Error: Unsupported data type: "+typeof a),this;this.create(a);this.open();b.isFunction(this.o.onShow)&&this.o.onShow.apply(this,[this.d]);return this},create:function(a){this.getDimensions();if(this.o.modal&&m)this.d.iframe=b('<iframe src="javascript:false;"></iframe>').css(b.extend(this.o.iframeCss,
		{display:"none",opacity:0,position:"fixed",height:h[0],width:h[1],zIndex:this.o.zIndex,top:0,left:0})).appendTo(this.o.appendTo);this.d.overlay=b("<div></div>").attr("id",this.o.overlayId).addClass("simplemodal-overlay").css(b.extend(this.o.overlayCss,{display:"none",opacity:this.o.opacity/100,height:this.o.modal?j[0]:0,width:this.o.modal?j[1]:0,position:"fixed",left:0,top:0,zIndex:this.o.zIndex+1})).appendTo(this.o.appendTo);this.d.container=b("<div></div>").attr("id",this.o.containerId).addClass("simplemodal-container").css(b.extend({position:this.o.fixed?
		"fixed":"absolute"},this.o.containerCss,{display:"none",zIndex:this.o.zIndex+2})).append(this.o.close&&this.o.closeHTML?b(this.o.closeHTML).addClass(this.o.closeClass):"").appendTo(this.o.appendTo);this.d.wrap=b("<div></div>").attr("tabIndex",-1).addClass("simplemodal-wrap").css({height:"100%",outline:0,width:"100%"}).appendTo(this.d.container);this.d.data=a.attr("id",a.attr("id")||this.o.dataId).addClass("simplemodal-data").css(b.extend(this.o.dataCss,{display:"none"})).appendTo("body");this.setContainerDimensions();
		this.d.data.appendTo(this.d.wrap);(m||n)&&this.fixIE()},bindEvents:function(){var a=this;b("."+a.o.closeClass).bind("click.simplemodal",function(b){b.preventDefault();a.close()});a.o.modal&&a.o.close&&a.o.overlayClose&&a.d.overlay.bind("click.simplemodal",function(b){b.preventDefault();a.close()});l.bind("keydown.simplemodal",function(b){a.o.modal&&9===b.keyCode?a.watchTab(b):a.o.close&&a.o.escClose&&27===b.keyCode&&(b.preventDefault(),a.close())});k.bind("resize.simplemodal orientationchange.simplemodal",
		function(){a.getDimensions();a.o.autoResize?a.setContainerDimensions():a.o.autoPosition&&a.setPosition();m||n?a.fixIE():a.o.modal&&(a.d.iframe&&a.d.iframe.css({height:h[0],width:h[1]}),a.d.overlay.css({height:j[0],width:j[1]}))})},unbindEvents:function(){b("."+this.o.closeClass).unbind("click.simplemodal");l.unbind("keydown.simplemodal");k.unbind(".simplemodal");this.d.overlay.unbind("click.simplemodal")},fixIE:function(){var a=this.o.position;b.each([this.d.iframe||null,!this.o.modal?null:this.d.overlay,
		"fixed"===this.d.container.css("position")?this.d.container:null],function(b,f){if(f){var g=f[0].style;g.position="absolute";if(2>b)g.removeExpression("height"),g.removeExpression("width"),g.setExpression("height",'document.body.scrollHeight > document.body.clientHeight ? document.body.scrollHeight : document.body.clientHeight + "px"'),g.setExpression("width",'document.body.scrollWidth > document.body.clientWidth ? document.body.scrollWidth : document.body.clientWidth + "px"');else{var c,e;a&&a.constructor===
		Array?(c=a[0]?"number"===typeof a[0]?a[0].toString():a[0].replace(/px/,""):f.css("top").replace(/px/,""),c=-1===c.indexOf("%")?c+' + (t = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"':parseInt(c.replace(/%/,""))+' * ((document.documentElement.clientHeight || document.body.clientHeight) / 100) + (t = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"',a[1]&&(e="number"===typeof a[1]?
		a[1].toString():a[1].replace(/px/,""),e=-1===e.indexOf("%")?e+' + (t = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft) + "px"':parseInt(e.replace(/%/,""))+' * ((document.documentElement.clientWidth || document.body.clientWidth) / 100) + (t = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft) + "px"')):(c='(document.documentElement.clientHeight || document.body.clientHeight) / 2 - (this.offsetHeight / 2) + (t = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"',
		e='(document.documentElement.clientWidth || document.body.clientWidth) / 2 - (this.offsetWidth / 2) + (t = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft) + "px"');g.removeExpression("top");g.removeExpression("left");g.setExpression("top",c);g.setExpression("left",e)}}})},focus:function(a){var d=this,a=a&&-1!==b.inArray(a,["first","last"])?a:"first",f=b(":input:enabled:visible:"+a,d.d.wrap);setTimeout(function(){0<f.length?f.focus():d.d.wrap.focus()},
		10)},getDimensions:function(){var a="undefined"===typeof window.innerHeight?k.height():window.innerHeight;j=[l.height(),l.width()];h=[a,k.width()]},getVal:function(a,b){return a?"number"===typeof a?a:"auto"===a?0:0<a.indexOf("%")?parseInt(a.replace(/%/,""))/100*("h"===b?h[0]:h[1]):parseInt(a.replace(/px/,"")):null},update:function(a,b){if(!this.d.data)return!1;this.d.origHeight=this.getVal(a,"h");this.d.origWidth=this.getVal(b,"w");this.d.data.hide();a&&this.d.container.css("height",a);b&&this.d.container.css("width",
		b);this.setContainerDimensions();this.d.data.show();this.o.focus&&this.focus();this.unbindEvents();this.bindEvents()},setContainerDimensions:function(){var a=m||o,d=this.d.origHeight?this.d.origHeight:b.browser.opera?this.d.container.height():this.getVal(a?this.d.container[0].currentStyle.height:this.d.container.css("height"),"h"),a=this.d.origWidth?this.d.origWidth:b.browser.opera?this.d.container.width():this.getVal(a?this.d.container[0].currentStyle.width:this.d.container.css("width"),"w"),f=this.d.data.outerHeight(!0),
		g=this.d.data.outerWidth(!0);this.d.origHeight=this.d.origHeight||d;this.d.origWidth=this.d.origWidth||a;var c=this.o.maxHeight?this.getVal(this.o.maxHeight,"h"):null,e=this.o.maxWidth?this.getVal(this.o.maxWidth,"w"):null,c=c&&c<h[0]?c:h[0],e=e&&e<h[1]?e:h[1],i=this.o.minHeight?this.getVal(this.o.minHeight,"h"):"auto",d=d?this.o.autoResize&&d>c?c:d<i?i:d:f?f>c?c:this.o.minHeight&&"auto"!==i&&f<i?i:f:i,c=this.o.minWidth?this.getVal(this.o.minWidth,"w"):"auto",a=a?this.o.autoResize&&a>e?e:a<c?c:a:
		g?g>e?e:this.o.minWidth&&"auto"!==c&&g<c?c:g:c;this.d.container.css({height:d,width:a});this.d.wrap.css({overflow:f>d||g>a?"auto":"visible"});this.o.autoPosition&&this.setPosition()},setPosition:function(){var a,b;a=h[0]/2-this.d.container.outerHeight(!0)/2;b=h[1]/2-this.d.container.outerWidth(!0)/2;var f="fixed"!==this.d.container.css("position")?k.scrollTop():0;this.o.position&&"[object Array]"===Object.prototype.toString.call(this.o.position)?(a=f+(this.o.position[0]||a),b=this.o.position[1]||
		b):a=f+a;this.d.container.css({left:b,top:a})},watchTab:function(a){if(0<b(a.target).parents(".simplemodal-container").length){if(this.inputs=b(":input:enabled:visible:first, :input:enabled:visible:last",this.d.data[0]),!a.shiftKey&&a.target===this.inputs[this.inputs.length-1]||a.shiftKey&&a.target===this.inputs[0]||0===this.inputs.length)a.preventDefault(),this.focus(a.shiftKey?"last":"first")}else a.preventDefault(),this.focus()},open:function(){this.d.iframe&&this.d.iframe.show();b.isFunction(this.o.onOpen)?
		this.o.onOpen.apply(this,[this.d]):(this.d.overlay.show(),this.d.container.show(),this.d.data.show());this.o.focus&&this.focus();this.bindEvents()},close:function(){if(!this.d.data)return!1;this.unbindEvents();if(b.isFunction(this.o.onClose)&&!this.occb)this.occb=!0,this.o.onClose.apply(this,[this.d]);else{if(this.d.placeholder){var a=b("#simplemodal-placeholder");this.o.persist?a.replaceWith(this.d.data.removeClass("simplemodal-data").css("display",this.display)):(this.d.data.hide().remove(),a.replaceWith(this.d.orig))}else this.d.data.hide().remove();
		this.d.container.hide().remove();this.d.overlay.hide();this.d.iframe&&this.d.iframe.hide().remove();this.d.overlay.remove();this.d={}}}}});

/**
 * photonic.js - Contains all custom JavaScript functions required by Photonic
 */
$j = jQuery.noConflict();

function photonicHtmlEncode(value){
	return $j('<div/>').text(value).html();
}

function photonicHtmlDecode(value){
	return $j('<div/>').html(value).text();
}

function photonicFormatFancyBoxTitle(title, currentArray, currentIndex, currentOpts) {
	if (typeof $j(currentArray[currentIndex]).data('title') != 'undefined' && $j(currentArray[currentIndex]).data('title') != '') {
		return $j(currentArray[currentIndex]).data('title');
	}
	return title;
}

function photonicLightBoxTitle(obj) {
	if (typeof obj.title != 'undefined' && obj.title != '') {
		return obj.title;
	}
	return $j(obj).data('title');
}

$j(document).ready(function() {
	// JQuery Cycle stops if there is only one image in it. The following snippet fixes the issue.
	$j('#sliderContent, .sliderContent').each(function() {
		if ($j(this).children().length == 1) {
			var single = this.firstChild;
			$j(single).show();
		}
	});

	if (Photonic_JS.slideshow_library == 'fancybox' && Photonic_JS.slideshow_mode) {
		setInterval($j.fancybox.next, parseInt(Photonic_JS.slideshow_interval, 10));
	}

	if (Photonic_JS.slideshow_library == 'fancybox') {
		$j('a.launch-gallery-fancybox').each(function() {
			$j(this).fancybox({
				transitionIn	:	'elastic',
				transitionOut	:	'elastic',
				speedIn			:	600,
				speedOut		:	200,
				overlayShow		:	true,
				overlayColor	:	'#000',
				overlayOpacity	: 0.8,
				titleShow		: Photonic_JS.fbox_show_title,
				titleFormat		: photonicFormatFancyBoxTitle,
				titlePosition	: Photonic_JS.fbox_title_position
			});
		});
	}
	else if (Photonic_JS.slideshow_library == 'fancybox2' && $j.fancybox) {
		$j('a.launch-gallery-fancybox').fancybox({
			autoPlay: Photonic_JS.slideshow_mode,
			playSpeed: parseInt(Photonic_JS.slideshow_interval, 10),
			beforeLoad: function() {
				if (Photonic_JS.fbox_show_title) {
					this.title = $j(this.element).data('title');
				}
			},
			helpers: {
				title: {
					type: Photonic_JS.fbox_title_position
				}
			}
		});
	}

	if ($j.prettyPhoto) {
		$j("a[rel^='photonic-prettyPhoto']").prettyPhoto({
			theme: Photonic_JS.pphoto_theme,
			autoplay_slideshow: Photonic_JS.slideshow_mode,
			slideshow: Photonic_JS.slideshow_interval,
			show_title: false,
			social_tools: '',
			deeplinking: false
		});
	}

	$j('a.launch-gallery-colorbox').each(function() {
		$j(this).colorbox({
			opacity: 0.8,
			maxWidth: '95%',
			maxHeight: '95%',
			title: photonicLightBoxTitle(this),
			slideshow: Photonic_JS.slideshow_mode,
			slideshowSpeed: Photonic_JS.slideshow_interval
		});
	});

	if ($j.fn.piroBox_ext) {
		$j().piroBox_ext({
			//
		});
	}

	$j('.photonic-flickr-set-thumb').on('click', function() {
		photonicDisplayPopup(this, 'flickr', 'set');
		return false;
	});

	$j('.photonic-flickr-gallery-thumb').on('click', function() {
		photonicDisplayPopup(this, 'flickr', 'gallery');
		return false;
	});

	$j('.photonic-picasa-album-thumb').on('click', function(e) {
		var thumb_id = this.id;
		var href = this.href;
		var classes = this.className;
		classes = classes.split(' ');
		var panel_id = thumb_id.substr(28);
		var panel = '#photonic-picasa-panel-' + panel_id;

		var loading = document.createElement('div');
		loading.className = 'photonic-loading';
		$j(loading).appendTo($j('body')).show();

		var thumb_size = 75;
		for (var i=0; i<classes.length; i++) {
			if (classes[i].indexOf('photonic-picasa-album-thumb-') > -1) {
				thumb_size = classes[i].substr(28);
			}
		}

		if ($j(panel).length == 0) {
			$j.post(Photonic_JS.ajaxurl, "action=photonic_picasa_display_album&panel_id=" + thumb_id + "&href=" + href + '&thumb_size=' + thumb_size, function(data) {
				photonicDisplayPopupContent(data, 'picasa', 'album', panel_id);
			});
		}
		else {
			photonicRedisplayPopupContents('picasa', 'album', panel_id, panel, 'photonic-picasa-panel-container-' + panel_id);
		}

		return false;
	});

	$j('a.photonic-smug-album-thumb').on('click', function(e) {
		if ($j(this).hasClass('photonic-smug-passworded')) {
			return false;
		}

		var thumb_id = this.id;
		var href = this.href;
		var panel_id = thumb_id.substr(26);
		var panel = '#photonic-smug-panel-' + panel_id;

		var loading = document.createElement('div');
		loading.className = 'photonic-loading';
		$j(loading).appendTo($j('body')).show();

		if ($j(panel).length == 0) {
			$j.post(Photonic_JS.ajaxurl, "action=photonic_smug_display_album&panel_id=" + thumb_id + "&href=" + href, function(data) {
				photonicDisplayPopupContent(data, 'smug', 'album', panel_id);
			});
		}
		else {
			photonicRedisplayPopupContents('smug', 'album', panel_id, panel, 'photonic-smug-panel-container-' + panel_id);
		}

		return false;
	});

	$j('.photonic-zenfolio-set-thumb').on('click', function(e) {
		var thumb_id = this.id;
		var href = this.href;
		var panel_id = thumb_id.substr(28);

		if ($j(this).hasClass('photonic-zenfolio-set-passworded')) {
//			$j('#photonic-zenfolio-prompter-' + panel_id).dialog('open');
			return false;
		}

		var classes = this.className;
		classes = classes.split(' ');
		var panel = '#photonic-zenfolio-panel-' + panel_id;

		var loading = document.createElement('div');
		loading.className = 'photonic-loading';
		$j(loading).appendTo($j('body')).show();

		var thumb_size = 1;
		for (var i=0; i<classes.length; i++) {
			if (classes[i].indexOf('photonic-zenfolio-set-thumb-') > -1) {
				thumb_size = classes[i].substr(28);
				break;
			}
		}

		if ($j(panel).length == 0) {
			$j.post(Photonic_JS.ajaxurl, "action=photonic_zenfolio_display_set&panel_id=" + thumb_id + "&href=" + href + '&thumb_size=' + thumb_size, function(data) {
				photonicDisplayPopupContent(data, 'zenfolio', 'set', panel_id);
			});
		}
		else {
			photonicRedisplayPopupContents('zenfolio', 'set', panel_id, panel, 'photonic-zenfolio-panel-container-' + panel_id);
		}

		return false;
	});

	$j('a.modalCloseImg').on('click', function() {
		var thisClass = this.className;
		thisClass = thisClass.substr(14);
		$j('#' + thisClass).hide();
	});

	$j('.photonic-flickr-stream a, a.photonic-flickr-set-thumb, a.photonic-flickr-gallery-thumb, .photonic-picasa-stream a, .photonic-post-gallery-nav a, .photonic-500px-stream a, .photonic-smug-stream a, .photonic-instagram-stream a, .photonic-zenfolio-stream a, a.photonic-zenfolio-set-thumb').each(function() {
		if (!($j(this).parent().hasClass('photonic-header-title'))) {
			$j(this).data('title', $j(this).attr('title'));
			var tempTitle = $j(this).data('title');
			if (typeof tempTitle != 'undefined' && tempTitle != '') {
				var strippedTitle = $j('<div/>').html(tempTitle).text();
				$j(this).attr('title', strippedTitle);
			}
		}
	});

	if (Photonic_JS.flickr_photo_title_display == 'tooltip' || Photonic_JS.flickr_collection_set_title_display == 'tooltip' || Photonic_JS.flickr_gallery_title_display == 'tooltip' ||
		Photonic_JS.picasa_photo_title_display == 'tooltip' || Photonic_JS.picasa_photo_pop_title_display == 'tooltip' ||
		Photonic_JS.wp_thumbnail_title_display == 'tooltip' ||
		Photonic_JS.Dpx_photo_title_display == 'tooltip' ||
		Photonic_JS.instagram_photo_title_display == 'tooltip' || Photonic_JS.instagram_user_title_display == 'tooltip' ||
		Photonic_JS.zenfolio_photo_title_display == 'tooltip' ||
		Photonic_JS.smug_photo_title_display == 'tooltip' || Photonic_JS.smug_photo_pop_title_display == 'tooltip' || Photonic_JS.smug_albums_album_title_display == 'tooltip'
		) {
		var tooltipObj = Photonic_JS.flickr_photo_title_display == 'tooltip' ? '.photonic-flickr-stream .photonic-flickr-photo a' : '';
		tooltipObj += (tooltipObj != '' && Photonic_JS.flickr_collection_set_title_display == 'tooltip') ? ',' : '';
		tooltipObj += Photonic_JS.flickr_collection_set_title_display == 'tooltip' ? 'a.photonic-flickr-set-thumb' : '';
		tooltipObj += (tooltipObj != '' && Photonic_JS.flickr_gallery_title_display == 'tooltip') ? ',' : '';
		tooltipObj += Photonic_JS.flickr_gallery_title_display == 'tooltip' ? 'a.photonic-flickr-gallery-thumb' : '';
		tooltipObj += (tooltipObj != '' && Photonic_JS.picasa_photo_title_display == 'tooltip') ? ',' : '';
		tooltipObj += Photonic_JS.picasa_photo_title_display == 'tooltip' ? '.photonic-picasa-stream a' : '';
		tooltipObj += (tooltipObj != '' && Photonic_JS.picasa_photo_pop_title_display == 'tooltip') ? ',' : '';
		tooltipObj += Photonic_JS.picasa_photo_pop_title_display == 'tooltip' ? '.photonic-picasa-panel a' : '';
		tooltipObj += (tooltipObj != '' && Photonic_JS.wp_thumbnail_title_display == 'tooltip') ? ',' : '';
		tooltipObj += Photonic_JS.wp_thumbnail_title_display == 'tooltip' ? '.photonic-post-gallery-nav a' : '';
		tooltipObj += (tooltipObj != '' && Photonic_JS.Dpx_photo_title_display == 'tooltip') ? ',' : '';
		tooltipObj += Photonic_JS.Dpx_photo_title_display == 'tooltip' ? '.photonic-500px-stream a' : '';
		tooltipObj += (tooltipObj != '' && Photonic_JS.smug_photo_title_display == 'tooltip') ? ',' : '';
		tooltipObj += Photonic_JS.smug_photo_title_display == 'tooltip' ? '.photonic-smug-stream a' : '';
		tooltipObj += (tooltipObj != '' && Photonic_JS.smug_photo_pop_title_display == 'tooltip') ? ',' : '';
		tooltipObj += Photonic_JS.smug_photo_pop_title_display == 'tooltip' ? '.photonic-smug-panel a' : '';
		tooltipObj += (tooltipObj != '' && Photonic_JS.smug_albums_album_title_display == 'tooltip') ? ',' : '';
		tooltipObj += Photonic_JS.smug_albums_album_title_display == 'tooltip' ? '.photonic-smug-album-thumb a' : '';
		tooltipObj += (tooltipObj != '' && Photonic_JS.instagram_photo_title_display == 'tooltip') ? ',' : '';
		tooltipObj += Photonic_JS.instagram_photo_title_display == 'tooltip' ? '.photonic-instagram-photo a' : '';
		tooltipObj += (tooltipObj != '' && Photonic_JS.instagram_user_title_display == 'tooltip') ? ',' : '';
		tooltipObj += Photonic_JS.instagram_user_title_display == 'tooltip' ? '.photonic-instagram-user a' : '';
		tooltipObj += (tooltipObj != '' && Photonic_JS.zenfolio_photo_title_display == 'tooltip') ? ',' : '';
		tooltipObj += Photonic_JS.zenfolio_photo_title_display == 'tooltip' ? '.photonic-zenfolio-photo a' : '';
		tooltipObj += (tooltipObj != '' && Photonic_JS.zenfolio_set_title_display == 'tooltip') ? ',' : '';
		tooltipObj += Photonic_JS.zenfolio_set_title_display == 'tooltip' ? 'a.photonic-zenfolio-set-thumb' : '';

		$j(tooltipObj).each(function() {
			if (!($j(this).parent().hasClass('photonic-header-title'))) {
				var iTitle = $j(this).find('img').attr('alt');
				if (iTitle != '') {
					$j(this).tooltip({
						bodyHandler: function() {
							return iTitle;
						},
						showURL: false
					});
				}
			}
		});
	}

	if ($j.jcarousel) {
		$j('.photonic-carousel').jcarousel({
			// Configuration goes here
		});
	}

	$j('.auth-button').not('.auth-button-picasa, .auth-button-instagram').click(function (){
		var provider = '';
		if ($j(this).hasClass('auth-button-flickr')) {
			provider = 'flickr';
		}
		else if ($j(this).hasClass('auth-button-500px')) {
			provider = '500px';
		}
		else if ($j(this).hasClass('auth-button-smug')) {
			provider = 'smug';
		}
		var callbackId = $j(this).attr('rel');

		$j.post(Photonic_JS.ajaxurl, "action=photonic_authenticate&provider=" + provider + '&callback_id=' + callbackId, function(data) {
			if (provider == 'flickr') {
				window.location.replace(data);
			}
			else if (provider == '500px') {
				window.location.replace(data);
			}
			else if (provider == 'smug') {
				window.open(data);
			}
		});
		return false;
	});

	$j('.photonic-login-box-flickr:not(:first)').remove();
	$j('.photonic-login-box-flickr').attr({id: 'photonic-login-box-flickr'});
	$j('.photonic-login-box-picasa:not(:first)').remove();
	$j('.photonic-login-box-picasa').attr({id: 'photonic-login-box-picasa'});
	$j('.photonic-login-box-500px:not(:first)').remove();
	$j('.photonic-login-box-500px').attr({id: 'photonic-login-box-500px'});
	$j('.photonic-login-box-smugmug:not(:first)').remove();
	$j('.photonic-login-box-smugmug').attr({id: 'photonic-login-box-smugmug'});
	$j('.photonic-login-box-zenfolio:not(:first)').remove();
	$j('.photonic-login-box-zenfolio').attr({id: 'photonic-login-box-zenfolio'});
	$j('.photonic-login-box-instagram:not(:first)').remove();
	$j('.photonic-login-box-instagram').attr({id: 'photonic-login-box-instagram'});
/*
	$j('.photonic-password-prompter').dialog({
		autoOpen: false,
		height:	160,
		width: 300,
		modal: true,
		dialogClass: 'photonic-jq'
	});

	$j('.photonic-password-form').ajaxForm({
		dataType: 'json'
	});
*/

	// callback function
	function modalOpen(dialog) {
		dialog.overlay.fadeIn(200, function () {
			dialog.data.hide();
			dialog.container.fadeIn(100, function () {
				dialog.data.fadeIn(100);
				var panel = dialog.data.attr('id');
				$j('.slideshow-grid-panel').cycle({timeout: 0, prev: 'a#' + panel + '-prev', next: 'a#' + panel + '-next'});
			});
		});
	}

	// callback function
	function modalClose(dialog) {
		dialog.data.fadeOut(100, function () {
			dialog.container.fadeOut(100, function () {
				dialog.overlay.fadeOut(200, function () {
					$j.modal.close();
				});
			});
		});
	}

	function photonicDisplayPopup(thumb, provider, popup) {
		var thumbId = thumb.id;
		var baseStr = 'photonic-' + provider + '-' + popup + '-thumb-';
		var baseLen = baseStr.length;
		var panelId = thumbId.substr(baseLen);

		var containerId = 'photonic-' + provider + '-panel-container-' + panelId;
		var panel = '#photonic-' + provider + '-panel-' + panelId;

		var loading = document.createElement('div');
		loading.className = 'photonic-loading';
		$j(loading).appendTo($j('body')).show();

		if ($j(panel).length == 0) {
			var object_id = panelId.substr(panelId.lastIndexOf('-') + 1);
			var method;
			if (provider == 'flickr' && popup == 'gallery') {
				var remainder = panelId.substr(0, panelId.lastIndexOf('-'));
				remainder = remainder.substr(remainder.lastIndexOf('-') + 1);
				object_id = remainder + '-' + object_id;
				method = 'flickr.galleries.getPhotos'
			}
			else if (provider == 'flickr' && popup == 'set') {
				method = 'flickr.photosets.getPhotos';
			}

			$j.post(Photonic_JS.ajaxurl, "action=photonic_flickr_display_photos&method=" + method + "&object_id=" + object_id + '&panel_id=' + panelId, function(data) {
				photonicDisplayPopupContent(data, provider, popup, panelId);
			});
		}
		else {
			photonicRedisplayPopupContents(provider, popup, panelId, panel, containerId);
		}

		return false;
	}

	/**
	 * Displays all photos in a popup. Invoked when the popup data is being fetched for the first time for display in a popup.
	 * Must be used by all providers for displaying photos in a popup.
	 *
	 * @param data The contents of the popup
	 * @param provider The data provider: flickr | picasa | smug | zenfolio
	 * @param popup The type of popup object: set | gallery | album
	 * @param panelId The trailing section of the thumbnail's id
	 */
	function photonicDisplayPopupContent(data, provider, popup, panelId) {
		var div = $j(data);
		var ul = div.find('ul');
		var screens = $j(ul).children('li').length;
		var prev = document.createElement('a');
		prev.id = 'photonic-' + provider + '-' + popup + '-' + panelId + '-prev';
		prev.href = '#';
		prev.className = 'panel-previous';
		prev.innerHTML = '&nbsp;';

		var next = document.createElement('a');
		next.id = 'photonic-' + provider + '-' + popup + '-' + panelId + '-next';
		next.href = '#';
		next.className = 'panel-next';
		next.innerHTML = '&nbsp;';

		$j(ul).first('li').waitForImages(function() {
			$j(div).appendTo($j('#photonic-' + provider + '-' + popup + '-' + panelId)).show();
			if (screens > 1) {
				$j(this).before(prev)
					.after(next)
					.cycle({
						timeout: 0,
						slideResize: false,
						prev: 'a#photonic-' + provider + '-' + popup + '-' + panelId + '-prev',
						next: 'a#photonic-' + provider + '-' + popup + '-' + panelId + '-next',
						sync: false
					});
			}
			else {
				$j(this).cycle({
					timeout: 0,
					slideResize: false,
					sync: false
				});
			}

			$j('#photonic-' + provider + '-panel-' + panelId).modal({
				autoPosition: false,
				dataCss: { width: '' + Photonic_JS.gallery_panel_width + 'px' },
				overlayCss: { background: '#000' },
				closeClass: 'photonic-' + provider + '-panel-' + panelId,
				opacity: 90,
				close: true,
				escClose: false,
				containerId: 'photonic-' + provider + '-panel-container-' + panelId,
				onClose: function(dialog) { $j.modal.close(); $j('#photonic-' + provider + '-panel-' + panelId).css({ display: 'none' }) },
				onOpen: modalOpen
			});
			var viewport = [$j(window).width(), $j(window).height(), $j(document).scrollLeft(), $j(document).scrollTop()];
			var target = {};

			target.top = parseInt(Math.max(viewport[3] - 20, viewport[3] + ((viewport[1] - $j('#photonic-' + provider + '-panel-container-' + panelId).height() - 40) * 0.5)), 10);
			target.left = parseInt(Math.max(viewport[2] - 20, viewport[2] + ((viewport[0] - $j('#photonic-' + provider + '-panel-container-' + panelId).width() - 40) * 0.5)), 10);

			$j('#photonic-' + provider + '-panel-container-' + panelId).css({top: target.top, left: target.left });
			$j('.photonic-loading').hide();
		});
	}

	function photonicRedisplayPopupContents(provider, popup, panelId, panel, containerId) {
		$j('.photonic-loading').hide();
		$j(panel).modal({
			autoPosition: false,
			dataCss: { width: '' + Photonic_JS.gallery_panel_width + 'px' },
			overlayCss: { background: '#000' },
			opacity: 90,
			close: true,
			escClose: false,
			containerId: containerId,
			onClose: modalClose
		});
		var viewport = [$j(window).width(), $j(window).height(), $j(document).scrollLeft(), $j(document).scrollTop()];
		var target = {};
		target.top = parseInt(Math.max(viewport[3] - 20, viewport[3] + ((viewport[1] - $j('#photonic-' + provider + '-panel-' + panelId).height() - 40) * 0.5)), 10);
		target.left = parseInt(Math.max(viewport[2] - 20, viewport[2] + ((viewport[0] - $j('#photonic-' + provider + '-panel-' + panelId).width() - 40) * 0.5)), 10);
		$j('#' + containerId).css({top: target.top, left: target.left});
		$j('.slideshow-grid-panel').cycle({timeout: 0, prev: 'a#photonic-' + provider + '-' + popup + '-' + panelId + '-prev', next: 'a#photonic-' + provider + '-' + popup + '-' + panelId + '-next'});
	}
});

