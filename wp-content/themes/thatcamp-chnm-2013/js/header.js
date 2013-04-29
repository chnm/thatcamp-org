// This jquery invokes the crossSlide jQuery plugin – the images can be from any location

jQuery(document).ready(function() {
    jQuery('#sliding-header').
    crossSlide({sleep: 2,fade: 1},
    [
	        { src: 'http://thatcamp.org/wp-content/themes/thatcamp-chnm-2013/images/bannerA-960x250.jpg' },
	        { src: 'http://thatcamp.org/wp-content/themes/thatcamp-chnm-2013/images/bannerB-960x250.jpg' } 
    ]);
});