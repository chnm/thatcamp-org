<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<?php if((is_home() && ($paged < 2 )) || is_single() || is_page()) { echo '<meta name="robots" content="index,follow" />'; } else { echo '<meta name="robots" content="noindex,follow" />'; } ?>

<?php if (is_single() || is_page() ) : if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<meta name="description" content="<?php metaDesc(); ?>" />
<?php csv_tags(); ?>
<?php endwhile; endif; elseif(is_home()) : ?>
<meta name="description" content="<?php if(theme_option('site_description')) { echo trim(stripslashes(theme_option('site_description'))); } else { bloginfo('description'); } ?>" />
<meta name="keywords" content="<?php if(theme_option('keywords')) { echo trim(stripslashes(theme_option('keywords'))); } else { echo 'wordpress,c.bavota,magazine basic,custom theme,themes.bavotasan.com,premium themes'; } ?>" />
<?php endif; ?>

<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' | '; } ?><?php bloginfo('name'); if(is_home()) { echo ' | '; bloginfo('description'); } ?></title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<?php pbt_header_css(); ?>
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="<?php echo THEME_URL; ?>/iestyles.css" />
<![endif]-->
<?php if(is_singular() && get_option('thread_comments')) wp_enqueue_script( 'comment-reply' ); ?>
<?php wp_enqueue_script('jquery'); ?>
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<!-- begin header -->
<?php if(theme_option('user_login') != 2) { ?>
<div id="login">
	<?php
    global $user_identity, $user_level;
    if (is_user_logged_in()) { ?>
        <ul>
            <li><span style="float:left;"><?php _e('Logged in as:', "feed-me-seymour"); ?> <strong><?php echo $user_identity ?></strong></span></li>
            <li><a href="<?php echo admin_url(); ?>"><?php _e('Control Panel', "feed-me-seymour"); ?></a></li>
            <?php if ( $user_level >= 1 ) { ?>
                <li class="dot"><a href="<?php echo admin_url('post-new.php'); ?>"><?php _e('Write', "feed-me-seymour"); ?></a></li>
            <?php } ?>
            <li class="dot"><a href="<?php echo admin_url('profile.php'); ?>"><?php _e('Profile', "feed-me-seymour"); ?></a></li>
            <li class="dot"><a href="<?php echo wp_logout_url( get_permalink() ); ?>" title="<?php _e('Log Out', "feed-me-seymour") ?>"><?php _e('Log Out', "feed-me-seymour"); ?></a></li>
        </ul>
    <?php 
    } else {
        echo '<ul>';
				echo '<li><a href="'.wp_login_url( get_permalink() ).'">'.__('Log In', "feed-me-seymour").'</a></li>';
        if (get_option('users_can_register')) { ?>
            <li class="dot"><a href="<?php echo site_url('wp-login.php?action=register', 'login') ?>"><?php _e('Register', "feed-me-seymour") ?></a> </li>
    <?php 
        }
		echo '</ul>';
    } ?> 
</div>
<?php 
}
$headeralign = theme_option('logo_location');
if($headeralign=="fl") $adfloat = ' class="fr"';
if($headeralign=="fr") $adfloat = ' class="fl"';
if($headeralign=="aligncenter") $adfloat = ' class="aligncenter"';
$float = ' class="'.$headeralign.'"';
?>
<div id="header">
    <?php if(theme_option('rss_button') != 2 && $headeralign!="aligncenter") { ?>
    <div id="header-rss"<?php if(!empty($adfloat)) echo $adfloat; ?>>
    	<a href="<?php bloginfo('rss2_url'); ?>"><img src="<?php echo THEME_URL; ?>/images/rss.png" alt="Subscribe to RSS Feed" /></a><p><?php _e('Subscribe to RSS', "feed-me-seymour"); ?></p>
    </div>
    <?php } ?>
	<?php if (theme_option('logo_header')) { ?>
    	<a href="<?php echo home_url(); ?>/" class="headerimage"><img src="<?php echo theme_option('logo_header'); ?>" alt="<?php bloginfo('name'); ?>"<?php echo $float; ?> /></a>
    <?php } else { ?>
    <div id="title"<?php echo $float; ?>>
    	<a href="<?php echo home_url(); ?>/"><?php bloginfo('name'); ?></a>
    </div>
    <?php } ?>
    <div id="description"<?php echo $float; ?>>
        <?php if(theme_option('tag_line')!=2) bloginfo('description'); ?>
    </div> 
</div>
<!-- end header -->

<div id="mainwrapper">
<?php
	$loc = theme_option('sidebar_location');
	if($loc==1 || $loc==3 || $loc==5) {
		get_sidebar(); // calling the First Sidebar
	}
	if($loc==3) get_sidebar( "second" );
	?>
	<div id="leftcontent">