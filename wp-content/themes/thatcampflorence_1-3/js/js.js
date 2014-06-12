var IE = /*@cc_on!@*/false;

function links(id) {
	var link = document.getElementById(id).getElementsByTagName("a");
	for (var i = 0; i < link.length; i++)
		link[i].setAttribute("target","_blank");
}

$(document).ready(function(){

	links("new");
	links("navb");
	links("sidebar");
	links("extra");
	links("footer");
	
	var rel = "group";
	var linkA = document.getElementById("content").getElementsByTagName("a")[0].getAttribute('rel');
	if(linkA != undefined) rel = linkA;
	
	$("a[rel=" + rel + "]").fancybox({
		'opacity'			: true,
		'overlayShow'		: true,
		'transitionIn'		: 'elastic',
		'transitionOut'		: 'elastic',
		'titlePosition'		: 'outside',
		'overlayColor'		: '#000',
		'overlayOpacity'	: 0.75,
		'titleFormat'		: function() { return ''; }
	});
	
});

$(function()
{
	$("#map").gMap({ markers: [{
	latitude: 43.788056,
	longitude: 11.284734
	}],
	zoom: 7 });
});

if(!IE){
var head  = document.getElementsByTagName("head").item(0);
var style = document.createElement("style");
style.setAttribute('type','text/css');
style.setAttribute('media','all');
style.appendChild(document.createTextNode("#content h2, #content h3, #content h4, #sidebar h2, #sidebar h3, #sidebar h4 { text-shadow: 1px 2px 0 white; } #content #nav a:hover, #sidebar ul li ul.tweet_area li img.tweet_avatar, #navigation li a:hover, #content img, .border, #navigation ul li ul { -moz-box-shadow: 1px 1px 5px rgba(0, 0, 0, .5); -webkit-box-shadow: 1px 1px 5px rgba(0, 0, 0, .5); -goog-ms-box-shadow: 1px 1px 5px rgba(0, 0, 0, .5); box-shadow: 1px 1px 5px rgba(0, 0, 0, .5); } #content #map img, #content .wp-smiley { background: transparent; border: 0; -moz-box-shadow: none; -webkit-box-shadow: none; -goog-ms-box-shadow: none; } "));
head.appendChild(style);
}