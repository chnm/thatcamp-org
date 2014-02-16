jQuery(function(){
  // Cycle
  jQuery('#highlight').cycle({
    fx:        'fade',
    timeout:   '5000',
    pager:     '#highlight-pager',
    cleartype: 1
  });
  
  // Submenus
  jQuery('#menu li:has(ul)').hover(
    function(){
      if(jQuery(this).children('ul').queue().length)
        return false;
      
      jQuery(this).children('ul').slideDown();
    },
    function(){
      jQuery(this).children('ul').slideUp();
    }
  );
  
  // Zebra
  //jQuery(".post:odd, .comment:odd, #widget_pages li:odd, #widget_categories li:odd, #widget_archive li:odd, #widget_recent_comments li:odd, #widget_recent_entries li:odd, #widget_rss li:odd, .links li:odd, #widget_meta li:odd").addClass("odd");
  jQuery(".post:odd, .comment:odd").addClass("odd");
  jQuery("#sidebar ul").children("li:odd").addClass("odd");
  
  // Primeiro item sem borda
  //jQuery("#menu li:first a, #widget_pages li:first a, #widget_categories li:first a, #widget_archive li:first a, #widget_recent_comments li:first, #widget_recent_entries li:first a, #widget_rss li:first,.links ul li:first a, #widget_meta li:first a").addClass('noborder');
  jQuery("#sidebar ul li:first-child, #sidebar ul li:first-child a").addClass("noborder");
  
  // BackToTop
  jQuery('.backtotop').click(function(){ backtotop(); return false; });
  
});
