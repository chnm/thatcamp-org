<?php
/**
 * Defining constants
 *
 * @since 1.0.0
 */
$bavotasan_theme_data = wp_get_theme( 'farben-basic' );
define( 'BAVOTASAN_THEME_URL', get_template_directory_uri() );
define( 'BAVOTASAN_THEME_TEMPLATE', get_template_directory() );
define( 'BAVOTASAN_THEME_VERSION', trim( $bavotasan_theme_data->Version ) );
define( 'BAVOTASAN_THEME_NAME', $bavotasan_theme_data->Name );

/**
 * Includes
 *
 * @since 1.0.0
 */
require( BAVOTASAN_THEME_TEMPLATE . '/library/customizer.php' ); // Functions for theme options page
require( BAVOTASAN_THEME_TEMPLATE . '/library/preview-pro.php' ); // Functions for preview pro page
require( BAVOTASAN_THEME_TEMPLATE . '/library/about.php' ); // Functions for doc page

/**
 * Prepare the content width
 *
 * @since 1.0.3
 */
function bavotasan_content_width() {
	$bavotasan_theme_options = bavotasan_theme_options();
	$bavotasan_array_content = array( 'col-md-2' => .1666, 'col-md-3' => .25, 'col-md-4' => .3333, 'col-md-5' => .4166, 'col-md-6' => .5, 'col-md-7' => .5833, 'col-md-8' => .6666, 'col-md-9' => .75, 'col-md-10' => .8333, 'col-md-12' => 1 );

    return round( $bavotasan_array_content[$bavotasan_theme_options['primary']] * $bavotasan_theme_options['width'] - 30 );
}

if ( ! isset( $content_width ) )
	$content_width = absint( bavotasan_content_width() );

add_action( 'after_setup_theme', 'bavotasan_setup' );
if ( ! function_exists( 'bavotasan_setup' ) ) :
/**
 * Initial setup
 *
 * This function is attached to the 'after_setup_theme' action hook.
 *
 * @uses	load_theme_textdomain()
 * @uses	get_locale()
 * @uses	BAVOTASAN_THEME_TEMPLATE
 * @uses	add_theme_support()
 * @uses	add_editor_style()
 * @uses	add_custom_background()
 * @uses	add_custom_image_header()
 * @uses	register_default_headers()
 *
 * @since 1.0.0
 */
function bavotasan_setup() {
	load_theme_textdomain( 'farben-basic', BAVOTASAN_THEME_TEMPLATE . '/library/languages' );

	// Add default posts and comments RSS feed links to <head>.
	add_theme_support( 'automatic-feed-links' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style( 'library/css/admin/editor-style.css' );

	// This theme uses wp_nav_menu() in two location.
	register_nav_menu( 'primary', __( 'Primary Menu', 'farben-basic' ) );

	// Add support for a variety of post formats
	add_theme_support( 'post-formats', array( 'gallery', 'image', 'video', 'audio', 'quote', 'link', 'status', 'aside' ) );

	// This theme uses Featured Images (also known as post thumbnails) for archive pages
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'sticky', 250, 250, true );
	add_image_size( 'sticky-wide', 500, 250, true );
	add_image_size( 'sticky-double', 500, 500, true );
	add_image_size( 'featured-img', 840, 410, true );

	// Add a filter to bavotasan_header_image_width and bavotasan_header_image_height to change the width and height of your custom header.
	add_theme_support( 'custom-header', array(
		'header-text' => false,
		'flex-height' => true,
		'flex-width' => true,
		'random-default' => true,
		'width' => apply_filters( 'bavotasan_header_image_width', 1400 ),
		'height' => apply_filters( 'bavotasan_header_image_height', 500 ),
	) );

	// Add support for custom backgrounds
	add_theme_support( 'custom-background' );

	// Add HTML5 elements
	add_theme_support( 'html5', array( 'comment-list', 'comment-form', ) );

	// Add title tag support
	add_theme_support( 'title-tag' );

	// Infinite scroll
	add_theme_support( 'infinite-scroll', array(
	    'type' => 'scroll',
	    'container' => 'primary',
		'wrapper' => false,
		'footer' => true,
	) );

	// Remove default gallery styles
	add_filter( 'use_default_gallery_style', '__return_false' );

	// Add Woocommerce support
	add_theme_support( 'woocommerce' );
}
endif; // bavotasan_setup

