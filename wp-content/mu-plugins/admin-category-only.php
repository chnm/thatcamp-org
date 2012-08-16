<?php
/*
Plugin Name: Admin category Only
Plugin URI: http://deliverthemessage.net
Description: Hide categories you choose from all role but admin the write panel
Version: 2.3a
Author: Kevin Lanteri & Olly Benson
Author URI: http://deliverthemessage.net
*/

/*Admin only category*/
function aoc_remove_cat_box() {
	remove_meta_box( 'categorydiv' , 'post' , 'normal' ); 
}

add_action( 'admin_menu' , 'aoc_remove_cat_box' );

function aoc_custom_post_categories_meta_box( $post, $box ) {
	global $post, $box;
	$defaults = array('taxonomy' => 'category');
	if ( !isset($box['args']) || !is_array($box['args']) )
		$args = array();
	else
		$args = $box['args'];
	extract( wp_parse_args($args, $defaults), EXTR_SKIP );
	$tax = get_taxonomy($taxonomy);

	?>
	<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
		<ul id="<?php echo $taxonomy; ?>-tabs" class="category-tabs">
			<li class="tabs"><a href="#<?php echo $taxonomy; ?>-all" tabindex="3"><?php echo $tax->labels->all_items; ?></a></li>
		</ul>

		<div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
			<?php
            $name = ( $taxonomy == 'category' ) ? 'post_category' : 'tax_input[' . $taxonomy . ']';
            echo "<input type='hidden' name='{$name}[]' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
            ?>
			<ul id="<?php echo $taxonomy; ?>checklist" class="list:<?php echo $taxonomy?> categorychecklist form-no-clear">
            
            
            <?php				
				global $user_ID, $current_user;
        
        // $current_role = $current_user->caps;
        
        
        $categories=  get_categories('hide_empty=0');
				
        
//
//  THIS WAS HACKED BECAUSE CAPABILITIES WAS USING A MORE COMPLICATED/REDUNDANT METHOD TO GET ROLES
//
				$capabilities = $current_user->caps;
				
				if($capabilities):
				foreach($capabilities as $k => $v):
					$current_role = $k;
				endforeach;
				endif;
        
					foreach ($categories as $c):
				if (!$c->parent) :
        if(get_option('cant_access_'.$c->cat_ID) && $current_role):
				$righteous = json_decode(get_option('cant_access_'.$c->cat_ID));
        if(!in_array( $current_role , $righteous )):
						wp_category_checklist($post->ID, $c->cat_ID, $_POST['post_category'], false, null, true);
						endif;
				else:
						wp_category_checklist($post->ID, $c->cat_ID, $_POST['post_category'], false, null, true);
				endif;
        endif;
				endforeach;
				

			 ?>   
			</ul>
		</div>
	<?php if ( !current_user_can($tax->cap->assign_terms) ) : ?>
	<p><em><?php _e('You cannot modify this taxonomy.'); ?></em></p>
	<?php endif; ?>
	</div>
	<?php
}

function aoc_custom_cat_mbox(){
	global $post;
	$tax_name = get_object_taxonomies('post');
	add_meta_box('catdiv', __('Secured Categories'), 'aoc_custom_post_categories_meta_box', 'post', 'side', 'core', array( 'taxonomy' => $tax_name ));	
}
add_action('admin_menu', 'aoc_custom_cat_mbox');


function aoc_custom_cat_assignation($tag){
//
// THIS WAS HACKED BECAUSE YOU CAN'T EXCLUDE CHILD CATEGORIES
//
  if(current_user_can('level_10') && !$tag->parent):
	?>
		<tr>
        	<th>Choose role(s) who will <strong>not</strong> be able to add this category to posts:</th>
        		<td>
                <?php
        			$editable_roles = get_editable_roles();
					
					$option = get_option('cant_access_'.$_GET['tag_ID']);
					$cat_association = json_decode(get_option('cant_access_'.$_GET['tag_ID']));
          foreach($editable_roles as $role):
						if(key($role['capabilities']) != 'switch_themes'):
						  echo '<input type="checkbox" name="cat_access[]" value="'.strtolower($role['name']).'" ';
//
// THIS WAS HACKED BECAUSE OF A TYPO - IT SAYS 'CAN_ACCESS_'
//
						  if(get_option('cant_access_'.$_GET['tag_ID'])):
								if(is_array($cat_association)):
									if(in_array(strtolower($role['name']) , $cat_association ))	echo 'checked="checked"';
								endif;
						  endif;
						echo '/> '.$role['name']."\r\n".'<br />';
						endif;
					endforeach;
				?>
        		</td>
        </tr>
        <?php
		endif;
    
    if ($tag->parent) echo "<tr><td colspan='2'>You can not exclude child categories individually.</td></tr>";
    
}


add_action('edit_category_form_fields', 'aoc_custom_cat_assignation'); 

function aoc_custom_assignation_save(){
	global $tag_ID,$tag;

	if($_POST['cat_access']):
		update_option( 'cant_access_'.$_POST['tag_ID'], json_encode($_POST['cat_access']) );
	endif;
					
			
}

add_action('edit_category', 'aoc_custom_assignation_save'); 
