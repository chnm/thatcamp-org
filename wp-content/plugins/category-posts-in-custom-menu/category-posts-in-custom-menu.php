<?php
/*
    Plugin Name: Category Posts in Custom Menu
    Plugin URI: http://blog.dianakoenraadt.nl
    Description: This plugin replaces selected Category links / Post Tag links / Custom taxonomy links in a Custom Menu by a list of their posts/pages.
    Version: 0.8
    Author: Diana Koenraadt
    Author URI: http://www.dianakoenraadt.nl
    License: GPL2
*/

/*  Copyright 2012 Diana Koenraadt (email : diana at dianakoenraadt dot nl)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once( ABSPATH . 'wp-admin/includes/nav-menu.php' ); // Load all the nav menu interface functions

new CPCM_Manager;

class CPCM_Manager {

	function CPCM_Manager()
	{
		$this->__construct();

	} // function

	function __construct()
	{
		add_action( 'admin_enqueue_scripts', array( &$this, 'cpmp_wp_admin_nav_menus_css' ) );
        add_filter( 'wp_edit_nav_menu_walker', array( &$this, 'cpcm_edit_nav_menu_walker' ), 1, 2 );
        add_filter( 'wp_nav_menu_objects', array( &$this, 'cpcm_replace_taxonomy_by_posts' ), 1, 2 );
        add_action( 'wp_update_nav_menu_item', array( &$this, 'cpcm_update_nav_menu_item' ), 1, 3 );  
	} // function

        static function cpmp_uninstall() {
            // We're uninstalling, so delete all custom fields on nav_menu_items that the CPCM plugin added
            $all_nav_menu_items = get_posts('numberposts=-1&post_type=nav_menu_item&post_status=any');

            foreach( $all_nav_menu_items as $nav_menu_item) {
                delete_post_meta($nav_menu_item->ID, 'cpcm-unfold');
                delete_post_meta($nav_menu_item->ID, 'cpcm-orderby');
                delete_post_meta($nav_menu_item->ID, 'cpcm-order');
                delete_post_meta($nav_menu_item->ID, 'cpcm-item-count');
                delete_post_meta($nav_menu_item->ID, 'cpcm-item-titles');
				delete_post_meta($nave_menu_item->ID, 'cpcm-remove-original-item');
            }
        } // function

        /* 
        * Add CSS for div.cpmp-description to nav-menus.php
        */
        function cpmp_wp_admin_nav_menus_css($hook){
            // Check the hook so that the .css is only added to the .php file where we need it
            if( 'nav-menus.php' != $hook )
                    return;
            wp_register_style( 'cpmp_wp_admin_nav_menus_css', plugins_url( 'cpmp_wp_admin_nav_menus.css' , __FILE__ ) );
            wp_enqueue_style( 'cpmp_wp_admin_nav_menus_css' );
        } // function

        /*
        * Extend Walker_Nav_Menu_Edit and use the extended class (CPCM_Walker_Nav_Menu_Edit) to add controls to nav-menus.php, 
        * specifically a div is added with class="cpmp-description". Everything else in this extended class is unchanged with 
        * respect to the parent class.
        *
        * Note that this extension of Walker_Nav_Menu_Edit is required because there are no hooks in its start_el method.
        * If hooks are provided in later versions of wordpress, the plugin needs to be updated to use these hooks, that would be 
        * much better.
        */
        function cpcm_edit_nav_menu_walker( $walker_name, $menu_id ) {
            if ( class_exists ( 'CPCM_Walker_Nav_Menu_Edit' ) ) {
                    return 'CPCM_Walker_Nav_Menu_Edit';
            }
            return 'Walker_Nav_Menu_Edit';
        } // function

		function replace_placeholders( $post, $string )
		{
			$custom_field_keys = get_post_custom_keys($post->ID);
			foreach ( (array)$custom_field_keys as $key => $value ) {
				$valuet = trim($value);
				if ( '_' == $valuet{0} )
				continue;
				$meta = get_post_meta($post->ID, $valuet, true);
				$valuet_str = str_replace(' ', '_', $valuet);
				// Check if post_myfield occurs
				if (substr_count($string, "%post_" . $valuet_str) > 0)
				{
					if (is_string($meta))
					{
						$string = str_replace( "%post_" . $valuet_str, $meta, $string);
					}
				}
			}
			
			$userdata = get_userdata($post->post_author);
			$string = str_replace( "%post_author", 	$userdata ? $userdata->data->display_name : '', $string);

			$featured_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
			$string = str_replace( "%post_feat_image", 	$featured_image, $string);

			$string = str_replace( "%post_title", 	$post->post_title, 	$string);
			$string = str_replace( "%post_excerpt", 	$post->post_excerpt, 	$string);
			$string = str_replace( "%post_url", 	get_permalink($post->ID), 	$string);

			$post_date_gmt = $post->post_date_gmt;
			$string = preg_replace("/\%post_date_gmt\(\)/", mysql2date('F jS, Y', $post_date_gmt), $string);
			$string = preg_replace("/\%post_date_gmt\(([a-zA-Z\s\\\\:,]*)\)/e", "mysql2date('$1', '$post_date_gmt')", $string);
			$string = str_replace( "%post_date_gmt", 	$post_date_gmt, 	$string);

			$post_date = $post->post_date;
			$string = preg_replace("/\%post_date\(\)/", mysql2date('F jS, Y', $post_date), $string);
			$string = preg_replace("/\%post_date\(([a-zA-Z\s\\\\:,]*)\)/e", "mysql2date('$1', '$post_date')", $string);
			$string = str_replace( "%post_date", 	$post_date, 	$string);

			$string = str_replace( "%post_status", 	$post->post_status, 	$string);

			$post_modified_gmt = $post->post_modified_gmt;
			$string = preg_replace("/\%post_modified_gmt\(\)/", mysql2date('F jS, Y', $post_modified_gmt), $string);
			$string = preg_replace("/\%post_modified_gmt\(([a-zA-Z\s\\\\:,]*)\)/e", "mysql2date('$1', '$post_modified_gmt')", $string);
			$string = str_replace( "%post_modified_gmt", 	$post_modified_gmt, 	$string);

			$post_modified = $post->post_modified;
			$string = preg_replace("/\%post_modified\(\)/", mysql2date('F jS, Y', $post_modified), $string);
			$string = preg_replace("/\%post_modified\(([a-zA-Z\s\\\\:,]*)\)/e", "mysql2date('$1', '$post_modified')", $string);
			$string = str_replace( "%post_modified", 	$post_modified, 	$string);

			$string = str_replace( "%post_comment_count", 	$post->comment_count, 	$string);
			
			// Remove remaining %post_ occurrences.
			$pattern = "/" . "((\((?P<lbrack>(\S*))))?" . "\%post_\w+(?P<brackets>(\(((?P<inner>[^\(\)]*)|(?P>brackets))\)))" . "(((?P<rbrack>(\S*))\)))?" . "/";
			$string = preg_replace($pattern, '', $string);
			
			$pattern = "/%post_\w+(?P<brackets>(\(((?P<inner>[^\(\)]*)|(?P>brackets))\)))?/";
			$string = preg_replace($pattern, '', $string);			
			
			$pattern = "/%post_\w+(\(\w*\))?/"; 
			$string = preg_replace($pattern, '', $string);
			
			return $string;
		}
		
        /* 
        * Build the menu structure for display: Augment taxonomies (category, tags or custom taxonomies) that have been marked as such, by their posts. Optionally: remove original menu item.
        */
        function cpcm_replace_taxonomy_by_posts( $sorted_menu_items, $args ) {
	        $result = array();    
	        $inc = 0;
	        foreach ( (array) $sorted_menu_items as $key => $menu_item ) {
                // Augment taxonomy object with a list of its posts: Append posts to $result
                // Optional: Remove the taxonomy object/original menu item itself.
                if ( $menu_item->type == 'taxonomy' && (get_post_meta($menu_item->db_id, "cpcm-unfold", true) == '1')) {					
					$query_arr = array();

					$query_arr['tax_query'] = array(array('taxonomy'=>$menu_item->object,
					'field'=>'id',
					'terms'=>$menu_item->object_id
					));

					// If cpcm-unfold is true, the following custom fields exist:
					$query_arr['order'] = get_post_meta($menu_item->db_id, "cpcm-order", true);
					$query_arr['orderby'] = get_post_meta($menu_item->db_id, "cpcm-orderby", true);
					$query_arr['numberposts'] = get_post_meta($menu_item->db_id, "cpcm-item-count", true); // default value of -1 returns all posts

					// Support for custom post types
					$tag = get_taxonomy($menu_item->object);
					$query_arr['post_type'] = $tag->object_type;

					$posts = get_posts( $query_arr );
					
					// Decide whether the original item needs to be preserved.
					$remove_original_item = get_post_meta($menu_item->db_id, "cpcm-remove-original-item", true);
					$menu_item_parent = $menu_item->menu_item_parent;
					switch ($remove_original_item) {
						case "always":
							$inc += -1;
							break;
						case "only if empty":
							if (empty($posts))
							{
								$inc += -1;
							}
							else 
							{
								array_push($result,$menu_item);
								$menu_item_parent = $menu_item->db_id;
							}
							break;
						case "never":
							array_push($result,$menu_item);
							$menu_item_parent = $menu_item->db_id;
							break;
					}

					foreach( (array) $posts as $pkey => $post ) {
						// Decorate the posts with the required data for a menu-item.
						$post = wp_setup_nav_menu_item( $post );
						$post->menu_item_parent = $menu_item_parent; // Set to parent of taxonomy item.

						// Transfer properties from the old menu item to the new one
						$post->target = $menu_item->target;
						$post->classes = $menu_item->classes;
						$post->xfn = $menu_item->xfn;
						$post->description = $menu_item->description;

						// Set the title of the new menu item
						$post->title = get_post_meta($menu_item->db_id, "cpcm-item-titles", true);

						// Replace the placeholders in the title by the properties of the post
						$post->title = $this->replace_placeholders($post, $post->title);

						$inc += 1;
					}
					// Extend the items with classes.
					_wp_menu_item_classes_by_context( $posts );
					// Append the new menu_items to the menu array that we're building.
					$result = array_merge( $result, $posts );
				} else {
					// Treat other objects as usual, but note that the position 
					// of elements in the array changes.
					$result[$menu_item->menu_order + $inc] = $menu_item;
				}
            }

            unset( $sorted_menu_items );
            return $result;
        } // function

        /*
        * Store the entered data in nav-menus.php by inspecting the $_POST variable again.
        */
        function cpcm_update_nav_menu_item( $menu_id = 0, $menu_item_db_id = 0, $menu_item_data = array() ) {
            // Only inspect the values if the $_POST variable contains data (the wp_update_nav_menu_item filter is applied in three other places, without a $_POST action)
            if ( ! empty( $_POST['menu-item-db-id'] ) ) {
                update_post_meta( $menu_item_db_id, 'cpcm-unfold', (!empty( $_POST['menu-item-cpcm-unfold'][$menu_item_db_id]) ) );
                update_post_meta( $menu_item_db_id, 'cpcm-orderby', (empty( $_POST['menu-item-cpcm-orderby'][$menu_item_db_id]) ? "none" : $_POST['menu-item-cpcm-orderby'][$menu_item_db_id]) );
                update_post_meta( $menu_item_db_id, 'cpcm-order', (empty( $_POST['menu-item-cpcm-order'][$menu_item_db_id]) ? "DESC" : $_POST['menu-item-cpcm-order'][$menu_item_db_id]) );
                update_post_meta( $menu_item_db_id, 'cpcm-item-count', (int) (empty( $_POST['menu-item-cpcm-item-count'][$menu_item_db_id]) ? "-1" : $_POST['menu-item-cpcm-item-count'][$menu_item_db_id]) );
                update_post_meta( $menu_item_db_id, 'cpcm-item-titles', (empty( $_POST['menu-item-cpcm-item-titles'][$menu_item_db_id]) ? "%post_title" : $_POST['menu-item-cpcm-item-titles'][$menu_item_db_id]) );
                update_post_meta( $menu_item_db_id, 'cpcm-remove-original-item', (empty( $_POST['menu-item-cpcm-remove-original-item'][$menu_item_db_id]) ? "always" : $_POST['menu-item-cpcm-remove-original-item'][$menu_item_db_id]) );
            } // if 
        } // function

} // class