add_action( 'wp_head', 'bavotasan_styles' );
/**
 * Add a style block to the theme for the current link color.
 *
 * This function is attached to the 'wp_head' action hook.
 *
 * @since 1.0.0
 */
function bavotasan_styles() {
	$bavotasan_theme_options = bavotasan_theme_options();
	?>
<style>
.container { max-width: <?php echo esc_attr( $bavotasan_theme_options['width'] ); ?>px; }
</style>
	<?php
}

add_action( 'wp_enqueue_scripts', 'bavotasan_add_js' );
if ( ! function_exists( 'bavotasan_add_js' ) ) :
/**
 * Load all JavaScript to header
 *
 * This function is attached to the 'wp_enqueue_scripts' action hook.
 *
 * @uses	is_admin()
 * @uses	is_singular()
 * @uses	get_option()
 * @uses	wp_enqueue_script()
 * @uses	BAVOTASAN_THEME_URL
 *
 * @since 1.0.0
 */
function bavotasan_add_js() {
	$bavotasan_theme_options = bavotasan_theme_options();

	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	if ( is_front_page() )
		wp_enqueue_script( 'masonry' );

	wp_enqueue_script( 'bootstrap', BAVOTASAN_THEME_URL .'/library/js/bootstrap.js', array( 'jquery' ), '3.0.3', true );
	wp_enqueue_script( 'theme', BAVOTASAN_THEME_URL .'/library/js/theme.js', array( 'bootstrap' ), '', true );

	wp_enqueue_style( 'theme_stylesheet', get_stylesheet_uri() );
	wp_enqueue_style( 'google_fonts', esc_url( bavotasan_font_url() ), false, null );
	wp_enqueue_style( 'font_awesome', BAVOTASAN_THEME_URL .'/library/css/font-awesome.css', false, '4.3.0', 'all' );
}
endif; // bavotasan_add_js

/**
 * Prepare Google Fonts if any have been selected.
 *
 * @since 1.0.0
 */
function bavotasan_font_url() {
	return add_query_arg( 'family', 'Source+Sans+Pro', '//fonts.googleapis.com/css' );
}

add_action( 'widgets_init', 'bavotasan_widgets_init' );
if ( ! function_exists( 'bavotasan_widgets_init' ) ) :
/**
 * Creating the two sidebars
 *
 * This function is attached to the 'widgets_init' action hook.
 *
 * @uses	register_sidebar()
 *
 * @since 1.0.0
 */
