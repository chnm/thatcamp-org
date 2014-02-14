<?php

if ( function_exists('register_sidebar') ) :

	register_sidebar(
		array(
			'name'          => __( 'Sidebar', 'thatcamp'),
			'id'            => 'sidebar',
			'description'   => 'Sidebar',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
		
	register_sidebar(array(
        		'name' => 'Footer',
	        	'before_widget' => '',
        		'after_widget' => '',
	          'before_title' => '<div class="title">',
	          'after_title' => '</div>',
    		)
	 );
	
endif;

// Require our widget file
require( dirname(__FILE__) . '/widgets.php' );

function register_my_menus() {
  register_nav_menus(
    array('main-menu' => __( 'Main Menu' ) )
  );
}

add_action( 'init', 'register_my_menus' );

add_theme_support( 'post-thumbnails', array( 'post', 'page' ) );

add_action( 'init', 'enable_category_taxonomy_for_pages', 500 );

function enable_category_taxonomy_for_pages() {
    register_taxonomy_for_object_type('category','page');
}


/* Flush rewrite rules for custom post types. */
add_action( 'load-themes.php', 'frosty_flush_rewrite_rules' );

/* Flush your rewrite rules */
function frosty_flush_rewrite_rules() {
	global $pagenow, $wp_rewrite;

	if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) )
		$wp_rewrite->flush_rules();
}

wp_enqueue_script('modernizr.custom.20659', get_template_directory_uri() . '/javascripts/modernizr.custom.20659.js',array(),'1.0',false);
wp_enqueue_script('respond.min', get_template_directory_uri() . '/javascripts/respond.min.js',array(),'1.0',true);
wp_enqueue_script('selectivizr-min', get_template_directory_uri() . '/javascripts/selectivizr-min.js',array('jquery'),'1.0',true);
wp_enqueue_script('jquery.cookie', get_template_directory_uri() . '/javascripts/jquery.cookie.js',array('jquery'),'1.0',true);
wp_enqueue_script('jquery.hoverIntent.minified', get_template_directory_uri('jquery') . '/javascripts/jquery.hoverIntent.minified.js',array(),'1.0',true);
wp_enqueue_script('jquery.dcjqaccordion.2.7.min', get_template_directory_uri('jquery') . '/javascripts/jquery.dcjqaccordion.2.7.min.js',array(),'1.0',true);

function thatcamp_older_posts_link( $link_text, $max_pages ) {
        $paged = thatcamp_get_paged();

        if ( $paged >= $max_pages ) {
                return;
        }

        $p = $paged + 1;
        if ( false !== strpos( wp_guess_url(), '/page/' ) ) {
                $url = preg_replace( '|/page/[0-9]+/|', '/page/' . $p . '/', wp_guess_url() );
        } else {
                $url = add_query_arg( 'paged', $p, wp_guess_url() );
        }

        echo '<a href="' . $url . '">' . $link_text . '</a>';
}

function thatcamp_newer_posts_link( $link_text, $max_pages ) {
        $paged = thatcamp_get_paged();

        if ( 1 === $paged ) {
                return;
        }

        $p = $paged - 1;
        if ( false !== strpos( wp_guess_url(), '/page/' ) ) {
                $url = preg_replace( '|/page/[0-9]+/|', '/page/' . $p . '/', wp_guess_url() );
        } else {
                $url = add_query_arg( 'paged', $p, wp_guess_url() );
        }

        echo '<a href="' . $url . '">' . $link_text . '</a>';
}

function thatcamp_get_paged() {
        global $paged;

	$the_paged = null;

	if ( isset( $_GET['paged'] ) ) {
		$the_paged = intval( $_GET['paged'] );
	}

	if ( ! $the_paged ) {
		if ( ! $paged ) {
			$the_paged = 1;
		} else {
			$the_paged = $paged;
		}
	}

        return $the_paged;
}

function thatcamp_add_styles_alt() {
	if ( bp_is_root_blog() ) {
		return;
	}

	?>
<style type="text/css">
div.generic-button {
  margin-bottom: 1rem;
}
div.generic-button a {
  background: #668800 url('<?php echo WP_CONTENT_URL ?>/themes/thatcamp-karma/assets/images/thatcamp-greenbutton.jpg');
  border: 1px solid #668800;
  opacity: 1;
  -moz-border-radius: 3px;
  -webkit-border-radius: 3px;
  border-radius: 3px;
  color: #ffffff;
  cursor: pointer;
  font-family: Francois One;
  font-size: 1.1rem;
  outline: none;
  padding: 4px 10px;
  text-align: center;
  text-decoration: none;
  line-height: 14px;
  text-decoration: -1px -1px 0px #668800;
}
div.generic-button a:hover {
  opacity: 0.9;
}
div.generic-button.disabled-button {
  position: relative;
}
div.generic-button.disabled-button a {
  opacity: 0.5;
}
div.generic-button.disabled-button span {
  margin-left: -999em;
  position: absolute;
}
div.generic-button.disabled-button:hover span {
  border-radius: 5px 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px;
  box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.1); -webkit-box-shadow: 5px 5px rgba(0, 0, 0, 0.1); -moz-box-shadow: 5px 5px rgba(0, 0, 0, 0.1);
  position: absolute; left: 1em; top: 2em; z-index: 99;
  margin-left: 0;
  background: #FFFFAA; border: 1px solid #FFAD33;
  padding: 4px 8px;
  white-space: nowrap;
}
</style>
	<?php
}
remove_action( 'wp_head', 'thatcamp_add_styles' );
add_action( 'wp_head', 'thatcamp_add_styles_alt' );
?>