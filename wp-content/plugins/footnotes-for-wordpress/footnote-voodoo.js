// Fair Use Repository Footnote Voodoo
// Version 2010.0303
// 
// Copyright (c) 2005-2010, Charles Johnson
// Parts derived from WikiPedia Inline Article Viewer by Brent Charbonneau
// Released under the GPL license
// http://www.gnu.org/copyleft/gpl.html
//
// Trying to render footnotes in pure HTML sucks. Unfortunately, the texts on
// Fair Use tend to feature a lot of footnotes. To make things a bit prettier,
// without mucking up the XHTML, we use some canonical formatting and some
// JavaScript magic to make footnotes pop up in pretty little windows if your
// browser supports it. (If your browser doesn't support it, then you get sent
// to the second-best place to go through an ordinary hyperlink.)
//
// Due to limitations with Internet Exploder's XML DOM parser, the script
// currently does not correctly handle footnotes that are (1) located at a
// remote URI, with (2) a DOCTYPE set on the document. Sorry. I may be able
// to work out this bug some day. But Internet Exploder's documentation isn't
// helping me any in my efforts.

var icon = document.createElement('span');
icon.className = "footnote-indicator";

var inlineWindowCount = 0;

////////////////////////////////////////////////////////////////////////////////
// Kludge-tastic: cross-browser compatibility. Sort of. ////////////////////////
////////////////////////////////////////////////////////////////////////////////

// Ecumenical event listener. Thanks to Scott Andrew LePera
function add_event (obj, evType, fn, useCapture) {
	if (obj.addEventListener){
		obj.addEventListener(evType, fn, useCapture);
		return true;
	} else if (obj.attachEvent){
		var r = obj.attachEvent("on"+evType, fn);
		return r;
	} else {
		return false;
	}
}

// Ecumenical XMLHttpRequest object constructor
function get_xmlhttp () {
	var xmlhttp=false;
	/*@cc_on @*/
	/*@if (@_jscript_version >= 5)
	// JScript gives us Conditional compilation, we can cope with old IE versions.
	// and security blocked creation of the objects.
	 try {
	  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	 } catch (e) {
	  try {
	   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	  } catch (E) {
	   xmlhttp = false;
	  }
	 }
	@end @*/
	if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
	  xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}

// Ecumenical DOM Parser
function xml_to_DOM (xml) {
	var ret = false;
	if (document.implementation.createDocument){ 
		// Mozilla, create a new DOMParser 
		var parser = new DOMParser(); 
		ret = parser.parseFromString(xml, "text/xml"); 
	} else if (window.ActiveXObject) { 
		// Internet Explorer, create a new XML document using ActiveX 
		// and use loadXML as a DOM parser. 
		ret = new ActiveXObject("Microsoft.XMLDOM");
		ret.async="false";
		ret.loadXML(xml);
	} /* if */
	return ret;
}
 
////////////////////////////////////////////////////////////////////////////////
// Functionality ///////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

function rewriteLinks() {
	// This function grabs all the links within the page's #content div and 
	// sends them off to be modified
	
	var links, currentLink;
	
	// Cycle through the links to find the footnoted text.
	links = document.getElementsByTagName('a');
	
	for (var i = 0; i < links.length; i++) {
		currentLink = links[i];

		// do something with thisLink
		if ( currentLink.className=='footnoted'
		&& currentLink.getAttribute('inlineviewappended') != 'true' 
		&& currentLink.getAttribute('inlineviewlink') != 'true'
		&& currentLink.className != 'image' ) {
			appendInlineButton(currentLink);
		}
	}
}