function bavotasan_widgets_init() {
	register_sidebar( array(
		'name' => __( 'First Sidebar', 'farben-basic' ),
		'id' => 'sidebar',
		'description' => __( 'This is the first sidebar. It won&rsquo;t appear on the home page unless you set a static front page.', 'farben-basic' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Jumbo Headline', 'farben-basic' ),
		'id' => 'jumbo-headline',
		'description' => __( 'Area on the home page below the large header image. Designed specifically for one text widget. ', 'farben-basic' ),
		'before_widget' => '<aside id="%1$s" class="jumbo-headline %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h2>',
		'after_title' => '</h2>',
	) );
}
endif; // bavotasan_widgets_init

/**
 * Add pagination
 *
 * @uses	paginate_links()
 * @uses	add_query_arg()
 *
 * @since 1.0.0
 */
function bavotasan_pagination() {
	global $wp_query, $paged;

	// Don't print empty markup if there's only one page.
	if ( $wp_query->max_num_pages < 2 && 0 == $paged )
		return;
	?>

	<nav class="navigation clearfix" role="navigation">
		<div class="sr-only"><?php _e( 'Posts navigation', 'farben-basic' ); ?></div>
		<?php if ( get_next_posts_link() ) : ?>
		<div class="nav-previous"><?php next_posts_link( __( '<span class="sr-only">Older posts</span>', 'farben-basic' ) ); ?></div>
		<?php endif; ?>

		<?php if ( get_previous_posts_link() ) : ?>
		<div class="nav-next"><?php previous_posts_link( __( '<span class="sr-only">Newer posts</span>', 'farben-basic' ) ); ?></div>
		<?php endif; ?>
	</nav><!-- .navigation -->
	<?php
	wp_reset_query();
}

if ( ! function_exists( 'bavotasan_comment' ) ) :
/**
 * Callback function for comments
 *
 * Referenced via wp_list_comments() in comments.php.
 *
 * @uses	get_avatar()
 * @uses	get_comment_author_link()
 * @uses	get_comment_date()
 * @uses	get_comment_time()
 * @uses	edit_comment_link()
 * @uses	comment_text()
 * @uses	comments_open()
 * @uses	comment_reply_link()
 *
 * @since 1.0.0
 */
function bavotasan_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;

	switch ( $comment->comment_type ) :
		case '' :
		?>
		<li <?php comment_class(); ?>>
			<div id="comment-<?php comment_ID(); ?>" class="comment-body">
				<div class="comment-avatar">
					<?php echo get_avatar( $comment, 55 ); ?>
				</div>
				<div class="comment-content">
					<div class="comment-author">
						<?php echo get_comment_author_link() . ' '; ?>
					</div>
					<div class="comment-meta">
						<?php
						printf( __( '%1$s at %2$s', 'farben-basic' ), get_comment_date(), get_comment_time() );
						edit_comment_link( __( 'Edit', 'farben-basic' ), '  ', '' );
						?>
					</div>
					<div class="comment-text">
						<?php if ( '0' == $comment->comment_approved ) { echo '<em>' . __( 'Your comment is awaiting moderation.', 'farben-basic' ) . '</em>'; } ?>
						<?php comment_text() ?>
					</div>
					<?php if ( $args['max_depth'] != $depth && comments_open() && 'pingback' != $comment->comment_type ) { ?>
					<div class="reply">
						<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
					</div>
					<?php } ?>
				</div>
			</div>
			<?php
			break;

		case 'pingback'  :
		case 'trackback' :
		?>
		<li id="comment-<?php comment_ID(); ?>" class="pingback">
			<div class="comment-body">
				<i class="fa fa-paperclip"></i>
				<?php _e( 'Pingback:', 'farben-basic' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(edit)', 'farben-basic' ), ' ' ); ?>
			</div>
			<?php
			break;
	endswitch;
}
endif; // bavotasan_comment

add_filter( 'excerpt_more', 'bavotasan_excerpt' );
if ( ! function_exists( 'bavotasan_excerpt' ) ) :
/**
 * Adds a read more link to all excerpts
 *
 * This function is attached to the 'excerpt_more' filter hook.
 *
 * @param	int $more
 *
 * @return	Custom excerpt ending
 *
	 * @since 1.0.0
 */
function bavotasan_excerpt( $more ) {
	return '&hellip; <p><a class="more-link" href="' . esc_url( get_permalink( get_the_ID() ) ) . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'farben-basic' ) . '</a></p>';
}
endif; // bavotasan_excerpt

/**
 * Print the attached image with a link to the next attached image.
 *
 * @since 1.0.9
 */
function bavotasan_the_attached_image() {
	$post = get_post();

	$attachment_size = apply_filters( 'bavotasan_attachment_size', array( 810, 810 ) );
	$next_attachment_url = wp_get_attachment_url();

	$attachment_ids = get_posts( array(
		'post_parent' => $post->post_parent,
		'fields' => 'ids',
		'numberposts' => -1,
		'post_status' => 'inherit',
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'order' => 'ASC',
		'orderby' => 'menu_order ID',
	) );

	if ( count( $attachment_ids ) > 1 ) {
		foreach ( $attachment_ids as $attachment_id ) {
			if ( $attachment_id == $post->ID ) {
				$next_id = current( $attachment_ids );
				break;
			}
		}

		if ( $next_id )
			$next_attachment_url = get_attachment_link( $next_id );
		else
			$next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
	}

	printf( '<a href="%1$s" rel="attachment">%2$s</a>',
		esc_url( $next_attachment_url ),
		wp_get_attachment_image( $post->ID, $attachment_size )
	);
}

/**
 * Create the required attributes for the #primary container
 *
 * @since 1.0.0
 */
