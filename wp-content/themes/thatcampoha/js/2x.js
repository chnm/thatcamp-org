//see also 2x.php for details on Retina image replacement
var pixelRatio = !!window.devicePixelRatio ? window.devicePixelRatio : 1;

  jQuery(function() {
  
      jQuery.getJSON("/wp-content/themes/boilerplate/2x.php", function(data){
          if (pixelRatio > 1) {
  
              jQuery('header img').each(function() {
  
                  // Very naive replacement that assumes no dots in file names.
                  var newsrc = jQuery(this).attr('src').replace(".","_2x.");
  
                  if (jQuery.inArray(newsrc, data)) {
                      jQuery(this).attr('src', newsrc);
                  }
  
              });
          }
      });
  });