function appendInlineButton(link) {
	// Create the new element to hold all that we're doing...
	var container = document.createElement('span');

	// This will prevent the view inline icon from breaking away from the
	// last word of the article link
	container.style.whiteSpace = 'nowrap';

	// We want the link to wrap normally though, and only hold the new icon
	// to the last word so we should explicitly set normal wrap on the link
	// element
	link.style.whiteSpace = 'normal';
	
	// Add a new anchor inside the new button
	// We use innerHTML because it's quicker than writing out the style
	// properties one by one with javascript

	var linkTitle = (link.title ? 'title="' + link.title + '" ' : '');
	container.innerHTML = '<a ' + linkTitle + 'href="' + link.href + '" inlineviewlink="true" style="text-decoration: none; margin-left: 0.3em;"></a>';
	
	// Pull out our innerHTML generated anchor element...
	var newLink = container.firstChild;

	// Add a click event listener to our links
	// this event handler is where the inline page will be fetched
	add_event(newLink, 'click', inlineViewClickHandler, true);
	add_event(link, 'click', inlineViewClickHandler, true); 

	//newLink.appendChild(icon.cloneNode(false));

	// Insert the new container before the current link...
	//link.parentNode.insertBefore(container,link);
	
	// Now move the link inside the container before the new link
	// (FYI: this dance here is for the whitepace break stuff we implemented
	// earlier)
	//container.insertBefore(link, newLink);

	// Experienced some weird multiple-appendings... this was added
	// to attempt to stop this behavior...
	link.setAttribute('inlineviewappended','true');
	return true;
}

function inlineViewClickHandler(anEvent) {
	// Kludge-tastic
	var event = (anEvent ? anEvent : window.event);
	var target;
	
	if (event.currentTarget) {
		target = event.currentTarget;
	} else if (event.target) {
		target = event.target;
	} else if (event.srcElement) { // fucking Internet Explorer
		target = event.srcElement;
	}

	// Event handler which takes clicks from our injected inline view icon links...
	var href, link;

	// Trace back up the hierarchy in case we are in a child node
	while (target && target.tagName.toLowerCase() !== 'a' && target.tagName.toLowerCase() !== 'img') {
		target = target.parentNode;
	}

	// Create the new dhtml frame
	if ( target.tagName.toLowerCase() == 'a' ) {
		// user managed to click the actual anchor and not the icon
		href = target.href;
		link = target;
	} else if ( target.tagName.toLowerCase() == 'img' ) { 
		// user clicked the icon
		href = target.parentNode.href;
		link = target.parentNode;
	} else {
		// No idea how this function was launched, so we cannot proceed.
		return false;
	}
	inlineWindow = newInlineWindow(event, href, link);

	if (event.preventDefault) {
		event.preventDefault();
	} else {
		event.returnValue = false;
	}
	return true;
}


