<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<?php /* Arclite/digitalnature */ ?>
<html xmlns="http://www.w3.org/1999/xhtml" <?php //language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php wp_title('&laquo;', true, 'right'); if (get_query_var('cpage') ) echo ' Page '.get_query_var('cpage').' &laquo; ';?> <?php bloginfo('name'); ?></title>

<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<link rel="shortcut icon" href="<?php bloginfo('template_directory'); ?>/favicon.ico" />

<?php if (is_singular() && get_option('thread_comments')) wp_enqueue_script( 'comment-reply' ); ?>
<?php if(get_arclite_option('jquery')): ?>
 <?php wp_enqueue_script('jquery'); ?>
 <?php wp_enqueue_script('arclite',get_bloginfo('template_url').'/js/jquery.arclite.min.js',array(),false,true); ?>
<?php endif; ?>

<?php wp_head(); ?>

</head>
<body <?php if (is_home()) { ?>class="home"<?php } else { ?>class="inner"<?php } ?>>
 <!-- page wrap -->
 <div id="page"<?php if(!is_page_template('page-nosidebar.php')) { echo ' class="with-sidebar'; if((get_arclite_option('threecol')) || (is_page_template('page-3col.php'))) echo ' and-secondary'; echo '"';  } ?>>

  <!-- header -->
  <div id="header-wrap">
   <div id="header" class="block-content">
     <div id="pagetitle" class="clearfix">

      <?php
      // logo image?
      $logo = (get_arclite_option('logo'));
      if($logo): ?>
      <h1 class="logo"><a href="<?php bloginfo('url'); ?>/"><img src="<?php echo $logo; ?>" title="<?php bloginfo('name');  ?>" alt="<?php bloginfo('name');  ?>" /></a></h1>
      <?php else: ?>
      <h1 class="logo"><a href="<?php bloginfo('url'); ?>/"><?php bloginfo('name'); ?></a></h1>
      <?php endif;  ?>

      <?php if(get_bloginfo('description')<>'') { ?><h4><?php bloginfo('description'); ?></h4><?php } ?>

      <?php if(get_arclite_option('search')<>'no') { ?>
      <?php // get_search_form(); ?>
      <!-- search form -->
      <div class="search-block">
        <div class="searchform-wrap">
          <form method="get" id="searchform" action="<?php bloginfo('url'); ?>/">
            <fieldset>
            <input type="text" name="s" id="searchbox" class="searchfield" value="<?php _e("Search","arclite"); ?>" onfocus="if(this.value == '<?php _e("Search","arclite"); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e("Search","arclite"); ?>';}" />
             <input type="submit" value="Go" class="go" />
            </fieldset>
          </form>
        </div>
      </div>
      <!-- /search form -->
      <?php } ?>

     </div>

     <!-- main navigation -->
     <div id="nav-wrap1">
      <div id="nav-wrap2">
        <ul id="nav">
         <?php
          if((get_option('show_on_front')<>'page') && (get_arclite_option('navigation')<>'categories')) {
           if(is_home() && !is_paged()){ ?>
            <li id="nav-homelink" class="current_page_item"><a class="fadeThis" href="<?php echo get_settings('home'); ?>" title="<?php _e('You are Home','arclite'); ?>"><span><?php _e('Home','arclite'); ?></span></a></li>
           <?php } else { ?>
            <li id="nav-homelink"><a class="fadeThis" href="<?php echo get_option('home'); ?>" title="<?php _e('Click for Home','arclite'); ?>"><span><?php _e('Home','arclite'); ?></span></a></li>
          <?php
           }
          } ?>
         <?php
           if(get_arclite_option('navigation')=='categories') {
            echo preg_replace('@\<li([^>]*)>\<a([^>]*)>(.*?)\<\/a>@i', '<li$1><a class="fadeThis"$2><span>$3</span></a>', wp_list_categories('show_count=0&echo=0&title_li='.get_arclite_option('navigation_exclude')));
            }
           else {
             echo preg_replace('@\<li([^>]*)>\<a([^>]*)>(.*?)\<\/a>@i', '<li$1><a class="fadeThis"$2><span>$3</span></a>', wp_list_pages('echo=0&orderby=name&title_li=&exclude='.get_arclite_option('navigation_exclude')));
           }
          ?>
        </ul>
      </div>
     </div>
     <!-- /main navigation -->

   </div>
  </div>
  <!-- /header -->
