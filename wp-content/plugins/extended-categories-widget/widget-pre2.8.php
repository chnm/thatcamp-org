<?php

function widget_extended_categories_init ()
{
	// Widgets exists?
	if (! function_exists('wp_register_sidebar_widget') || ! function_exists('wp_register_widget_control')) {
		return;
	}

	function widget_extended_categories ($args, $number = 1)
	{
		$version = '3.6.4';
		// Check for version
		require (ABSPATH . WPINC . '/version.php');
		if (version_compare($wp_version, '2.5.1', '<')) {
			$avh_extcat_canselectcats = false;
		} else {
			$avh_extcat_canselectcats = true;
		}
		extract($args);
		$options = get_option('widget_extended_categories');
		$c = $options[$number]['count'] ? '1' : '0';
		$h = $options[$number]['hierarchical'] ? '1' : '0';
		$e = $options[$number]['hide_empty'] ? '1' : '0';
		$s = $options[$number]['sort_column'] ? $options[$number]['sort_column'] : 'name';
		$o = $options[$number]['sort_order'] ? $options[$number]['sort_order'] : 'asc';
		$r = $options[$number]['rssfeed'] ? 'RSS' : '';
		$i = $options[$number]['rssimage'] ? $options[$number]['rssimage'] : '';
		if (empty($r)) {
			$i = '';
		}
		
		$title = empty($options[$number]['title']) ? __('Categories') : attribute_escape($options[$number]['title']);
		$style = empty($options[$number]['style']) ? 'list' : $options[$number]['style'];
		if ($avh_extcat_canselectcats) {
			if ($options[$number]['post_category']) {
				$post_category = unserialize($options[$number]['post_category']);
				$included_cats = implode(",", $post_category);
			}
			$cat_args = array('include'=>$included_cats, 'orderby'=>$s, 'order'=>$o, 'show_count'=>$c, 'hide_empty'=>$e, 'hierarchical'=>$h, 'title_li'=>'', 'show_option_none'=>__('Select Category'), 'feed'=>$r, 'feed_image'=>$i, 'name'=>'ec-cat-' . $number, 'depth'=>2);
		} else {
			$cat_args = array('orderby'=>$s, 'order'=>$o, 'show_count'=>$c, 'hide_empty'=>$e, 'hierarchical'=>$h, 'title_li'=>'', 'show_option_none'=>__('Select Category'), 'feed'=>$r, 'feed_image'=>$i, 'name'=>'ec-cat-' . $number);
		}
		echo $before_widget;
		echo '<!-- AVH Extended Categories version ' . $version . ' | http://blog.avirtualhome.com/wordpress-plugins/ -->';
		echo $before_title . $title . $after_title;
		
		if ($style == 'list') {
			echo '<ul>';
			wp_list_categories($cat_args);
			echo '</ul>';
		} else {
			wp_dropdown_categories($cat_args);
			?>
<script lang='javascript'><!--
                        var ec_dropdown_<?php
			echo ($number);
			?> = document.getElementById('ec-cat-<?php
			echo ($number);
			?>');
                        function ec_onCatChange_<?php
			echo ($number);
			?>() {
                            if ( ec_dropdown_<?php
			echo ($number);
			?>.options[ec_dropdown_<?php
			echo ($number);
			?>.selectedIndex].value > 0 ) {
                                location.href = "<?php
			echo get_option('home');
			?>/?cat="+ec_dropdown_<?php
			echo ($number);
			?>.options[ec_dropdown_<?php
			echo ($number);
			?>.selectedIndex].value;
                            }
                        }
                        ec_dropdown_<?php
			echo ($number);
			?>.onchange = ec_onCatChange_<?php
			echo ($number);
			?>;
--></script>
<?php
		}
		echo $after_widget;
	}

	function widget_extended_categories_control ($number = 1)
	{
		// Check for version
		require (ABSPATH . WPINC . '/version.php');
		if (version_compare($wp_version, '2.5.1', '<')) {
			$avh_extcat_canselectcats = false;
		} else {
			$avh_extcat_canselectcats = true;
		}
		// Get actual options
		$options = $newoptions = get_option('widget_extended_categories');
		if (! is_array($options)) {
			$options = $newoptions = array();
		}
		// Post to new options array
		

		if ($_POST['categories-submit-' . $number]) {
			$newoptions[$number]['title'] = strip_tags(stripslashes($_POST['categories-title-' . $number]));
			$newoptions[$number]['count'] = isset($_POST['categories-count-' . $number]);
			$newoptions[$number]['hierarchical'] = isset($_POST['categories-hierarchical-' . $number]);
			$newoptions[$number]['hide_empty'] = isset($_POST['categories-hide_empty-' . $number]);
			$newoptions[$number]['sort_column'] = strip_tags(stripslashes($_POST['categories-sort_column-' . $number]));
			$newoptions[$number]['sort_order'] = strip_tags(stripslashes($_POST['categories-sort_order-' . $number]));
			$newoptions[$number]['style'] = strip_tags(stripslashes($_POST['categories-style-' . $number]));
			$newoptions[$number]['rssfeed'] = isset($_POST['categories-rssfeed-' . $number]);
			$newoptions[$number]['rssimage'] = attribute_escape($_POST['categories-rssimage-' . $number]);
			if ($avh_extcat_canselectcats) {
				if (in_array('-1', $_POST['post_category-' . $number], true)) {
					$newoptions[$number]['post_category'] = false;
				} else {
					$newoptions[$number]['post_category'] = serialize($_POST['post_category-' . $number]);
				}
			}
		
		}
		
		// Update if new options
		if ($options != $newoptions) {
			$options = $newoptions;
			update_option('widget_extended_categories', $options);
		}
		
		// Prepare data for display
		$title = htmlspecialchars($options[$number]['title'], ENT_QUOTES);
		$count = $options[$number]['count'] ? 'checked="checked"' : '';
		$hierarchical = $options[$number]['hierarchical'] ? 'checked="checked"' : '';
		$hide_empty = $options[$number]['hide_empty'] ? 'checked="checked"' : '';
		$sort_id = ($options[$number]['sort_column'] == 'ID') ? ' SELECTED' : '';
		$sort_name = ($options[$number]['sort_column'] == 'name') ? ' SELECTED' : '';
		$sort_count = ($options[$number]['sort_column'] == 'count') ? ' SELECTED' : '';
		$sort_order_a = ($options[$number]['sort_order'] == 'asc') ? ' SELECTED' : '';
		$sort_order_d = ($options[$number]['sort_order'] == 'desc') ? ' SELECTED' : '';
		$style_list = ($options[$number]['style'] == 'list') ? ' SELECTED' : '';
		$style_drop = ($options[$number]['style'] == 'drop') ? ' SELECTED' : '';
		$rssfeed = $options[$number]['rssfeed'] ? 'checked="checked"' : '';
		$rssimage = htmlspecialchars($options[$number]['rssimage'], ENT_QUOTES);
		if ($avh_extcat_canselectcats) {
			$selected_cats = ($options[$number]['post_category'] != '') ? unserialize($options[$number]['post_category']) : false;
		}
		?>
<div><label for="categories-title-<?php
		echo $number;
		?>"><?php
		_e('Title:');
		?>
	<input style="width: 250px;"
	id="categories-title-<?php
		echo $number;
		?>"
	name="categories-title-<?php
		echo $number;
		?>" type="text"
	value="<?php
		echo $title;
		?>" /> </label> <label
	for="categories-count-<?php
		echo $number;
		?>"
	style="line-height: 35px; display: block;">Show post counts <input
	class="checkbox" type="checkbox" <?php
		echo $count;
		?>
	id="categories-count-<?php
		echo $number;
		?>"
	name="categories-count-<?php
		echo $number;
		?>" /> </label> <label
	for="categories-hierarchical"
	style="line-height: 35px; display: block;">Show hierarchy <input
	class="checkbox" type="checkbox" <?php
		echo $hierarchical;
		?>
	id="categories-hierarchical-<?php
		echo $number;
		?>"
	name="categories-hierarchical-<?php
		echo $number;
		?>" /> </label> <label
	for="categories-hide_empty-<?php
		echo $number;
		?>"
	style="line-height: 35px; display: block;">Hide empty categories <input
	class="checkbox" type="checkbox" <?php
		echo $hide_empty;
		?>
	id="categories-hide_empty-<?php
		echo $number;
		?>"
	name="categories-hide_empty-<?php
		echo $number;
		?>" /> </label> <label
	for="categories-sort_column-<?php
		echo $number;
		?>"
	style="line-height: 35px; display: block;">Sort by <select
	id="categories-sort_column-<?php
		echo $number;
		?>"
	name="categories-sort_column-<?php
		echo $number;
		?>">
	<option value="ID" <?php
		echo $sort_id?>>ID</option>
	<option value="name" <?php
		echo $sort_name?>>Name</option>
	<option value="count" <?php
		echo $sort_count?>>Count</option>
</select> </label> <label
	for="categories-sort_order-<?php
		echo $number;
		?>"
	style="line-height: 35px; display: block;">Sort order <select
	id="categories-sort_order-<?php
		echo $number;
		?>"
	name="categories-sort_order-<?php
		echo $number;
		?>">
	<option value="asc" <?php
		echo $sort_order_a?>>Ascending</option>
	<option value="desc" <?php
		echo $sort_order_d?>>Descending</option>
</select> </label> <label for="categories-style-<?php
		echo $number;
		?>"
	style="line-height: 35px; display: block;">Display style <select
	id="categories-style-<?php
		echo $number;
		?>"
	name="categories-style-<?php
		echo $number;
		?>">
	<option value='list' <?php
		echo $style_list;
		?>>List</option>
	<option value='drop' <?php
		echo $style_drop;
		?>>Drop down</option>
</select> </label> <label
	for="categories-rssfeed-<?php
		echo $number;
		?>"
	style="line-height: 35px; display: block;">Show RSS Feed <input
	class="checkbox" type="checkbox" <?php
		echo $rssfeed;
		?>
	id="categories-rssfeed-<?php
		echo $number;
		?>"
	name="categories-rssfeed-<?php
		echo $number;
		?>" /> </label> <label
	for="categories-rssimage-<?php
		echo $number;
		?>"><?php
		_e('Path (URI) to RSS image:');
		?>
	<input style="width: 250px;"
	id="categories-rssimage-<?php
		echo $number;
		?>"
	name="categories-rssimage-<?php
		echo $number;
		?>" type="text"
	value="<?php
		echo $rssimage;
		?>" /> </label>

<?php
		if ($avh_extcat_canselectcats) {
			echo '			<b>Include these categories</b><hr />';
			echo '			<ul id="categorychecklist" class="list:category categorychecklist form-no-clear" style="list-style-type: none; margin-left: 5px; padding-left: 0px; margin-bottom: 20px;">';
			echo '				<li id="category--1-' . $number . '" class="popular-category">';
			echo '					<label for="in-category--1-' . $number . '" class="selectit">';
			echo '						<input value="-1" name="post_category-' . $number . '[]" id="in-category--1-' . $number . '" type="checkbox"';
			if (! $selected_cats) {
				echo 'checked';
			}
			echo '> Include All Categories';
			echo '					</label>';
			echo '				</li>';
			avh_wp_category_checklist(0, 0, $selected_cats, false, $number);
			echo '			</ul>';
		}
		?>

	<input type="hidden" id="categories-submit-<?php
		echo $number;
		?>"
	name="categories-submit-<?php
		echo $number;
		?>" value="1" /></div>
<?php
	}

	/**
	 * Called after the widget_extended_categories_page form has been submitted.
	 * Set the amount of widgets wanted and register the widgets
	 *
	 */
	function widget_extended_categories_setup ()
	{
		$options = $newoptions = get_option('widget_extended_categories');
		if (isset($_POST['extended_categories-number-submit'])) {
			$number = (int) $_POST['extended_categories-number'];
			if ($number > 9)
				$number = 9;
			if ($number < 1)
				$number = 1;
			$newoptions['number'] = $number;
		}
		if ($options != $newoptions) {
			$options = $newoptions;
			update_option('widget_extended_categories', $options);
			widget_extended_categories_register($options['number']);
		}
	}

	/**
	 * How many Wish List widgets are wanted.
	 *
	 */
	function widget_extended_categories_page ()
	{
		$options = get_option('widget_extended_categories');
		?>
<div class="wrap">
<form method="post">
<h2><?php
		_e('AVH Extended Categories Widgets', 'avhextendedcategories');
		?></h2>
<p style="line-height: 30px;"><?php
		_e('How many wishlist widgets would you like?', 'avhextendedcategories');
		?>
						<select id="extended_categories-number"
	name="extended_categories-number"
	value="<?php
		echo $options['number'];
		?>">
							<?php
		for ($i = 1; $i < 10; ++ $i)
			echo "<option value='$i' " . ($options['number'] == $i ? "selected='selected'" : '') . ">$i</option>";
		?>
						</select> <span class="submit"><input type="submit"
	name="extended_categories-number-submit"
	id="extended_categories-number-submit"
	value="<?php
		echo attribute_escape(__('Save', 'avhextendedcategories'));
		?>" /></span></p>
</form>
</div>
<?php
	}

	function widget_extended_categories_register ()
	{
		$options = get_option('widget_extended_categories');
		
		$number = (int) $options['number'];
		if ($number < 1)
			$number = 1;
		if ($number > 9)
			$number = 9;
		for ($i = 1; $i <= 9; $i ++) {
			$id = "extended-categories-$i";
			$name = sprintf(__('Extended Categories %d'), $i);
			wp_register_sidebar_widget($id, $name, $i <= $number ? 'widget_extended_categories' : /* unregister */ '', array('classname'=>'widget_extended_categories_init'), $i);
			wp_register_widget_control($id, $name, $i <= $number ? 'widget_extended_categories_control' : /* unregister */ '', array('width'=>300, 'height'=>270), $i);
		}
		add_action('sidebar_admin_setup', 'widget_extended_categories_setup');
		add_action('sidebar_admin_page', 'widget_extended_categories_page');
	}
	
	// Launch Widgets
	widget_extended_categories_register();
}
add_action('plugins_loaded', 'widget_extended_categories_init');

/**
 * As the original wp_category_checklist doesn't support multiple lists on the same page I needed to duplicate the functions
 * use by the wp_category_checklist function
 *
 */
/**
 * Class that will display the categories
 *
 */
class AVH_Walker_Category_Checklist extends Walker
{
	var $tree_type = 'category';
	var $db_fields = array('parent'=>'parent', 'id'=>'term_id'); //TODO: decouple this
	var $number;

	function start_lvl (&$output, $depth, $args)
	{
		$indent = str_repeat("\t", $depth);
		$output .= "$indent<ul class='children'>\n";
	}

	function end_lvl (&$output, $depth, $args)
	{
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	function start_el (&$output, $category, $depth, $args)
	{
		extract($args);
		
		$class = in_array($category->term_id, $popular_cats) ? ' class="popular-category"' : '';
		$output .= "\n<li id='category-$category->term_id-$this->number'$class>" . '<label for="in-category-' . $category->term_id . '-' . $this->number . '" class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="post_category-' . $this->number . '[]" id="in-category-' . $category->term_id . '-' . $this->number . '"' . (in_array($category->term_id, $selected_cats) ? ' checked="checked"' : "") . '/> ' . wp_specialchars(apply_filters('the_category', $category->name)) . '</label>';
	}

	function end_el (&$output, $category, $depth, $args)
	{
		$output .= "</li>\n";
	}
}

/**
 * Creates the categories checklist
 *
 * @param int $post_id
 * @param int $descendants_and_self
 * @param array $selected_cats
 * @param array $popular_cats
 * @param int $number
 */
function avh_wp_category_checklist ($post_id = 0, $descendants_and_self = 0, $selected_cats = false, $popular_cats = false, $number)
{
	$walker = new AVH_Walker_Category_Checklist();
	$walker->number = $number;
	
	$descendants_and_self = (int) $descendants_and_self;
	
	$args = array();
	if (is_array($selected_cats))
		$args['selected_cats'] = $selected_cats;
	elseif ($post_id)
		$args['selected_cats'] = wp_get_post_categories($post_id);
	else
		$args['selected_cats'] = array();
	
	if (is_array($popular_cats))
		$args['popular_cats'] = $popular_cats;
	else
		$args['popular_cats'] = get_terms('category', array('fields'=>'ids', 'orderby'=>'count', 'order'=>'DESC', 'number'=>10, 'hierarchical'=>false));
	
	if ($descendants_and_self) {
		$categories = get_categories("child_of=$descendants_and_self&hierarchical=0&hide_empty=0");
		$self = get_category($descendants_and_self);
		array_unshift($categories, $self);
	} else {
		$categories = get_categories('get=all');
	}
	
	// Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)
	$checked_categories = array();
	for ($i = 0; isset($categories[$i]); $i ++) {
		if (in_array($categories[$i]->term_id, $args['selected_cats'])) {
			$checked_categories[] = $categories[$i];
			unset($categories[$i]);
		}
	}
	
	// Put checked cats on top
	echo call_user_func_array(array(&$walker, 'walk'), array($checked_categories, 0, $args));
	// Then the rest of them
	echo call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args));
}

?>