function newInlineWindow(event, href, link){
	// Close all previous inline windows...
	closeInlineWindows();
	
	// Setup some constants for use in creating the inline window...
	var browserWidth;
	if (document.width) {
		browserWidth = window.innerWidth;
	} else if (document.body.clientWidth) {
		browserWidth = document.body.clientWidth;
	} else {
		browserWidth = 640;
	}

	var windowWidth = Math.round(browserWidth * 0.60);
	var windowHeight = Math.round(window.innerHeight * 0.45);
	var windowPadding = 14;
	var windowTextPadding = 5;
	var windowFontSize = 10;
	var windowBorderSize = 1;
	var windowButtonHeight = 11;
	var windowButtonTextSize = 12;
	var windowID = inlineWindowCount++;
	var windowFullID = 'inlineWindow-' + windowID;
	
	
	var noteTitle = (link.title ? link.title : 'textual note');
	
	if (noteTitle.indexOf(':') > -1) { // Split off note gloss
		noteTitle = noteTitle.substring(0, noteTitle.indexOf(':'));
	}

	// stop the window before getting this close to the left/right/top/bottom of the screen
	var pageBoundPadding = 10;
	
	// where we first declare the xpos and ypos variables
	var xpos, ypos;
	
	// get the position of the element that was clicked on...
	var elementTop = link.offsetTop;
	var elementLeft = link.offsetLeft;
	var elementWidth = link.offsetWidth;
	var elementHeight = link.offsetHeight;
	
	// setup the x-position of the inline window...
	// check to see if the left 1/3 of the window will overlap the left page bound..
	/* if ( elementLeft - (windowWidth/3) < pageBoundPadding ) {
		xpos = pageBoundPadding;
	} 
	// check to see if the right 2/3 of the window will overlap the right page bound...
	else if ( elementLeft + (windowWidth*2/3) > document.width - pageBoundPadding ) {
		xpos = document.width - pageBoundPadding - windowWidth;
	}
	else {
		// if we're not going to hit either wall, set the window to be offset
		// by 1/3 to the left of where we clicked (looks better than centering
		xpos = elementLeft - (windowWidth/3);
	} */
	
	xpos = elementLeft + elementWidth;
	
	// setup the y-positioning of the inline window aka the yellow box...
	ypos = elementTop + 25;

	var container = document.createElement('div');
	container.id = windowFullID;
	
	var cssBoxWidth = Math.round((windowWidth - (windowPadding+windowBorderSize)*2)/browserWidth*100);
	var cssBoxHeight = windowHeight - (windowPadding+windowBorderSize*2);

	container.innerHTML = '<div id="outerWindowCont-' + windowID + '" ' + 
		'class="float-a-note" style="' +
		'top: ' + ypos + 'px;' +
		'margin: 0;' +
		'padding: ' + Math.round((windowPadding-windowButtonHeight)/2) +'px ' + windowPadding + 'px ' + windowPadding + 'px; ' +
		'max-height: ' + cssBoxHeight + 'px; ' +
		'border: ' + windowBorderSize + 'px solid #eee; '+
		'font-size: ' + windowFontSize + 'pt; '+
		'">'+
			'<div id="innerWindowCont-' + windowID + '" class="footnote" style="'+
				'max-height: ' + (cssBoxHeight-(windowPadding*2)) + 'px; ' +
				'padding: ' + windowTextPadding + 'px; '+
			'">loading<span style="text-decoration: blink">...</span></div>'+
			'<div class="note-label" style="'+
				'float: right; '+
				'margin: 0 3px ' + Math.round((windowPadding-windowButtonHeight)/2) +'px; '+
				'padding: 0 3px; '+
				'height: ' + windowButtonHeight + 'px; '+
				'font-size: ' + windowButtonTextSize + 'px; '+
				'line-height: ' + windowButtonTextSize + 'px; '+
			'"><a href="#" onClick="closeInlineWindow(' + windowID + '); return false;" style="text-decoration: none; border: none">close</a></div>'+
			'<div class="note-label" style="'+
				'float: right; '+
				'margin: 0 3px ' + Math.round((windowPadding-windowButtonHeight)/2) +'px; '+
				'padding: 0 3px; '+
				'height: ' + windowButtonHeight + 'px; '+
				'font-size: ' + windowButtonTextSize + 'px; '+
				'line-height: ' + windowButtonTextSize + 'px; '+
			'"><a href="' + href + '" style="text-decoration: none; border: none;" onClick="closeInlineWindow(' + windowID + '); return true;">jump to footnote</a></div>'+	
		'</div>'+
		'<div id="outerWindowTip-'+windowID+'" style="position: absolute;'+
			'left: '+(elementLeft-5)+'px;'+
			'top: '+(ypos-9)+'px;'+
			'height: 10px;'+
			'width: 17px;'+
			'background: transparent '+tipDownUrl+' 0px 0px no-repeat;'+
			'z-index: 10000;'+
		'"></div>';
	document.getElementById('article').insertBefore(container, document.getElementById('article').firstChild);

	populateInnerWindow(href, link, windowID);

	if (typeof(jQuery) != 'undefined') {
		jQuery('#'+windowFullID).css('display', 'none');
		jQuery('#'+windowFullID).fadeIn('slow');		
	}
}

function arrangeWindowVertically (link, windowID) {
	var outerWindowContentBox = document.getElementById('outerWindowCont-' + windowID);
	var outerWindowTipBox = document.getElementById('outerWindowTip-' + windowID);

	// stop the window before getting this close to the bottom of the screen
	var pageBoundPadding = 10;

	var ypos;
	
	// get the position of the element that was clicked on...
	var elementTop = getElementOffset(link,'Top');
	var elementBottom = getElementOffset(link, 'Bottom');

	// check to see if the window goes beyond the bottom of the viewport...
	var boxBottom = elementTop;
	var windowBottom = window.pageYOffset + window.innerHeight;
	
	// below the fold; bring 'er up
	if ( boxBottom > windowBottom ) {
		//outerWindowContentBox.style.top = 'auto';
		//outerWindowContentBox.style.bottom = elementBottom + 'px';
		ypos = elementTop - outerWindowContentBox.offsetHeight - 9;
		outerWindowContentBox.style.top = ypos + 'px';
		
		outerWindowTipBox.style.top = (elementTop - 10) + 'px';
		outerWindowTipBox.style.backgroundImage = tipUpUrl;	}
}

