<?php
/*
Template Name: Photo Album


*/
global $TanTanFlickrPlugin;
if (!is_object($TanTanFlickrPlugin)) wp_die('Flickr Photo Album plugin is not installed / activated!');

get_header();
?>

<!-- main wrappers -->
<div id="main-wrap1">
 <div id="main-wrap2">

  <!-- main page block -->
  <div id="main" class="block-content clearfix">
   <div class="mask-main rightdiv">
    <div class="mask-left">

     <!-- first column -->
     <div class="col1">
      <div id="main-content">

        <?php
        include($tpl = $TanTanFlickrPlugin->getDisplayTemplate($photoTemplate));

        // uncomment this line to print out the template being used
        // echo 'Photo Album Template: '.$tpl;
        ?>

        <?php if (!is_object($Silas)):?>
        <div class="flickr-meta-links center">
         Powered by the <a href="http://tantannoodles.com/toolkit/photo-album/">Flickr Photo Album</a> plugin for WordPress.
        </div>
        <?php endif; ?>
            
      </div>
     </div>
     <!-- /first column -->
     <?php get_sidebar(); ?>
     <?php include(TEMPLATEPATH . '/sidebar-secondary.php'); ?>

    </div>
   </div>
  </div>
  <!-- /main page block -->

 </div>
</div>
<!-- /main wrappers -->

<?php get_footer(); ?>