function bavotasan_primary_attr() {
	$bavotasan_theme_options = bavotasan_theme_options();
	$primary = str_replace( 'col-md-', '', $bavotasan_theme_options['primary'] );
	$secondary = ( is_active_sidebar( 'second-sidebar' ) ) ? str_replace( 'col-md-', '', $bavotasan_theme_options['secondary'] ) : 12 - $primary;

	$class = $bavotasan_theme_options['primary'];
	$class = ( 'left' == $bavotasan_theme_options['layout'] ) ? $class . ' pull-right' : $class;

	echo 'class="' . esc_attr( $class ) . '"';
}

/**
 * Create the required classes for the #secondary sidebar container
 *
 * @since 1.0.0
 */
function bavotasan_sidebar_class() {
	$bavotasan_theme_options = bavotasan_theme_options();
	$primary = str_replace( 'col-md-', '', $bavotasan_theme_options['primary'] );

	$end = ( 'right' == $bavotasan_theme_options['layout'] ) ? ' end' : '';
	$class = 'col-md-' . ( 12 - $primary ) . $end;

	echo 'class="' . esc_attr( $class ) . '"';
}

/**
 * Default menu
 *
 * Referenced via wp_nav_menu() in header.php.
 *
 * @since 1.0.0
 */
function bavotasan_default_menu( $args ) {
	extract( $args );

	$output = wp_list_categories( array(
		'title_li' => '',
		'echo' => 0,
		'number' => 5,
		'depth' => 1,
	) );

	echo '<ul class="' . esc_attr( $menu_class ) . '">' . $output . '</ul>';
}

/**
 * Add bootstrap classes to menu items
 *
 * @since 1.0.0
 */
class Bavotasan_Page_Navigation_Walker extends Walker_Nav_Menu {
	function check_current( $classes ) {
		return preg_match( '/(current[-_])|active|dropdown/', $classes );
	}

	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$output .= "\n<ul class=\"dropdown-menu\">\n";
	}

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$item_html = '';
		parent::start_el( $item_html, $item, $depth, $args );

		if ( $item->is_dropdown && ( $depth === 0 ) ) {
			$item_html = str_replace( '<a', '<a class="dropdown-toggle" data-toggle="dropdown" data-target="#"', $item_html );
			$item_html = str_replace( '</a>', ' <span class="caret"></span></a>', $item_html );
		} elseif ( stristr( $item_html, 'li class="divider' ) ) {
			$item_html = preg_replace( '/<a[^>]*>.*?<\/a>/iU', '', $item_html );
		} elseif ( stristr( $item_html, 'li class="nav-header' ) ) {
			$item_html = preg_replace( '/<a[^>]*>(.*)<\/a>/iU', '$1', $item_html );
		}

		$output .= $item_html;
	}

	function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
		$element->is_dropdown = !empty( $children_elements[$element->ID] );

		if ( $element->is_dropdown ) {
			if ( $depth === 0 ) {
				$element->classes[] = 'dropdown';
			} elseif ( $depth > 0 ) {
				$element->classes[] = 'dropdown-submenu';
			}
		}
		$element->classes[] = ( $element->current || in_array( 'current-menu-parent', $element->classes ) ) ? 'active' : '';

		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
}

add_filter( 'wp_nav_menu_args', 'bavotasan_nav_menu_args' );
/**
 * Set our new walker only if a menu is assigned and a child theme hasn't modified it to one level deep
 *
 * This function is attached to the 'wp_nav_menu_args' filter hook.
 *
 * @author Kirk Wight <http://kwight.ca/adding-a-sub-menu-indicator-to-parent-menu-items/>
 * @since 1.0.0
 */
function bavotasan_nav_menu_args( $args ) {
    if ( 1 !== $args[ 'depth' ] && has_nav_menu( 'primary' ) && 'primary' == $args[ 'theme_location' ] )
        $args[ 'walker' ] = new Bavotasan_Page_Navigation_Walker;

    return $args;
}

add_filter( 'post_class', 'bavotasan_post_class' );
/**
 * Add post class
 *
 * @since 1.0.0
 */
function bavotasan_post_class( $classes ) {
   	$classes[] = 'xfolkentry';
   	$classes[] = 'clearfix';

	return $classes;
}


add_filter( 'body_class', 'bavotasan_body_class' );
/**
 * Add body class
 *
 * @since 1.0.0
 */
function bavotasan_body_class( $classes ) {
   	$classes[] = 'basic';

	return $classes;
}

/**
 * Display search results title
 *
 * @since 1.0.0
 */