function populateInnerWindow(href, link, windowID) {
	var innerWindowContentBox = document.getElementById('innerWindowCont-' + windowID);

	var footnoteId = '';
	
	// Normalize because Internet Exploder fucking sucks
	lp = link.pathname; dlp = document.location.pathname;
	if (lp.substr(0, 1) != '/') { lp = '/' + lp; }
	if (dlp.substr(0, 1) != '/') { dlp = '/' + dlp; }
	if (link.hash && lp == dlp) { // Internal footnote
		footnoteId = href.substring(href.indexOf('#') + 1, href.length);

		// Fetch the content to fill out the footnote
		var content = document.getElementById(footnoteId);

		// Copy the content over
		if (content) {
			innerWindowContentBox.innerHTML = content.innerHTML;
		}
		arrangeWindowVertically(link, windowID);
	} else {
		fetchContent(href, link, windowID);
	}

}

function fetchContent (href, link, windowID) {
	var error = false;

	var xmlhttp = get_xmlhttp();
	if (xmlhttp) {
		xmlhttp.open('GET', href, true);
		xmlhttp.onreadystatechange=function() {
			var innerWindowContentBox = document.getElementById('innerWindowCont-' + windowID);

			if (xmlhttp.readyState==4) {
				if (xmlhttp.status == 200) {
					var xmlDoc = xml_to_DOM(xmlhttp.responseText);
					if (xmlDoc) {
						var contentHTML = false;
						if (xmlDoc.getElementById) {
							contentHTML = xmlDoc.getElementById('footnote').innerHTML;
						} else {
							var nodes = xmlDoc.selectNodes('//div[@id="footnote"]');
							if (nodes.length > 0) {
								contentHTML = nodes[0].xml;
							}
						}
						
						if (innerWindowContentBox) {
							if (contentHTML) {
								innerWindowContentBox.innerHTML = contentHTML;
							} else {
								error = true;
								innerWindowContentBox.innerHTML = '<p><strong>Error:</strong> Could not find note content.</p>';
							}
						}
						arrangeWindowVertically(link, windowID);
					} else {
						error = true;
						innerWindowContentBox.innerHTML = '<p><strong>Error:</strong> Could not create parser to look up note.</p>';
					}
				}
			}
			
			// If there's an error, make up for it by redirecting.
			if (error) {
				window.location.href = href;
			}

		}
		xmlhttp.send('');
	}
}

function closeInlineWindows(){
	for(var i = 0; i < inlineWindowCount; i++) {
		closeInlineWindow(i);
		if ( document.getElementById('inlineWindow-' + i) ) {
			close;
		}
	}
}

function closeInlineWindow (windowID) { 
	var inlineWindow = document.getElementById('inlineWindow-' + windowID);

	if ( inlineWindow ) {
		if (typeof(jQuery) != 'undefined') {
			jQuery('#outerWindowTip-' + windowID).fadeOut('fast', function() {
				jQuery('#outerWindowCont-' + windowID).fadeOut('slow', function() {
					inlineWindow.parentNode.removeChild(inlineWindow);
				} );
			} );
		} else {
			inlineWindow.style.display = 'none';
			inlineWindow.parentNode.removeChild(inlineWindow);
		}
	}
}

function getElementOffset(element,whichCoord) {
	var count = 0
	while (element!=null) {
	 	count += element['offset' + whichCoord];
		element = element.offsetParent;
	}
	return count;
}

// Begin the action
if (typeof(jQuery) != 'undefined') {
	// if possible, use jQuery for faster activation
	jQuery(document).ready(rewriteLinks);
} else {
	add_event(window, 'load', rewriteLinks, true);
}