class CPCM_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit  {
	function start_el(&$output, $item, $depth, $args) {
        global $_wp_nav_menu_max_depth;
		$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		ob_start();
		$item_id = esc_attr( $item->ID );
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);

		$original_title = '';
		if ( $item->type == 'taxonomy' ) {
			$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
			if ( is_wp_error( $original_title ) )
				$original_title = false;
		} elseif ( 'post_type' == $item->type ) {
			$original_object = get_post( $item->object_id );
			$original_title = $original_object->post_title;
		}

		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $item->object ),
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
		);

		$title = $item->title;

		if ( ! empty( $item->_invalid ) ) {
			$classes[] = 'menu-item-invalid';
			/* translators: %s: title of menu item which is invalid */
			$title = sprintf( __( '%s (Invalid)' ), $item->title );
		} elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
			$classes[] = 'pending';
			/* translators: %s: title of menu item in draft status */
			$title = sprintf( __('%s (Pending)'), $item->title );
		}

		$title = empty( $item->label ) ? $title : $item->label;

		?>
		<li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes ); ?>">
			<dl class="menu-item-bar">
				<dt class="menu-item-handle">
					<span class="item-title"><?php echo esc_html( $title ); ?></span>
					<span class="item-controls">
						<span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
						<span class="item-order hide-if-js">
							<a href="<?php
								echo wp_nonce_url(
									add_query_arg(
										array(
											'action' => 'move-up-menu-item',
											'menu-item' => $item_id,
										),
										remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
									),
									'move-menu_item'
								);
							?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up'); ?>">&#8593;</abbr></a>
							|
							<a href="<?php
								echo wp_nonce_url(
									add_query_arg(
										array(
											'action' => 'move-down-menu-item',
											'menu-item' => $item_id,
										),
										remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
									),
									'move-menu_item'
								);
							?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down'); ?>">&#8595;</abbr></a>
						</span>
						<a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php esc_attr_e('Edit Menu Item'); ?>" href="<?php
							echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
						?>"><?php _e( 'Edit Menu Item' ); ?></a>
					</span>
				</dt>
			</dl>

			<div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
				<?php if( 'custom' == $item->type ) : ?>
					<p class="field-url description description-wide">
						<label for="edit-menu-item-url-<?php echo $item_id; ?>">
							<?php _e( 'URL' ); ?><br />
							<input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
						</label>
					</p>
				<?php endif; ?>
				<p class="description description-thin">
					<label for="edit-menu-item-title-<?php echo $item_id; ?>">
						<?php _e( 'Navigation Label' ); ?><br />
						<input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
					</label>
				</p>
				<p class="description description-thin">
					<label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
						<?php _e( 'Title Attribute' ); ?><br />
						<input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
					</label>
				</p>
				<p class="field-link-target description">
					<label for="edit-menu-item-target-<?php echo $item_id; ?>">
						<input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank" name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
						<?php _e( 'Open link in a new window/tab' ); ?>
					</label>
				</p>
				<p class="field-css-classes description description-thin">
					<label for="edit-menu-item-classes-<?php echo $item_id; ?>">
						<?php _e( 'CSS Classes (optional)' ); ?><br />
						<input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
					</label>
				</p>
				<p class="field-xfn description description-thin">
					<label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
						<?php _e( 'Link Relationship (XFN)' ); ?><br />
						<input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
					</label>
				</p>
				<p class="field-description description description-wide">
					<label for="edit-menu-item-description-<?php echo $item_id; ?>">
						<?php _e( 'Description' ); ?><br />
						<textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
						<span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.'); ?></span>
					</label>
				</p>

                <?php /* BEGIN CATEGORY POSTS IN CUSTOM MENU */ if( $item->type == 'taxonomy' ) : ?>
                    <div class="cpmp-description">
                        <p class="field-cpcm-unfold description description-wide">
                            <label for="edit-menu-item-cpcm-unfold-<?php echo $item_id; ?>">
                                <input type="checkbox" id="edit-menu-item-cpcm-unfold-<?php echo $item_id; ?>" class="edit-menu-item-cpcm-unfold" name="menu-item-cpcm-unfold[<?php echo $item_id; ?>]" <?php checked( get_post_meta($item_id, "cpcm-unfold", true), true )  ?> /> Create submenu containing links to posts<?php if ('Category' == $item->type_label) echo ' in this category'; else if (('Tag' == $item->type_label) || ('Post Tag' == $item->type_label)) echo ' with this tag'; else echo ' in this taxonomy'; ?>.
                            </label>
                        </p>
                        <p class="field-cpcm-item-count description description-thin">
                            <label for="edit-menu-item-cpcm-item-count-<?php echo $item_id; ?>">
                                <?php _e( 'Number of Posts' ); ?><br />
                                <input type="text" id="edit-menu-item-cpcm-item-count-<?php echo $item_id; ?>" class="widefat code edit-menu-item-cpcm-item-count" name="menu-item-cpcm-item-count[<?php echo $item_id; ?>]" value="<?php $item_count = get_post_meta($item_id, "cpcm-item-count", true); echo $item_count != '' ? $item_count : '-1'; ?>" />
                            </label>
                        </p>
                        <p class="field-cpcm-orderby description description-thin">
                            <label for="edit-menu-item-cpcm-orderby-<?php echo $item_id; ?>">
                                <?php _e( 'Order By' ); ?><br />
                                <select id="edit-menu-item-cpcm-orderby-<?php echo $item_id; ?>" class="widefat edit-menu-item-cpcm-orderby" name="menu-item-cpcm-orderby[<?php echo $item_id; ?>]">
                                    <option value="none" <?php selected( get_post_meta($item_id, "cpcm-orderby", true), "none" )  ?>><?php _e('None'); ?></option>
                                    <option value="ID" <?php selected( get_post_meta($item_id, "cpcm-orderby", true), "ID" )  ?>><?php _e('ID'); ?></option>
                                    <option value="author" <?php selected( get_post_meta($item_id, "cpcm-orderby", true), "author" )  ?>><?php _e('Author'); ?></option>
                                    <option value="title" <?php selected( get_post_meta($item_id, "cpcm-orderby", true), "title" )  ?>><?php _e('Title'); ?></option>
                                    <option value="date" <?php selected( get_post_meta($item_id, "cpcm-orderby", true), "date" )  ?>><?php _e('Date'); ?></option>
                                    <option value="modified" <?php selected( get_post_meta($item_id, "cpcm-orderby", true), "modified" )  ?>><?php _e('Last Modified'); ?></option>
                                    <option value="parent" <?php selected( get_post_meta($item_id, "cpcm-orderby", true), "parent" )  ?>><?php _e('Post/Page Parent ID'); ?></option>
                                    <option value="rand" <?php selected( get_post_meta($item_id, "cpcm-orderby", true), "rand" )  ?>><?php _e('Random Order'); ?></option>
                                    <option value="comment_count" <?php selected( get_post_meta($item_id, "cpcm-orderby", true), "comment_count" )  ?>><?php _e('Number of Comments'); ?></option>
                                </select>
                            </label>
                        </p>
                        <p class="field-cpcm-order description description-thin">
                            <label for="edit-menu-item-cpcm-order-<?php echo $item_id; ?>">
                                <?php _e( 'Order' ); ?><br />
                                <select id="edit-menu-item-cpcm-order-<?php echo $item_id; ?>" class="widefat edit-menu-item-cpcm-order" name="menu-item-cpcm-order[<?php echo $item_id; ?>]">
                                    <option value="DESC" <?php selected( get_post_meta($item_id, "cpcm-order", true), "DESC" )  ?>><?php _e('Descending'); ?></option>
                                    <option value="ASC" <?php selected( get_post_meta($item_id, "cpcm-order", true), "ASC" )  ?>><?php _e('Ascending'); ?></option>
                                </select>
                            </label>
                        </p>
                        <p class="field-cpcm-remove-original-item description description-thin">
                            <label for="edit-menu-item-cpcm-remove-original-item-<?php echo $item_id; ?>">
                                <?php _e( 'Remove original menu item' ); ?><br />
                                <select id="edit-menu-item-cpcm-remove-original-item-<?php echo $item_id; ?>" class="widefat edit-menu-item-cpcm-remove-original-item" name="menu-item-cpcm-remove-original-item[<?php echo $item_id; ?>]">
                                    <option value="always" <?php selected( get_post_meta($item_id, "cpcm-remove-original-item", true), "always" )  ?>><?php _e('Always'); ?></option>
                                    <option value="only if empty" <?php selected( get_post_meta($item_id, "cpcm-remove-original-item", true), "only if empty" )  ?>><?php _e('Only if there are no posts'); ?></option>
                                    <option value="never" <?php selected( get_post_meta($item_id, "cpcm-remove-original-item", true), "never" )  ?>><?php _e('Never'); ?></option>
                                </select>
                            </label>
						</p>
                        <p class="field-cpcm-item-titles description description-wide">
                            <label for="edit-menu-item-cpcm-item-titles-<?php echo $item_id; ?>">
                                <?php _e( 'Post Navigation Label' ); ?><br />
                                <textarea id="edit-menu-item-cpcm-item-titles-<?php echo $item_id; ?>" class="widefat code edit-menu-item-cpcm-item-titles" name="menu-item-cpcm-item-titles[<?php echo $item_id; ?>]" rows="4"><?php $item_titles = get_post_meta($item_id, "cpcm-item-titles", true); echo $item_titles != '' ? esc_attr( $item_titles ) : '%post_title' ?></textarea>
                                <span class="description"><?php _e('The navigation label for generated post links may be customized using wildcars such as: %post_title, %post_author, %post_my_field (for custom field \'my field\' or \'my_field\'). See documentation.'); ?></span>
                            </label>
                        </p>
                    </div>
				<?php endif; /* CATEGORY POSTS IN CUSTOM MENU END */ ?>

				<div class="menu-item-actions description-wide submitbox">
					<?php if( 'custom' != $item->type && $original_title !== false ) : ?>
						<p class="link-to-original">
							<?php printf( __('Original: %s'), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
						</p>
					<?php endif; ?>
					<a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
					echo wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'delete-menu-item',
								'menu-item' => $item_id,
							),
							remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
						),
						'delete-menu_item_' . $item_id
					); ?>"><?php _e('Remove'); ?></a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-<?php echo $item_id; ?>" href="<?php	echo esc_url( add_query_arg( array('edit-menu-item' => $item_id, 'cancel' => time()), remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) ) ) );
						?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Cancel'); ?></a>
				</div>

				<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
				<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
				<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
				<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
				<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
				<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
			</div><!-- .menu-item-settings-->
			<ul class="menu-item-transport"></ul>
		<?php
		$output .= ob_get_clean();
	} // function
} // class

// Register the uninstall hook. Should be done after the class has been defined.
register_uninstall_hook( __FILE__, array( 'CPCM_Manager', 'cpmp_uninstall' ) );

?>