function bavotasan_search_title() {
	global $wp_query;
    $num = $wp_query->found_posts;
	printf( __( '%1$s search results for "%2$s"', 'farben-basic'),
	    absint( $wp_query->found_posts ),
	    esc_html( get_search_query() )
	);
}

add_action( 'pre_get_posts', 'bavotasan_home_query' );
/**
 * Remove sticky posts from home page query
 *
 * This function is attached to the 'pre_get_posts' action hook.
 *
 * @param	array $query
 *
 * @since 1.0.0
 */
function bavotasan_home_query( $query = '' ) {
	$bavotasan_slider_options = get_option( 'farben_slider_settings' );

	if ( is_home() && is_front_page() && ! is_admin() && $query->is_main_query() )
		$query->set( 'post__not_in', (array) get_option( 'sticky_posts' ) );
}

/**
 * Creates sticky variables depending on number of sticky posts
 * displayed
 *
 * @since 1.0.0
 */
function bavotasan_sticky_variables( $var, $query ) {
	if ( 10 == $query->post_count ) {
		$img_size = ( 2 == $query->current_post || 9 == $query->current_post ) ? 'sticky-wide' : 'sticky';
		$img_size = ( 5 == $query->current_post ) ? 'sticky-double' : $img_size;
		$class = ( 2 == $query->current_post || 5 == $query->current_post || 9 == $query->current_post ) ? 'wide' : '';
	} elseif ( 9 == $query->post_count ) {
		$img_size = ( 5 == $query->current_post ) ? 'sticky-wide' : 'sticky';
		$class = ( 5 == $query->current_post ) ? 'wide' : '';
	} elseif ( 8 == $query->post_count ) {
		$img_size = ( 2 == $query->current_post || 7 == $query->current_post ) ? 'sticky-wide' : 'sticky';
		$class = ( 2 == $query->current_post || 7 == $query->current_post ) ? 'wide' : '';
	} elseif ( 7 == $query->post_count ) {
		$img_size = ( 2 == $query->current_post ) ? 'sticky-double' : 'sticky';
		$class = ( 2 == $query->current_post ) ? 'wide' : '';
	} elseif ( 6 == $query->post_count ) {
		$img_size = ( 2 == $query->current_post ) ? 'sticky-double' : 'sticky';
		$img_size = ( 4 == $query->current_post ) ? 'sticky-wide' : $img_size;
		$class = ( 2 == $query->current_post || 4 == $query->current_post ) ? 'wide' : '';
	} elseif ( 4 == $query->post_count ) {
		$img_size = ( 2 == $query->current_post ) ? 'sticky-wide' : 'sticky';
		$class = ( 2 == $query->current_post ) ? 'wide' : '';
	} elseif ( 3 == $query->post_count ) {
		$img_size = ( 1 == $query->current_post || 2 == $query->current_post ) ? 'sticky-wide' : 'sticky';
		$class = ( 1 == $query->current_post || 2 == $query->current_post ) ? 'wide' : '';
	} else {
		$img_size = 'sticky';
		$class = '';
	}

	$return = array(
		'img_size' => $img_size,
		'class' => $class,
	);

	return $return[$var];
}

function bavotasan_color_array( $num ) {
	$array = array( 'yellow', 'blue', 'light-blue', 'red', 'green', 'mint', '' );

	return $array[$num];
}

/**
 * Display a post thumbnail if one exists and use the correct size/class
 *
 * @since 1.0.0
 */
function bavotasan_display_post_thumbnail() {
	if( has_post_thumbnail() ) {
		?>
		<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
			<?php the_post_thumbnail( 'featured-img', array( 'class' => 'image-full aligncenter' ) ); ?>
		</a>
		<?php
	}
}

/**
 * Woocommerce compatibility
 *
 * @since 1.0.1
 */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

add_action('woocommerce_before_main_content', 'bavotasan_wrapper_start', 10 );
function bavotasan_wrapper_start() {
	?>
	<div class="container">
		<div class="row">
			<div id="primary" class="col-md-12">
	<?php
}

add_action('woocommerce_after_main_content', 'bavotasan_wrapper_end', 10 );
function bavotasan_wrapper_end() {
	?>
			</div>
		</div>
	</div>
	<?php
}