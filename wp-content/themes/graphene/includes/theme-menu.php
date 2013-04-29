<?php
/**
 * Defines the custom walker that adds description to the display of our Header Menu
*/
class Graphene_Description_Walker extends Walker_Nav_Menu {
	function start_el( &$output, $item, $depth, $args ) {
		global $wp_query;
		
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		
		$class_names = $value = '';
		
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="'. esc_attr( $class_names ) . '"';
		
		$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
		
		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
		
		$prepend = '<strong>';
		$append = '</strong>';
		
		// Don't show description if it's longer than the length
		$desc_length = apply_filters( 'graphene_menu_desc_length', 50 );
		
		if ( strlen( $item->description ) > $desc_length)
			$description = '';
		else
			$description  = ! empty( $item->description ) ? '<span>'.esc_attr( $item->description ).'</span>' : '';
		
		if ( $depth != 0 )	{
				 $description = $append = $prepend = "";
		}
		
		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before .$prepend.apply_filters( 'the_title', $item->title, $item->ID ).$append;
		$item_output .= $description.$args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;
		
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}


/**
 * Define the callback menu, if there is no custom menu.
 * This menu automatically lists all Pages as menu items, including their direct
 * direct descendant, which will only be displayed for the current parent.
*/
if ( ! function_exists( 'graphene_default_menu' ) ) :

	function graphene_default_menu(){ global $graphene_settings; ?>
    
		<ul id="header-menu" class="<?php echo graphene_get_menu_class('menu clearfix default-menu'); ?>">
            <?php if (get_option( 'show_on_front' ) == 'posts' ) : ?>
            <li <?php if ( is_single() || is_front_page() ) { echo 'class="current_page_item current-menu-item"'; } ?>>
            	<a href="<?php echo get_home_url(); ?>">
                	<?php if ( ! $graphene_settings['disable_menu_desc'] ) echo '<strong>'; ?>
                	<?php _e( 'Home','graphene' ); ?>
                    <?php if ( ! $graphene_settings['disable_menu_desc'] ) echo '</strong>'; ?>
                    <?php if ( $graphene_settings['navmenu_home_desc']) {echo '<span>'.$graphene_settings['navmenu_home_desc'].'</span>';} ?>
                </a>
            </li>
            <?php endif; ?>
            <?php 
			$args = array( 
						'echo' 			=> 1,
						'depth' 		=> 5,
						'title_li' 		=> '',
						'walker' 		=> new Graphene_Walker_Page() 
					);
			add_filter( 'page_css_class', 'graphene_page_ancestor_class', 10, 4 );
			wp_list_pages( apply_filters( 'graphene_default_menu_args', $args ) );
			remove_filter( 'page_css_class', 'graphene_page_ancestor_class', 10, 4 );
			?>
        </ul>
<?php
	do_action( 'graphene_default_menu' );
	} 
	
endif;

class Graphene_Walker_Page extends Walker_Page {
    
    /**
     * Code exact copied from: wp-includes\post-template.php >> Walker_Page::start_el() 
     * @since 2.1.0
     */
	function start_el( &$output, $page, $depth, $args, $current_page = 0 ) {
		global $graphene_settings;
		
		if ( $depth )
			$indent = str_repeat("\t", $depth);
		else
			$indent = '';

		extract($args, EXTR_SKIP);
		$css_class = array('page_item', 'page-item-'.$page->ID);
		if ( !empty($current_page) ) {
			$_current_page = get_post( $current_page );
			if ( in_array( $page->ID, $_current_page->ancestors ) )
				$css_class[] = 'current_page_ancestor';
			if ( $page->ID == $current_page )
				$css_class[] = 'current_page_item';
			elseif ( $_current_page && $page->ID == $_current_page->post_parent )
				$css_class[] = 'current_page_parent';
		} elseif ( $page->ID == get_option('page_for_posts') ) {
			$css_class[] = 'current_page_parent';
		}

		$css_class = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );
		
		$title = apply_filters( 'the_title', $page->post_title, $page->ID );
		
		if ( ! $depth && ! $graphene_settings['disable_menu_desc'] ){
			$title = '<strong>' . $title . '</strong>';
			$nav_desc = graphene_get_post_meta( $page->ID, 'nav_description' );
			$title .= ( $nav_desc ) ? '<span>' . $nav_desc . '</span>' : '';
		}

		$output .= $indent . '<li class="' . $css_class . '"><a href="' . get_permalink($page->ID) . '">' . $link_before . $title . $link_after . '</a>';

		if ( !empty($show_date) ) {
			if ( 'modified' == $show_date )
				$time = $page->post_modified;
			else
				$time = $page->post_date;

			$output .= " " . mysql2date($date_format, $time);
		}
		
		$output = apply_filters( 'graphene_page_description_walker_output', $output, $page, $depth, $args, $current_page );
	}
}


/*
 * Adds a menu-item-ancestor class to menu items with children when using custom menu
 *
 * @package Graphene
 * @since Graphene 1.8
*/
function graphene_menu_item_ancestor( $items ) {

    foreach ( $items as $item ) {
        if ( graphene_menu_has_sub( $item->ID, $items ) ) {
            $item->classes[] = 'menu-item-ancestor';
        }
    }
    return $items;    
}
add_filter( 'wp_nav_menu_objects', 'graphene_menu_item_ancestor' );


/**
 * Check if a Custom Menu menu item has sub-menu item
 *
 * @package Graphene
 * @since Graphene 1.8
 */
function graphene_menu_has_sub( $menu_item_id, &$items ) {
	foreach ( $items as $item ) {
		if ( $item->menu_item_parent && $item->menu_item_parent==$menu_item_id ) {
			return true;
		}
	}
	return false;
};


/*
 * Adds a menu-item-ancestor class to menu items with children when using default menu
 *
 * @package Graphene
 * @since Graphene 1.8
*/
function graphene_page_ancestor_class( $css_class, $page, $depth, $args ) {
    if ( ! empty( $args['has_children'] ) )
        $css_class[] = 'menu-item-ancestor';
	
	if ( array_search( 'current_page_ancestor', $css_class ) ) $css_class[] = 'current-menu-ancestor';
	if ( array_search( 'current_page_parent', $css_class ) ) $css_class[] = 'current-menu-ancestor';
	if ( array_search( 'current_page_item', $css_class ) ) $css_class[] = 'current-menu-item';
	
    return $css_class;
}
add_filter( 'page_css_class', 'graphene_page_ancestor_class', 10, 4 );


/**
 * Add additional navigation menu class
 */
function graphene_get_menu_class( $menu_class ){
    global $graphene_settings;
    
    // if the search box is located in the navigation bar restrict the width of the menu to 12 grid columns
    if ( ( $search_box_location = $graphene_settings['search_box_location']) && $search_box_location == 'nav_bar' ){
        $menu_class .= ' grid_12';
    }
    
    return apply_filters( 'graphene_menu_class', $menu_class );
}