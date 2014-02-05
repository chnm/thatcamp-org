<?php

class AVH_EC_Admin
{
	/**
	 *
	 * @var AVH_EC_Core
	 */
	var $core;

	/**
	 *
	 * @var AVH_EC_Category_Group
	 */
	var $catgrp;

	var $hooks = array();
	var $message;

	/**
	 * PHP5 constructor
	 *
	 */
	function __construct ()
	{

		// Initialize the plugin
		$this->core = & AVH_EC_Singleton::getInstance('AVH_EC_Core');
		$this->catgrp = & AVH_EC_Singleton::getInstance('AVH_EC_Category_Group');

		add_action('wp_ajax_delete-group', array(&$this, 'ajaxDeleteGroup'));

		// Admin menu
		add_action('admin_init', array(&$this,'actionAdminInit'));
		add_action('admin_menu', array(&$this, 'actionAdminMenu'));
		add_filter('plugin_action_links_extended-categories-widget/widget_extended_categories.php', array(&$this, 'filterPluginActions'), 10, 2);

		// Actions used for editing posts
		add_action('load-post.php', array(&$this, 'actionLoadPostPage'));
		add_action('load-page.php', array(&$this, 'actionLoadPostPage'));

		// Actions related to adding and deletes categories
		add_action("created_category", array($this, 'actionCreatedCategory'), 10, 2);
		add_action("delete_category", array($this, 'actionDeleteCategory'), 10, 2);

		add_filter('manage_categories_group_columns', array(&$this, 'filterManageCategoriesGroupColumns'));
		add_filter('explain_nonce_delete-avhecgroup', array(&$this, 'filterExplainNonceDeleteGroup'), 10, 2);

		return;
	}

	/**
	 * PHP4 Constructor
	 *
	 */
	function AVH_EC_Admin ()
	{
		$this->__construct();
	}

	function actionAdminInit() {
		if (is_admin() && isset($_GET['taxonomy']) && 'category' == $_GET['taxonomy']) {
			add_action($_GET['taxonomy'] . '_edit_form', array(&$this,'displayCategoryGroupForm'), 10, 2 );
		}
		add_action('edit_term', array(&$this,'handleEditTerm'), 10, 3 );

	}

	/**
	 *
	 * Adds Category Group form
	 * @WordPress action category_edit_form
	 *
	 * @param unknown_type $term
	 * @param unknown_type $taxonomy
	 */
	function displayCategoryGroupForm($term, $taxonomy){

		$current_selection = '';
		$tax_meta = get_option($this->core->db_options_tax_meta);
		if ( isset($tax_meta[$taxonomy][$term->term_id]) ) {
			$tax_meta = $tax_meta[$taxonomy][$term->term_id];
			$current_selection = $tax_meta['category_group_term_id'];
		}

		if (empty($current_selection)) {
			$current_group = $this->catgrp->getGroupByCategoryID($term->term_id);
			$current_selection= $current_group->term_id;
		}

		$cat_groups = get_terms($this->catgrp->taxonomy_name, array('hide_empty'=>FALSE));
		foreach ($cat_groups as $group) {
			$temp_cat = get_term($group->term_id, $this->catgrp->taxonomy_name, OBJECT, 'edit');
			$dropdown_value[] = $group->term_id;
			$dropdown_text[] = $temp_cat->name;
		}

		foreach ( $dropdown_value as $key => $sel) {
			$seldata .= '<option value="' . esc_attr($sel) . '" ' . (($current_selection == $sel) ? 'selected="selected"' : '') . ' >' . esc_html(ucfirst($dropdown_text[$key])) . '</option>' . "\n";
		}

		echo '<h3>AVH Extended Categories - Category Group Widget</h3>';
		echo '<table class="form-table"><tbody>';
		echo '<tr class="form-field">';
		echo '<th valign="top" scope="row">';
		echo '<label for="avhec_categorygroup">Category Group</label></th>';
		echo '<td>';
		echo '<select id="avhec_categorygroup" name="avhec_categorygroup">';
		echo $seldata;
		echo '</select>';
		echo '<p class="description">Select the category group to show on the archive page.</p>';
		echo '</td>';
		echo '</tr>';
		echo '</tbody></table>';
	}

	/**
	 * Saves the association Category - Category Group fron the edit taxonomy page
	 * @WordPress action edit_form.
	 *
	 * @param unknown_type $term_id
	 * @param unknown_type $tt_id
	 * @param unknown_type $taxonomy
	 */
	function handleEditTerm( $term_id, $tt_id, $taxonomy ) {
		$tax_meta = get_option($this->core->db_options_tax_meta);
		if ( isset($_POST['avhec_categorygroup']) && $tax_meta[$taxonomy][$term_id]['category_group_term_id'] 	!= $_POST['avhec_categorygroup']) {
				$tax_meta[$taxonomy][$term_id]['category_group_term_id'] 	= $_POST['avhec_categorygroup'];
				update_option($this->core->db_options_tax_meta, $tax_meta);
		}
	}

	/**
	 * When a category is created this function is called to add the new category to the group all
	 * @param $term_id
	 * @param $term_taxonomy_id
	 */
	function actionCreatedCategory ($term_id, $term_taxonomy_id)
	{
		$group_id = $this->catgrp->getTermIDBy('slug', 'all');
		$this->catgrp->setCategoriesForGroup($group_id, (array) $term_id);
	}

	/**
	 * When a category is deleted this function is called so the category is deleted from every group as well.
	 *
	 * @param object $term
	 * @param int $term_taxonomy_id
	 */
	function actionDeleteCategory ($term_id, $term_taxonomy_id)
	{
		$this->catgrp->doDeleteCategoryFromGroup($term_id);
	}

	/**
	 * Enqueues the style on the post.php and page.php pages
	 * @WordPress Action load-$pagenow
	 *
	 */
	function actionLoadPostPage ()
	{
		wp_enqueue_style('avhec-admin-css');
	}

	/**
	 * Add the Tools and Options to the Management and Options page repectively
	 *
	 * @WordPress Action admin_menu
	 *
	 */
	function actionAdminMenu ()
	{

		// Register Style and Scripts
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.closure';
		wp_register_script('avhec-categorygroup-js', AVHEC_PLUGIN_URL . '/js/avh-ec.categorygroup' . $suffix . '.js', array('jquery'), $this->core->version, true);
		wp_register_script('avhec-manualorder', AVHEC_PLUGIN_URL . '/js/avh-ec.admin.manualorder' . $suffix . '.js', array ( 'jquery-ui-sortable' ), $this->core->version, false);
		wp_register_style('avhec-admin-css', AVHEC_PLUGIN_URL . '/css/avh-ec.admin.css', array('wp-admin'), $this->core->version, 'screen');

		// Add menu system
		$folder = $this->core->getBaseDirectory(AVHEC_PLUGIN_DIR);
		add_menu_page('AVH Extended Categories', 'AVH Extended Categories', 'manage_options', $folder, array(&$this, 'doMenuOverview'));
		$this->hooks['menu_overview'] = add_submenu_page($folder, 'AVH Extended Categories: ' . __('Overview', 'avh-ec'), __('Overview', 'avh-ec'), 'manage_options', $folder, array(&$this, 'doMenuOverview'));
		$this->hooks['menu_general'] = add_submenu_page($folder, 'AVH Extended Categories: ' . __('General Options', 'avh-ec'), __('General Options', 'avh-ec'), 'manage_options', 'avhec-general', array(&$this, 'doMenuGeneral'));
		$this->hooks['menu_category_groups'] = add_submenu_page($folder, 'AVH Extended Categories: ' . __('Category Groups', 'avh-ec'), __('Category Groups', 'avh-ec'), 'manage_options', 'avhec-grouped', array(&$this, 'doMenuCategoryGroup'));
		$this->hooks['menu_manual_order'] = add_submenu_page($folder, 'AVH Extended Categories: ' . __('Manually Order', 'avh-ec'), __('Manually Order', 'avh-ec'), 'manage_options', 'avhec-manual-order', array(&$this, 'doMenuManualOrder'));
		$this->hooks['menu_faq'] = add_submenu_page($folder, 'AVH Extended Categories:' . __('F.A.Q', 'avh-ec'), __('F.A.Q', 'avh-ec'), 'manage_options', 'avhec-faq', array(&$this, 'doMenuFAQ'));

		// Add actions for menu pages
		// Overview Menu
		add_action('load-' . $this->hooks['menu_overview'], array(&$this, 'actionLoadPageHook_Overview'));

		// General Options Menu
		add_action('load-' . $this->hooks['menu_general'], array(&$this, 'actionLoadPageHook_General'));

		// Category Groups Menu
		add_action('load-' . $this->hooks['menu_category_groups'], array(&$this, 'actionLoadPageHook_CategoryGroup'));

		// FAQ Menu
		add_action('load-' . $this->hooks['menu_faq'], array(&$this, 'actionLoadPageHook_faq'));
	}

	/**
	 * Setup everything needed for the Overview page
	 *
	 */
	function actionLoadPageHook_Overview ()
	{
		// Add metaboxes
		add_meta_box('avhecBoxCategoryGroupList', __('Group Overview', 'avh-ec'), array(&$this, 'metaboxCategoryGroupList'), $this->hooks['menu_overview'], 'normal', 'core');
		add_meta_box('avhecBoxTranslation', __('Translation', 'avh-ec'), array(&$this, 'metaboxTranslation'), $this->hooks['menu_overview'], 'normal', 'core');

		if (AVH_Common::getWordpressVersion() >= 3.1 ) {
			add_screen_option('layout_columns', array('max' => 2, 'default' => 2) );
		} else {
			add_filter('screen_layout_columns', array ( &$this, 'filterScreenLayoutColumns' ), 10, 2);
		}

		// WordPress core Scripts
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');

		// Plugin Scripts
		wp_enqueue_script('avhec-categorygroup-js');

		// WordPress core Styles
		wp_admin_css('css/dashboard');

		// Plugin Style
		wp_enqueue_style('avhec-admin-css');
	}

	/**
	 * Menu Page Overview
	 *
	 * @return none
	 */
	function doMenuOverview ()
	{
		global $screen_layout_columns;

		// This box can't be unselectd in the the Screen Options
		add_meta_box('avhecBoxAnnouncements', __('Announcements', 'avh-ec'), array(&$this, 'metaboxAnnouncements'), $this->hooks['menu_overview'], 'side', '');
		add_meta_box('avhecBoxDonations', __('Donations', 'avh-ec'), array(&$this, 'metaboxDonations'), $this->hooks['menu_overview'], 'side', '');

		$hide2 = '';
		switch ($screen_layout_columns) {
			case 2:
				$width = 'width:49%;';
				break;
			default:
				$width = 'width:98%;';
				$hide2 = 'display:none;';
		}

		echo '<div class="wrap avhec-metabox-wrap">';
		echo $this->displayIcon('index');
		echo '<h2>' . 'AVH Extended Categories - ' . __('Overview', 'avh-ec') . '</h2>';
		echo '	<div id="dashboard-widgets-wrap">';
		echo '		<div id="dashboard-widgets" class="metabox-holder">';
		echo '			<div class="postbox-container" style="' . $width . '">' . "\n";
		do_meta_boxes($this->hooks['menu_overview'], 'normal', '');
		echo "			</div>";
		echo '			<div class="postbox-container" style="' . $hide2 . $width . '">' . "\n";
		do_meta_boxes($this->hooks['menu_overview'], 'side', '');
		echo '			</div>';
		echo '		</div>';

		echo '<br class="clear"/>';
		echo '	</div>'; //dashboard-widgets-wrap
		echo '</div>'; // wrap


		$this->printMetaboxGeneralNonces();
		$this->printMetaboxJS('overview');
		$this->printAdminFooter();
	}

	/**
	 * Setup everything needed for the General Options page
	 *
	 */
	function actionLoadPageHook_General ()
	{
		// Add metaboxes
		add_meta_box('avhecBoxOptions', __('Options', 'avh-ec'), array(&$this, 'metaboxOptions'), $this->hooks['menu_general'], 'normal', 'core');

		if (AVH_Common::getWordpressVersion() >= 3.1 ) {
			add_screen_option('layout_columns', array('max' => 2, 'default' => 2) );
		} else {
			add_filter('screen_layout_columns', array ( &$this, 'filterScreenLayoutColumns' ), 10, 2);
		}

		// WordPress core Scripts
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');

		// WordPress core Styles
		wp_admin_css('css/dashboard');

		// Plugin Style and Scripts
		wp_enqueue_style('avhec-admin-css');

	}

	/**
	 * Menu Page General Options
	 *
	 * @return none
	 */
	function doMenuGeneral ()
	{
		global $screen_layout_columns;

		$groups = get_terms($this->catgrp->taxonomy_name, array('hide_empty'=>FALSE));
		foreach ($groups as $group) {
			$group_id[] = $group->term_id;
			$groupname[] = $group->name;
		}

		$options_general[] = array('avhec[general][alternative_name_select_category]', __('<em>Select Category</em> Alternative', 'avh-ec'), 'text', 20, __('Alternative text for Select Category.', 'avh-ec'));
		$options_general[] = array('avhec[cat_group][home_group]', 'Home Group', 'dropdown', $group_id, $groupname, __('Select which group to show on the home page.', 'avh-ec') . '<br />' . __('Selecting the group \'none\' will not show the widget on the page.', 'avh-ec'));
		$options_general[] = array('avhec[cat_group][no_group]', 'Nonexistence Group', 'dropdown', $group_id, $groupname, __('Select which group to show when there is no group associated with the post.', 'avh-ec') . '<br />' . __('Selecting the group \'none\' will not show the widget on the page.', 'avh-ec'));
		$options_general[] = array('avhec[cat_group][default_group]', 'Default Group', 'dropdown', $group_id, $groupname, __('Select which group will be the default group when editing a post.', 'avh-ec') . '<br />' . __('Selecting the group \'none\' will not show the widget on the page.', 'avh-ec'));

		if (isset($_POST['updateoptions'])) {
			check_admin_referer('avh_ec_generaloptions');

			$formoptions = $_POST['avhec'];
			$options = $this->core->getOptions();

			//$all_data = array_merge( $options_general );
			$all_data = $options_general;
			foreach ($all_data as $option) {
				$section = substr($option[0], strpos($option[0], '[') + 1);
				$section = substr($section, 0, strpos($section, ']['));
				$option_key = rtrim($option[0], ']');
				$option_key = substr($option_key, strpos($option_key, '][') + 2);

				switch ($section) {
					case 'general':
					case 'cat_group':
						$current_value = $options[$section][$option_key];
						break;
				}
				// Every field in a form is set except unchecked checkboxes. Set an unchecked checkbox to 0.
				$newval = (isset($formoptions[$section][$option_key]) ? attribute_escape($formoptions[$section][$option_key]) : 0);
				if ($newval != $current_value) { // Only process changed fields.
					switch ($section) {
						case 'general':
						case 'cat_group':
							$options[$section][$option_key] = $newval;
							break;
					}
				}
			}
			$this->core->saveOptions($options);
			$this->message = __('Options saved', 'avh-ec');
			$this->status = 'updated fade';

		}
		$this->displayMessage();

		$actual_options = $this->core->getOptions();
		foreach ($actual_options['cat_group'] as $key => $value) {
			if (! (in_array($value, (array) $group_id))) {
				$actual_options['cat_group'][$key] = $this->catgrp->getTermIDBy('slug', 'none');
			}
		}

		$hide2 = '';
		switch ($screen_layout_columns) {
			case 2:
				$width = 'width:49%;';
				break;
			default:
				$width = 'width:98%;';
				$hide2 = 'display:none;';
		}
		$data['options_general'] = $options_general;
		$data['actual_options'] = $actual_options;

		// This box can't be unselectd in the the Screen Options
		add_meta_box('avhecBoxDonations', __('Donations', 'avh-ec'), array(&$this, 'metaboxDonations'), $this->hooks['menu_general'], 'side', 'core');

		$hide2 = '';
		switch ($screen_layout_columns) {
			case 2:
				$width = 'width:49%;';
				break;
			default:
				$width = 'width:98%;';
				$hide2 = 'display:none;';
		}

		echo '<div class="wrap avhec-metabox-wrap">';
		echo $this->displayIcon('index');
		echo '<h2>' . 'AVH Extended Categories - ' . __('General Options', 'avh-ec') . '</h2>';
		echo '<form name="avhec-generaloptions" id="avhec-generaloptions" method="POST" action="' . admin_url('admin.php?page=avhec-general') . '" accept-charset="utf-8" >';
		wp_nonce_field('avh_ec_generaloptions');

		echo '	<div id="dashboard-widgets-wrap">';
		echo '		<div id="dashboard-widgets" class="metabox-holder">';
		echo '		<div class="postbox-container" style="' . $width . '">' . "\n";
		do_meta_boxes($this->hooks['menu_general'], 'normal', $data);
		echo "			</div>";
		echo '			<div class="postbox-container" style="' . $hide2 . $width . '">' . "\n";
		do_meta_boxes($this->hooks['menu_general'], 'side', $data);
		echo '			</div>';
		echo '		</div>';

		echo '<br class="clear"/>';
		echo '	</div>'; //dashboard-widgets-wrap
		echo '<p class="submit"><input	class="button"	type="submit" name="updateoptions" value="' . __('Save Changes', 'avhf-ec') . '" /></p>';
		echo '</form>';

		echo '</div>'; // wrap


		$this->printMetaboxGeneralNonces();
		$this->printMetaboxJS('general');
		$this->printAdminFooter();
	}

	/**
	 * Options Metabox
	 *
	 */
	function metaboxOptions ($data)
	{
		echo $this->printOptions($data['options_general'], $data['actual_options']);
	}

	/**
	 * Setup everything needed for the Category Group page
	 *
	 */
	function actionLoadPageHook_CategoryGroup ()
	{

		// Add metaboxes
		add_meta_box('avhecBoxCategoryGroupAdd', __('Add Group', 'avh-ec'), array(&$this, 'metaboxCategoryGroupAdd'), $this->hooks['menu_category_groups'], 'normal', 'core');
		add_meta_box('avhecBoxCategoryGroupList', __('Group Overview', 'avh-ec'), array(&$this, 'metaboxCategoryGroupList'), $this->hooks['menu_category_groups'], 'side', 'core');
		add_meta_box('avhecBoxCategoryGroupSpecialPages', __('Special Pages', 'avh-ec'), array(&$this, 'metaboxCategoryGroupSpecialPages'), $this->hooks['menu_category_groups'], 'normal', 'core');

		if (AVH_Common::getWordpressVersion() >= 3.1 ) {
			add_screen_option('layout_columns', array('max' => 2, 'default' => 2) );
		} else {
			add_filter('screen_layout_columns', array ( &$this, 'filterScreenLayoutColumns' ), 10, 2);
		}

		// WordPress core Scripts
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');

		// Plugin Scripts
		wp_enqueue_script('avhec-categorygroup-js');

		// WordPress core Styles
		wp_admin_css('css/dashboard');

		// Plugin Style
		wp_enqueue_style('avhec-admin-css');

	}

	/**
	 * Menu Page Category Group
	 *
	 * @return none
	 */
	function doMenuCategoryGroup ()
	{
		global $screen_layout_columns;

		$data_add_group_default = array('name'=>'', 'slug'=>'', 'widget_title'=>'', 'description'=>'');
		$data_add_group_new = $data_add_group_default;

		$options_add_group[] = array('avhec_add_group[add][name]', __('Group Name', 'avh-ec'), 'text', 20, __('The name is used to identify the group.', 'avh-ec'));
		$options_add_group[] = array('avhec_add_group[add][slug]', __('Slug Group', 'avh-ec'), 'text', 20, __('The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'avh-ec'));
		$options_add_group[] = array('avhec_add_group[add][widget_title]', __('Widget Title', 'avh-ec'), 'text', 20, __('When no title is given in the widget options, this will used as the title of the widget when this group is shown.', 'avh-ec'));
		$options_add_group[] = array('avhec_add_group[add][description]', __('Description', 'avh-ec'), 'textarea', 40, __('Description is not prominent by default.', 'avh-ec'), 5);

		$options_edit_group[] = array('avhec_edit_group[edit][name]', __('Group Name', 'avh-ec'), 'text', 20, __('The name is used to identify the group.', 'avh-ec'));
		$options_edit_group[] = array('avhec_edit_group[edit][slug]', __('Slug Group', 'avh-ec'), 'text', 20, __('The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'avh-ec'));
		$options_edit_group[] = array('avhec_edit_group[edit][widget_title]', __('Widget Title', 'avh-ec'), 'text', 20, __('When no title is given in the widget options, this will used as the title of the widget when this group is shown.', 'avh-ec'));
		$options_edit_group[] = array('avhec_edit_group[edit][description]', __('Description', 'avh-ec'), 'textarea', 40, __('Description is not prominent by default.', 'avh-ec'), 5);
		$options_edit_group[] = array('avhec_edit_group[edit][categories]', __('Categories', 'avh-ec'), 'catlist', 0, __('Select categories to be included in the group.', 'avh-ec'));

		if (isset($_POST['addgroup'])) {
			check_admin_referer('avh_ec_addgroup');

			$formoptions = $_POST['avhec_add_group'];

			$data_add_group_new['name'] = $formoptions['add']['name'];
			$data_add_group_new['slug'] = empty($formoptions['add']['slug']) ? sanitize_title($data_add_group_new['name']) : sanitize_title($formoptions['add']['slug']);
			$data_add_group_new['widget_title'] = $formoptions['add']['widget_title'];
			$data_add_group_new['description'] = $formoptions['add']['description'];

			$id = $this->catgrp->getTermIDBy('slug', $data_add_group_new['slug']);
			if (! $id) {
				$group_id = $this->catgrp->doInsertGroup($data_add_group_new['name'], array('description'=>$data_add_group_new['description'], 'slug'=>$data_add_group_new['slug']), $data_add_group_new['widget_title']);
				$this->catgrp->setCategoriesForGroup($group_id);
				$this->message = __('Category group saved', 'avh-ec');
				$this->status = 'updated fade';
				$data_add_group_new = $data_add_group_default;

			} else {
				$group = $this->catgrp->getGroup($id);
				$this->message = __('Category group conflicts with ', 'avh-ec') . $group->name;
				$this->message .= '<br />' . __('Same slug is used. ', 'avh-ec');
				$this->status = 'error';

			}
			$this->displayMessage();
		}
		$data_add_group['add'] = $data_add_group_new;
		$data['add'] = array('form'=>$options_add_group, 'data'=>$data_add_group);

		if (isset($_GET['action'])) {
			$action = $_GET['action'];

			switch ($action) {
				case 'edit':
					$group_id = (int) $_GET['group_ID'];
					$group = $this->catgrp->getGroup($group_id);
					$widget_title = $this->catgrp->getWidgetTitleForGroup($group_id);
					$cats = $this->catgrp->getCategoriesFromGroup($group_id);

					$data_edit_group['edit'] = array('group_id'=>$group_id, 'name'=>$group->name, 'slug'=>$group->slug, 'widget_title'=>$widget_title, 'description'=>$group->description, 'categories'=>$cats);
					$data['edit'] = array('form'=>$options_edit_group, 'data'=>$data_edit_group);

					add_meta_box('avhecBoxCategoryGroupEdit', __('Edit Group', 'avh-ec') . ': ' . $group->name, array(&$this, 'metaboxCategoryGroupEdit'), $this->hooks['menu_category_groups'], 'normal', 'low');
					break;
				case 'delete':
					if (! isset($_GET['group_ID'])) {
						wp_redirect($this->getBackLink());
						exit();
					}

					$group_id = (int) $_GET['group_ID'];
					check_admin_referer('delete-avhecgroup_' . $group_id);

					if (! current_user_can('manage_categories')) {
						wp_die(__('Cheatin&#8217; uh?'));
					}
					$this->catgrp->doDeleteGroup($group_id);
					break;
				default:
					;
					break;
			}
		}

		if (isset($_POST['editgroup'])) {
			check_admin_referer('avh_ec_editgroup');

			$formoptions = $_POST['avhec_edit_group'];
			$selected_categories = $_POST['post_category'];

			$group_id = (int) $_POST['avhec-group_id'];
			$result = $this->catgrp->doUpdateGroup($group_id, array('name'=>$formoptions['edit']['name'], 'slug'=>$formoptions['edit']['slug'], 'description'=>$formoptions['edit']['description']), $selected_categories, $formoptions['edit']['widget_title']);
			switch ($result) {
				case 1:
					$this->message = __('Category group updated', 'avh-ec');
					$this->status = 'updated fade';
					break;
				case 0:
					$this->message = __('Category group not updated', 'avh-ec');
					$this->message .= '<br />' . __('Duplicate slug detected', 'avh-ec');
					$this->status = 'error';
					break;
				case - 1:
					$this->message = __('Unknown category group', 'avh-ec');
					$this->status = 'error';
					break;
			}
			$this->displayMessage();
		}

		$hide2 = '';
		switch ($screen_layout_columns) {
			case 2:
				$width = 'width:49%;';
				break;
			default:
				$width = 'width:98%;';
				$hide2 = 'display:none;';
		}

		$data_special_pages_old = $this->core->options['sp_cat_group'];
		$data_special_pages_new = $data_special_pages_old;
		if (isset($_POST['avhec_special_pages'])) {
			check_admin_referer('avh_ec_specialpagesgroup');

			$formoptions = $_POST['avhec_special_pages'];
			$formdata = $formoptions['sp'];
			foreach ($formdata as $key => $value) {
				$data_special_pages_new[$key] = $value;
			}
			$this->core->options['sp_cat_group'] = $data_special_pages_new;
			$this->core->saveOptions($this->core->options);

		}
		$data_special_pages['sp'] = $data_special_pages_new;
		$cat_groups = get_terms($this->catgrp->taxonomy_name, array('hide_empty'=>FALSE));

		foreach ($cat_groups as $group) {
			$temp_cat = get_term($group->term_id, $this->catgrp->taxonomy_name, OBJECT, 'edit');
			$dropdown_value[] = $group->term_id;
			$dropdown_text[] = $temp_cat->name;
		}
		$options_special_pages[] = array('avhec_special_pages[sp][home_group]', __('Home page', 'avh-ec'), 'dropdown', $dropdown_value, $dropdown_text, sprintf(__('Select which category to show on the %s page.','avh-ec'),__('home','avhec')));
		//$options_special_pages[] = array('avhec_special_pages[sp][category_group]', __('Category Archive', 'avh-ec'), 'dropdown', $dropdown_value, $dropdown_text, sprintf(__('Select which category to show on the %s page.','avh-ec'),__('category archive','avhec')));
		$options_special_pages[] = array('avhec_special_pages[sp][day_group]', __('Daily Archive', 'avh-ec'), 'dropdown', $dropdown_value, $dropdown_text, sprintf(__('Select which category to show on the %s page.','avh-ec'),__('daily archive','avhec')));
		$options_special_pages[] = array('avhec_special_pages[sp][month_group]', __('Monthly Archive', 'avh-ec'), 'dropdown', $dropdown_value, $dropdown_text, sprintf(__('Select which category to show on the %s page.','avh-ec'),__('monthly archive','avhec')));
		$options_special_pages[] = array('avhec_special_pages[sp][year_group]', __('Yearly Archive', 'avh-ec'), 'dropdown', $dropdown_value, $dropdown_text, sprintf(__('Select which category to show on the %s page.','avh-ec'),__('yearly archive','avhec')));
		$options_special_pages[] = array('avhec_special_pages[sp][author_group]', __('Author Archive', 'avh-ec'), 'dropdown', $dropdown_value, $dropdown_text, sprintf(__('Select which category to show on the %s page.','avh-ec'),__('author archive','avhec')));
		$options_special_pages[] = array('avhec_special_pages[sp][search_group]', __('Search Page', 'avh-ec'), 'dropdown', $dropdown_value, $dropdown_text, sprintf(__('Select which category to show on the %s page.','avh-ec'),__('search','avhec')));

		$data['sp'] = array('form'=>$options_special_pages, 'data'=>$data_special_pages);

		// This box can't be unselectd in the the Screen Options
		//add_meta_box( 'avhecBoxDonations', __( 'Donations', 'avh-ec' ), array (&$this, 'metaboxDonations' ), $this->hooks['menu_category_groups'], 'side', 'core' );


		echo '<div class="wrap avhec-metabox-wrap">';
		echo $this->displayIcon('index');
		echo '<h2>' . 'AVH Extended Categories - ' . __('Category Groups', 'avh-ec') . '</h2>';

		echo '	<div id="dashboard-widgets-wrap">';
		echo '		<div id="dashboard-widgets" class="metabox-holder">';

		echo '			<div class="postbox-container" style="' . $width . '">' . "\n";
		do_meta_boxes($this->hooks['menu_category_groups'], 'normal', $data);
		echo "			</div>";

		echo '			<div class="postbox-container" style="' . $hide2 . $width . '">' . "\n";
		do_meta_boxes($this->hooks['menu_category_groups'], 'side', $data);
		echo '			</div>';

		echo '		</div>'; // dashboard-widgets
		echo '<br class="clear" />';
		echo '	</div>'; //dashboard-widgets-wrap
		echo '</div>'; // wrap


		$this->printMetaboxGeneralNonces();
		$this->printMetaboxJS('grouped');
		$this->printAdminFooter();
	}

	/**
	 * Metabox for Adding a group
	 * @param $data
	 */
	function metaboxCategoryGroupAdd ($data)
	{
		echo '<form name="avhec-addgroup" id="avhec-addgroup" method="POST" action="' . $this->getBackLink() . '" accept-charset="utf-8" >';
		wp_nonce_field('avh_ec_addgroup');
		echo $this->printOptions($data['add']['form'], $data['add']['data']);
		echo '<p class="submit"><input	class="button"	type="submit" name="addgroup" value="' . __('Add group', 'avh-ec') . '" /></p>';
		echo '</form>';
	}

	/**
	 * Metabox for showing the groups as a list
	 *
	 * @param $data
	 */
	function metaboxCategoryGroupList ($data)
	{
		echo '<form id="posts-filter" action="" method="get">';

		echo '<div class="clear"></div>';

		echo '<table class="widefat fixed" cellspacing="0">';
		echo '<thead>';
		echo '<tr>';
		print_column_headers('categories_group');
		echo '</tr>';
		echo '</thead>';

		echo '<tfoot>';
		echo '<tr>';
		print_column_headers('categories_group', false);
		echo '</tr>';
		echo '</tfoot>';

		echo '<tbody id="the-list" class="list:group">';
		$this->printCategoryGroupRows();
		echo '</tbody>';
		echo '</table>';

		echo '<br class="clear" />';
		echo '</form>';

		//echo '</div>';
	}

	/**
	 * Metabox Category Group Edit
	 *
	 */
	function metaboxCategoryGroupEdit ($data)
	{
		echo '<form name="avhec-editgroup" id="avhec-editgroup" method="POST" action="' . $this->getBackLink() . '" accept-charset="utf-8" >';
		wp_nonce_field('avh_ec_editgroup');
		echo $this->printOptions($data['edit']['form'], $data['edit']['data']);
		echo '<input type="hidden" value="' . $data['edit']['data']['edit']['group_id'] . '" name="avhec-group_id" id="avhec-group_id">';
		echo '<p class="submit"><input	class="button"	type="submit" name="editgroup" value="' . __('Update group', 'avh-ec') . '" /></p>';
		echo '</form>';
	}

	/**
	 * Metabox Category Group Special pages
	 *
	 */
	function metaboxCategoryGroupSpecialPages ($data)
	{
		echo '<form name="avhec-specialpagesgroup" id="avhec-specialpagesgroup" method="POST" action="' . $this->getBackLink() . '" accept-charset="utf-8" >';
		wp_nonce_field('avh_ec_specialpagesgroup');
		echo $this->printOptions($data['sp']['form'], $data['sp']['data']);
		echo '<p class="submit"><input	class="button"	type="submit" name="spgroup" value="' . __('Save settings', 'avh-ec') . '" /></p>';
		echo '</form>';
	}


		/**
	 * Setup everything needed for the Manul Order page
	 *
	 */
	function actionLoadPageHook_ManualOrder ()
	{

		add_meta_box('avhecBoxManualOrder', __('Manually Order Categories', 'avh-ec'), array(&$this, 'metaboxManualOrder'), $this->hooks['menu_manual_order'], 'normal', 'core');


		if (AVH_Common::getWordpressVersion() >= 3.1 ) {
			add_screen_option('layout_columns', array('max' => 1, 'default' => 1) );
		} else {
			add_filter('screen_layout_columns', array ( &$this, 'filterScreenLayoutColumns' ), 10, 2);
		}

		// WordPress core Styles and Scripts
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('avhec-manualorder');

		// WordPress core Styles
		wp_admin_css('css/dashboard');

		// Plugin Style
		wp_enqueue_style('avhec-admin-css');

	}

	/**
	 * Menu Page Manual Order
	 *
	 * @return none
	 */
	function doMenuManualOrder ()
	{
		global $screen_layout_columns;

		$hide2 = '';
		switch ($screen_layout_columns) {
			case 2:
				$width = 'width:49%;';
				break;
			default:
				$width = 'width:98%;';
				$hide2 = 'display:none;';
		}

		echo '<div class="wrap">';
		echo $this->displayIcon('index');
		echo '<h2>' . 'AVH Extended Categories - ' . __('Manually Order Categories', 'avh-ec') . '</h2>';

		echo '<div class="metabox-holder">';
		echo '			<div class="postbox-container" style="' . $width . '">' . "\n";
		do_meta_boxes($this->hooks['menu_manual_order'], 'normal', '');
		echo '			</div>';
		echo '</div>';
		echo '</div>'; // wrap
		echo '<div class="clear"></div>';

		$this->printMetaboxGeneralNonces();
		$this->printMetaboxJS('manual_order');
		$this->printAdminFooter();
	}

	/**
	 *
	 * @return unknown_type
	 */
	function metaboxManualOrder ()
	{
		global $wpdb;

		$parentID = 0;

		if (isset($_POST['btnSubCats'])) {
			$parentID = $_POST['cats'];
		} elseif (isset($_POST['hdnParentID'])) {
			$parentID = $_POST['hdnParentID'];
		}

		if (isset($_POST['btnReturnParent'])) {
			$parentsParent = $wpdb->get_row("SELECT parent FROM $wpdb->term_taxonomy WHERE term_id = " . $_POST['hdnParentID'], ARRAY_N);
			$parentID = $parentsParent[0];
		}

		$success = "";
		if (isset($_POST['btnOrderCats'])) {
			if (isset($_POST['hdnManualOrder']) && $_POST['hdnManualOrder'] != "") {

				$hdnManualOrder = $_POST['hdnManualOrder'];
				$IDs = explode(",", $hdnManualOrder);
				$result = count($IDs);

				for ($i = 0; $i < $result; $i ++) {
					$str = str_replace("id_", "", $IDs[$i]);
					$wpdb->query($wpdb->prepare("UPDATE $wpdb->terms SET avhec_term_order = '$i' WHERE term_id ='$str'"));
				}

				$success = '<div id="message" class="updated fade"><p>' . __('Categories updated successfully.', 'avh-ec') . '</p></div>';
			} else {
				$success = '<div id="message" class="updated fade"><p>' . __('An error occured, order has not been saved.', 'avh-ec') . '</p></div>';
			}

		}

		$_SubCategories = "";
		$results = $wpdb->get_results($wpdb->prepare("SELECT t.term_id, t.name FROM $wpdb->term_taxonomy tt, $wpdb->terms t, $wpdb->term_taxonomy tt2 WHERE tt.parent = $parentID AND tt.taxonomy = 'category' AND t.term_id = tt.term_id AND tt2.parent = tt.term_id GROUP BY t.term_id, t.name HAVING COUNT(*) > 0 ORDER BY t.avhec_term_order ASC"));
		foreach ($results as $row) {
			$_SubCategories .= "<option value='$row->term_id'>$row->name</option>";
		}

		echo '<div class="wrap">';
		echo '<form name="frmMyCatOrder" method="post" action="">';
		echo $success;



		echo '<h4>';
		_e('Order the categories', 'avh-ec');
		if ($parentID == 0) {
			echo ' at the Toplevel';
		} else {
			$_cats = get_category_parents($parentID, false, ' » ');
			echo ' in the category ' . trim($_cats, ' » ');
		}
		echo '</h4>';
		echo '<span class="description">';
		_e('Order the categories on this level by dragging and dropping them into the desired order.', 'avh-ec');
		echo '</span>';
		echo '<ul id="avhecManualOrder">';
		$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->terms t inner join $wpdb->term_taxonomy tt on t.term_id = tt.term_id WHERE taxonomy = 'category' and parent = $parentID ORDER BY avhec_term_order ASC"));
		foreach ($results as $row)
			echo "<li id='id_$row->term_id' class='lineitem menu-item-settings'>" . __($row->name) . "</li>";

		echo '</ul>';
		echo '<input type="submit" name="btnOrderCats" id="btnOrderCats" class="button-primary" 	value="' . __('Save Order', 'avh-ec') . '"	onclick="javascript:orderCats(); return true;" />';

		if ($parentID != 0) {
			echo "<input type='submit' class='button' id='btnReturnParent' name='btnReturnParent' value='" . __('Return to parent category', 'avh-ec') . "' />";
		}

		echo '<strong id="updateText"></strong><br /><br />';
		if ($_SubCategories != "") {

			echo '<h4>';
			_e('Select Subcategory', 'avh-ec');
			echo '</h4>';
			echo '<select id="cats" name="cats">';
			echo $_SubCategories;

			echo '</select><input type="submit" name="btnSubCats" class="button" id="btnSubCats" value="' . __('Select', 'avh-ec') . '" />';
			echo '<span class="description">';
			_e('Choose a category from the drop down to order the subcategories in that category.', 'avh-ec');
			echo '</span>';
		}

		echo '<input type="hidden" id="hdnManualOrder" name="hdnManualOrder" />';
		echo '<input type="hidden" id="hdnParentID" name="hdnParentID"	value="' . $parentID . '" /></form>';
		echo '</div>';
	}

	/**
	 * Setup everything needed for the FAQ page
	 *
	 */
	function actionLoadPageHook_faq ()
	{

		add_meta_box('avhecBoxFAQ', __('F.A.Q.', 'avh-ec'), array(&$this, 'metaboxFAQ'), $this->hooks['menu_faq'], 'normal', 'core');
		add_meta_box('avhecBoxTranslation', __('Translation', 'avh-ec'), array(&$this, 'metaboxTranslation'), $this->hooks['menu_faq'], 'normal', 'core');

		if (AVH_Common::getWordpressVersion() >= 3.1 ) {
			add_screen_option('layout_columns', array('max' => 2, 'default' => 2) );
		} else {
			add_filter('screen_layout_columns', array ( &$this, 'filterScreenLayoutColumns' ), 10, 2);
		}

		// WordPress core Styles and Scripts
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');

		// WordPress core Styles
		wp_admin_css('css/dashboard');

		// Plugin Style
		wp_enqueue_style('avhec-admin-css');

	}

	/**
	 * Menu Page FAQ
	 *
	 * @return none
	 */
	function doMenuFAQ ()
	{
		global $screen_layout_columns;

		// This box can't be unselectd in the the Screen Options
		add_meta_box('avhecBoxAnnouncements', __('Announcements', 'avh-ec'), array(&$this, 'metaboxAnnouncements'), $this->hooks['menu_faq'], 'side', 'core');
		add_meta_box('avhecBoxDonations', __('Donations', 'avh-ec'), array(&$this, 'metaboxDonations'), $this->hooks['menu_faq'], 'side', 'core');

		$hide2 = '';
		switch ($screen_layout_columns) {
			case 2:
				$width = 'width:49%;';
				break;
			default:
				$width = 'width:98%;';
				$hide2 = 'display:none;';
		}

		echo '<div class="wrap avhec-metabox-wrap">';
		echo $this->displayIcon('index');
		echo '<h2>' . 'AVH Extended Categories - ' . __('F.A.Q', 'avh-ec') . '</h2>';
		echo '	<div id="dashboard-widgets-wrap">';
		echo '		<div id="dashboard-widgets" class="metabox-holder">';
		echo '			<div class="postbox-container" style="' . $width . '">' . "\n";
		do_meta_boxes($this->hooks['menu_faq'], 'normal', '');
		echo '			</div>';
		echo '			<div class="postbox-container" style="' . $hide2 . $width . '">' . "\n";
		do_meta_boxes($this->hooks['menu_faq'], 'side', '');
		echo '			</div>';
		echo '		</div>';
		echo '<br class="clear"/>';
		echo '	</div>'; //dashboard-widgets-wrap
		echo '</div>'; // wrap


		$this->printMetaboxGeneralNonces();
		$this->printMetaboxJS('faq');
		$this->printAdminFooter();
	}

	/**
	 * Translation Metabox
	 * @return unknown_type
	 */
	function metaboxTranslation ()
	{
		$locale = apply_filters( 'plugin_locale', get_locale(), 'avh-ec' );
		$available_locale['cs_CZ'] = array('Czech - Čeština',0);
		$available_locale['nl_NL'] = array('Dutch - Nederlands',0);
		$available_locale['de_DE'] = array('German - Deutsch', 0);
		$available_locale['el'] = array('Greek - Čeština',0);
		$available_locale['id_ID'] = array('Indonesian - Bahasa Indonesia - Čeština',0);
		$available_locale['it_IT'] = array('Italian - Italiano',1);
		$available_locale['ru_RU'] = array('Russian — Русский',0);
		$available_locale['es_ES'] = array('Spanish - Español',0);
		$available_locale['sv_SE'] = array('Swedish - Svenska',0);
		$available_locale['tr'] = array('Turkish - Türkçe',0);

		echo '<div class="p">';
		echo __('This plugin is translated in several languages. Some of the languages might be incomplete. Please help to complete these translations or add a new language.', 'avh-ec') . '<br />';

		echo '</div>';

		echo '<div class="p">';
		echo '<span class="b">' . __('Available Languages', 'avh-ec') . '</span>';
		echo '<ul>';
		foreach ($available_locale as $key => $value) {
			echo '<li>';
			$complete = ($value[1] == 1 ? 'Complete' : 'Incomplete');
			echo $value[0] . ' (' . $key . ') - ' . $complete;
			echo '</li>';
		}
		echo '</ul>';
		echo '</div>';

		echo '<div class="p">';
			if ('en_US' != $locale & (!array_key_exists($locale, $available_locale))){
			echo 'Currently the plugin is not available in your language ('.$locale.'). Why not help out and translate the plugin in your language. You can help by visiting <a href="https://translations.launchpad.net/avhextendedcategories/trunk" target="_blank">Launchpad</a>.';
		} else {
			echo __('You can visit ', 'avh-ec') . '<a href="https://translations.launchpad.net/avhextendedcategories/trunk" target="_blank">Launchpad</a> ' . __('to help complete these translations or add a new language.', 'avh-ec');
		}
		echo '</div>';

	}

	/**
	 * Donation Metabox
	 * @return unknown_type
	 */
	function metaboxDonations ()
	{
		echo '<div class="p">';
		echo __('If you enjoy this plug-in please consider a donation. There are several ways you can show your appreciation.', 'avh-ec');
		echo '</div>';

		echo '<div class="p">';
		echo '<span class="b">Amazon</span><br />';
		echo __('If you decide to buy something from Amazon click the button.', 'avh-ec') . '</span><br />';
		echo '<a href="https://www.amazon.com/?tag=avh-donation-20" target="_blank" title="Amazon Homepage"><img alt="Amazon Button" src="' . $this->core->info['graphics_url'] . '/us_banner_logow_120x60.gif" /></a>';
		echo '</div>';

		echo '<div class="p">';
		echo __('You can send me something from my ', 'avh-ec') . '<a href="http://www.amazon.com/registry/wishlist/1U3DTWZ72PI7W?tag=avh-donation-20">' . __('Amazon Wish List', 'avh-ec') . '</a>';
		echo '</div>';

		echo '<div class="p">';
		echo '<span class="b">' . __('Through Paypal.', 'avh-ec') . '</span><br />';
		echo __('Click on the Donate button and you will be directed to Paypal where you can make your donation and you don\'t need to have a Paypal account to make a donation.', 'avh-ec') . '<br />';
		echo '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=S85FXJ9EBHAF2&lc=US&item_name=AVH%20Plugins&item_number=fdas&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted" target="_blank" title="Donate">';
		echo '<img src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" alt="Donate"/></a>';
		echo '</div>';

	}

	/***
	 * F.A.Q Metabox
	 * @return none
	 */
	function metaboxFAQ ()
	{
		echo '<div class="p">';
		echo '<span class="b">' . __('What about support?', 'avh-ec') . '</span><br />';
		echo __('I created a support site at http://forums.avirtualhome.com where you can ask questions or request features.', 'avh-ec') . '<br />';
		echo '</div>';

		echo '<div class="p">';
		echo '<span class="b">' . __('What is depth selection?', 'avh-ec') . '</span><br />';
		echo __('Starting with version 2.0 and WordPress 2.8 you can select how many levels deep you want to show your categories. This option only works when you select Show Hierarchy as well.', 'avh-ec') . '<br /><br />';
		echo __('Here is how it works: Say you have 5 top level categories and each top level has a number of children. You could manually select all the Top Level categories you want to show but now you can do the following:', 'avh-ec') . '<br />';
		echo __('You select to display all categories, select to Show hierarchy and select how many levels you want to show, in this case Toplevel only.', 'avh-ec') . '<br />';
		echo '</div>';

		echo '<div class="p">';
		echo '<span class="b">' . __('Multiple Category Groups', 'avh-ec') . '</span><br />';
		echo __('The following is an explanation how assigning multiple groups to page/post works.', 'avh-ec') . '<br /><br />';
		echo __('Lets say you have the following groups:', 'avh-ec');
		echo '<ul>';
		echo '<li>' . __('Free Time', 'avh-ec') . '</li>';
		echo '<li>' . __('Theater', 'avh-ec') . '</li>';
		echo '<li>' . __('Movie', 'avh-ec') . '</li>';
		echo '<li>' . __('Music', 'avh-ec') . '</li>';
		echo '</ul>';
		echo __('Setup several Category Group widgets and associated each widget with one or more groups.', 'avh-ec') . '<br />';
		echo __('Widget 1 has association with Free Time', 'avh-ec') . '<br />';
		echo __('Widget 2 has association with Theater, Movie and Music', 'avh-ec') . '<br />';
		echo __('Widget 3 has association with Theater, Movie and Music', 'avh-ec') . '<br /><br />';
		echo __('Page has associations the groups Free Time and Theater', 'avh-ec');
		echo '<ul>';
		echo '<li>' . __('Widget 1: Shows categories of the Free Time group', 'avh-ec') . '</li>';
		echo '<li>' . __('Widget 2: Shows categories of the Theater group.', 'avh-ec') . '</li>';
		echo '<li>' . __('Widget 3: Not displayed', 'avh-ec') . '</li>';
		echo '</ul>';
		echo __('Page has associations the group Movie.', 'avh-ec');
		echo '<ul>';
		echo '<li>' . __('Widget 1: Not displayed', 'avh-ec') . '</li>';
		echo '<li>' . __('Widget 2: Shows categories of the Movie group.', 'avh-ec') . '</li>';
		echo '<li>' . __('Widget 3: Not displayed', 'avh-ec') . '</li>';
		echo '</ul>';
		echo __('Page has associations the groups Free Time, Movie and Music', 'avh-ec');
		echo '<ul>';
		echo '<li>' . __('Widget 1: Shows categories of the Free Time group', 'avh-ec') . '</li>';
		echo '<li>' . __('Widget 2: Shows categories of the Movie or Music group.', 'avh-ec') . '</li>';
		echo '<li>' . __('Widget 3: Shows categories of the Music or Movie group.', 'avh-ec') . '</li>';
		echo '</ul>';
		echo __('Whether Widget 2 shows Movie or Music depends on the creation order of groups. If Widget 2 shows Movie, Widget 3 will show Music but if Widget 2 shows Music, Widget 3 will show Movie.', 'avh-ec') . '<br />';
		echo '</div>';
	}

	function metaboxAnnouncements ()
	{
		$php5 = version_compare('5.2', phpversion(), '<');
		echo '<div class="p">';
		echo '<span class="b">' . __('PHP4 Support', 'avh-ec') . '</span><br />';
		echo __('The next major release of the plugin will no longer support PHP4.', 'avh-ec') . '<br />';
		echo __('It will be written for PHP 5.2 and ', 'avh-ec');
		if ($php5) {
			echo __('your blog already runs the needed PHP version. When the new release comes out you can safely update.', 'avh-ec') . '<br />';
		} else {
			echo __('your blog still runs PHP4. When the new release comes out you can not use it.', 'avh-ec') . '<br />';
			echo __('I don\'t have a timeline for the next version but consider contacting your host if PHP 5.2 is available.', 'avh-ec') . '<br />';
			echo __('If your hosts doesn\'t offer PHP 5.2 you might want to consider switching hosts.', 'avh-ec') . '<br />';
			echo __('A host to consider is ', 'avh-ec') . '<a href="http://www.lunarpages.com/id/pdoes" target="_blank">Lunarpages</a>';
			echo __('I run my personal blog there and I am very happy with their services. You can get an account with unlimited bandwidth, storage and much more for a low price.', 'avh-ec');
		}
		echo '</div>';

	}

	/**
	 * Sets the amount of columns wanted for a particuler screen
	 *
	 * @WordPress filter screen_meta_screen
	 * @param $screen
	 * @return strings
	 */

	function filterScreenLayoutColumns ($columns, $screen)
	{
		switch ($screen) {
			case $this->hooks['menu_overview']:
				$columns[$this->hooks['menu_overview']] = 2;
				break;
			case $this->hooks['menu_general']:
				$columns[$this->hooks['menu_general']] = 2;
				break;
			case $this->hooks['menu_category_groups']:
				$columns[$this->hooks['menu_category_groups']] = 2;
				break;
			case $this->hooks['menu_faq']:
				$columns[$this->hooks['menu_faq']] = 2;
				break;

		}
		return $columns;
	}

	/**
	 * Adds Settings next to the plugin actions
	 *
	 * @WordPress Filter plugin_action_links_avh-amazon/avh-amazon.php
	 *
	 */
	function filterPluginActions ($links, $file)
	{
		$settings_link = '<a href="admin.php?page=extended-categories-widget">' . __('Settings', 'avh-ec') . '</a>';
		array_unshift($links, $settings_link); // before other links
		return $links;

	}

	/**
	 * Creates a new array for columns headers. Used in print_column_headers. The filter is called from get_column_headers
	 *
	 * @param $columns
	 * @return Array
	 * @see print_column_headers, get_column_headers
	 */
	function filterManageCategoriesGroupColumns ($columns)
	{
		$categories_group_columns = array('name'=>__('Name', 'avh-ec'), 'slug'=>'Slug', 'widget-title'=>__('Widget Title', 'avh-ec'), 'description'=>__('Description', 'avh-ec'), 'cat-in-group'=>__('Categories in the group', 'avh-ec'));
		return $categories_group_columns;
	}

	/**
	 * When not using AJAX, this function is called when the deletion fails.
	 *
	 * @param string $text
	 * @param int $group_id
	 * @return string
	 * @WordPress Filter explain_nonce_$verb-$noun
	 * @see wp_explain_nonce
	 */
	function filterExplainNonceDeleteGroup ($text, $group_id)
	{
		$group = get_term($group_id, $this->catgrp->taxonomy_name, OBJECT, 'display');

		$return = sprintf(__('Your attempt to delete this group: &#8220;%s&#8221; has failed.'), $group->name);
		return ($return);
	}

	############## Admin WP Helper ##############


	/**
	 * Get the backlink for forms
	 *
	 * @return strings
	 */
	function getBackLink ()
	{
		$page = basename(__FILE__);
		if (isset($_GET['page']) && ! empty($_GET['page'])) {
			$page = preg_replace('[^a-zA-Z0-9\.\_\-]', '', $_GET['page']);
		}

		if (function_exists("admin_url"))
			return admin_url(basename($_SERVER["PHP_SELF"])) . "?page=" . $page;
		else
			return $_SERVER['PHP_SELF'] . "?page=" . $page;
	}

	/**
	 * Print all Category Group rows
	 *
	 * @uses printCategoryGroupRow
	 *
	 */
	function printCategoryGroupRows ()
	{
		$cat_groups = get_terms($this->catgrp->taxonomy_name, array('hide_empty'=>FALSE));

		foreach ($cat_groups as $group) {
			if ('none' != $group->slug) {
				echo $this->printCategoryGroupRow($group->term_id, $group->term_taxonomy_id);
			}
		}
	}

	/**
	 * Displays all the information of a group in a row
	 * Adds inline link for delete and/or edit.
	 *
	 * @param int $group_term_id
	 * @param int $group_term_taxonomy_id
	 */
	function printCategoryGroupRow ($group_term_id, $group_term_taxonomy_id)
	{
		static $row_class = '';

		$group = get_term($group_term_id, $this->catgrp->taxonomy_name, OBJECT, 'display');

		$no_edit[$this->catgrp->getTermIDBy('slug', 'all')] = 0;
		$no_delete[$this->catgrp->getTermIDBy('slug', 'all')] = 0;

		if (current_user_can('manage_categories')) {
			$actions = array();
			if (! array_key_exists($group->term_id, $no_edit)) {
				$edit_link = "admin.php?page=avhec-grouped&amp;action=edit&amp;group_ID=$group->term_id";
				$edit = "<a class='row-title' href='$edit_link' title='" . esc_attr(sprintf(__('Edit &#8220;%s&#8221;'), $group->name)) . "'>" . esc_attr($group->name) . '</a><br />';

				$actions['edit'] = '<a href="' . $edit_link . '">' . __('Edit') . '</a>';
			} else {
				$edit = esc_attr($group->name);
			}
			if (! (array_key_exists($group->term_id, $no_delete))) {
				$actions['delete'] = "<a class='delete:the-list:group-$group->term_id submitdelete' href='" . wp_nonce_url("admin.php?page=avhec-grouped&amp;action=delete&amp;group_ID=$group->term_id", 'delete-avhecgroup_' . $group->term_id) . "'>" . __('Delete') . "</a>";
			}
			$action_count = count($actions);
			$i = 0;
			$edit .= '<div class="row-actions">';
			foreach ($actions as $action => $link) {
				++ $i;
				($i == $action_count) ? $sep = '' : $sep = ' | ';
				$edit .= "<span class='$action'>$link$sep</span>";
			}
			$edit .= '</div>';
		} else {
			$edit = $group->name;
		}

		$row_class = 'alternate' == $row_class ? '' : 'alternate';
		$qe_data = get_term($group->term_id, $this->catgrp->taxonomy_name, OBJECT, 'edit');

		$output = "<tr id='group-$group->term_id' class='iedit $row_class'>";

		$columns = get_column_headers('categories_group');
		$hidden = get_hidden_columns('categories_group');
		foreach ($columns as $column_name => $column_display_name) {
			$class = 'class="' . $column_name . ' column-' . $column_name . '"';

			$style = '';
			if (in_array($column_name, $hidden))
				$style = ' style="display:none;"';

			$attributes = $class . $style;

			switch ($column_name) {
				case 'cb':
					$output .= '<th scope="row" class="check-column">';
					if (! (array_key_exists($group->term_id, $no_delete))) {
						$output .= '<input type="checkbox" name="delete[]" value="' . $group->term_id . '" />';
					} else {
						$output .= "&nbsp;";
					}
					$output .= '</th>';
					break;
				case 'name':
					$output .= '<td ' . $attributes . '>' . $edit;
					$output .= '<div class="hidden" id="inline_' . $qe_data->term_id . '">';
					$output .= '<div class="name">' . $qe_data->name . '</div>';
					$output .= '<div class="slug">' . apply_filters('editable_slug', $qe_data->slug) . '</div>';
					$output .= '</div></td>';
					break;
				case 'widget-title':
					$title = $this->catgrp->getWidgetTitleForGroup($group->term_id);
					$output .= '<td ' . $attributes . '>' . $title . '</td>';
					break;

				case 'description':
					$output .= '<td ' . $attributes . '>' . $qe_data->description . '</td>';
					break;
				case 'slug':
					$output .= "<td $attributes>" . apply_filters('editable_slug', $qe_data->slug) . "</td>";
					break;
				case 'cat-in-group':
					$cats = $this->catgrp->getCategoriesFromGroup($group_term_id);
					$catname = array();
					foreach ($cats as $cat_id) {
						$catname[] = get_cat_name($cat_id);
					}
					natsort($catname);
					$cat = implode(', ', $catname);
					$output .= '<td ' . $attributes . '>' . $cat . '</td>';
					break;

			}
		}
		$output .= '</tr>';

		return $output;
	}

	/**
	 * Prints the general nonces, used by the AJAX
	 */
	function printMetaboxGeneralNonces ()
	{
		echo '<form style="display:none" method="get" action="">';
		echo '<p>';
		wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
		wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);
		echo '</p>';
		echo '</form>';

	}

	/**
	 * Print the Metabox JS for toggling closed and open
	 *
	 * @param $boxid
	 */
	function printMetaboxJS ($boxid)
	{
		$a = $this->hooks['menu_' . $boxid];
		echo '<script type="text/javascript">' . "\n";
		echo '	//<![CDATA[' . "\n";
		echo '	jQuery(document).ready( function($) {' . "\n";
		echo '		$(\'.if-js-closed\').removeClass(\'if-js-closed\').addClass(\'closed\');' . "\n";
		echo '		// postboxes setup' . "\n";
		echo '		postboxes.add_postbox_toggles(\'' . $a . '\');' . "\n";
		echo '	});' . "\n";
		echo '	//]]>' . "\n";
		echo '</script>';

	}

	/**
	 * Display plugin Copyright
	 *
	 */
	function printAdminFooter ()
	{
		echo '<p class="footer_avhec">';
		printf('&copy; Copyright %d <a href="http://blog.avirtualhome.com/" title="My Thoughts">Peter van der Does</a> | AVH Extended Categories Version %s', date('Y'), $this->core->version);
		echo '</p>';
	}

	/**
	 * Display WP alert
	 *
	 */
	function displayMessage ()
	{
		if ($this->message != '') {
			$message = $this->message;
			$status = $this->status;
			$this->message = $this->status = ''; // Reset
		}
		if (isset($message)) {
			$status = ($status != '') ? $status : 'updated fade';
			echo '<div id="message"	class="' . $status . '">';
			echo '<p><strong>' . $message . '</strong></p></div>';
		}
	}

	/**
	 * Ouput formatted options
	 *
	 * @param array $option_data
	 * @return string
	 */
	function printOptions ($option_data, $option_actual)
	{
		// Generate output
		$output = '';
		$output .= "\n" . '<table class="form-table avhec-options">' . "\n";
		foreach ($option_data as $option) {
			$section = substr($option[0], strpos($option[0], '[') + 1);
			$section = substr($section, 0, strpos($section, ']['));
			$option_key = rtrim($option[0], ']');
			$option_key = substr($option_key, strpos($option_key, '][') + 2);
			// Helper
			if ($option[2] == 'helper') {
				$output .= '<tr style="vertical-align: top;"><td class="helper" colspan="2">' . wp_filter_post_kses($option[4]) . '</td></tr>' . "\n";
				continue;
			}
			switch ($option[2]) {
				case 'checkbox':
					$input_type = '<input type="checkbox" id="' . esc_attr($option[0]) . '" name="' . esc_attr($option[0]) . '" value="' . esc_attr($option[3]) . '" ' . $this->isChecked('1', $option_actual[$section][$option_key]) . ' />' . "\n";
					$explanation = $option[4];
					break;
				case 'dropdown':
					$selvalue = $option[3];
					$seltext = $option[4];
					$seldata = '';
					foreach ((array) $selvalue as $key => $sel) {
						$seldata .= '<option value="' . esc_attr($sel) . '" ' . (($option_actual[$section][$option_key] == $sel) ? 'selected="selected"' : '') . ' >' . esc_html(ucfirst($seltext[$key])) . '</option>' . "\n";
					}
					$input_type = '<select id="' . esc_attr($option[0]) . '" name="' . esc_attr($option[0]) . '">' . $seldata . '</select>' . "\n";
					$explanation = $option[5];
					break;
				case 'text-color':
					$input_type = '<input type="text" ' . (($option[3] > 1) ? ' style="width: 95%" ' : '') . 'id="' . esc_attr($option[0]) . '" name="' . esc_attr($option[0]) . '" value="' . esc_attr($option_actual[$section][$option_key]) . '" size="' . esc_attr($option[3]) . '" /><div class="box_color ' . esc_attr($option[0]) . '"></div>' . "\n";
					$explanation = $option[4];
					break;
				case 'textarea':
					$input_type = '<textarea rows="' . esc_attr($option[5]) . '" ' . (($option[3] > 1) ? ' style="width: 95%" ' : '') . 'id="' . esc_attr($option[0]) . '" name="' . esc_attr($option[0]) . '" size="' . esc_attr($option[3]) . '" />' . $option_actual[$section][$option_key] . '</textarea>';
					$explanation = $option[4];
					break;
				case 'catlist':
					ob_start();
					echo '<div id="avhec-catlist">';
					echo '<ul>';
					wp_category_checklist(0, 0, $option_actual[$section][$option_key]);
					echo '</ul>';
					echo '</div>';
					$input_type = ob_get_contents();
					ob_end_clean();
					$explanation = $option[4];
					break;
				case 'text':
				default:
					$input_type = '<input type="text" ' . (($option[3] > 1) ? ' style="width: 95%" ' : '') . 'id="' . esc_attr($option[0]) . '" name="' . esc_attr($option[0]) . '" value="' . esc_attr($option_actual[$section][$option_key]) . '" size="' . esc_attr($option[3]) . '" />' . "\n";
					$explanation = $option[4];
					break;
			}
			// Additional Information
			$extra = '';
			if ($explanation) {
				$extra = '<br /><span class="description">' . wp_filter_kses($explanation) . '</span>' . "\n";
			}
			// Output
			$output .= '<tr style="vertical-align: top;"><th align="left" scope="row"><label for="' . esc_attr($option[0]) . '">' . wp_filter_kses($option[1]) . '</label></th><td>' . $input_type . '	' . $extra . '</td></tr>' . "\n";
		}
		$output .= '</table>' . "\n";
		return $output;
	}

	/**
	 * Used in forms to set an option checked
	 *
	 * @param mixed $checked
	 * @param mixed $current
	 * @return strings
	 */
	function isChecked ($checked, $current)
	{
		$return = '';
		if ($checked == $current) {
			$return = ' checked="checked"';
		}
		return $return;
	}

	/**
	 * Displays the icon on the menu pages
	 *
	 * @param $icon
	 */
	function displayIcon ($icon)
	{
		return ('<div class="icon32" id="icon-' . $icon . '"><br/></div>');
	}

	/**
	 * Ajax Helper: inline delete of the groups
	 */
	function ajaxDeleteGroup ()
	{
		$group_id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
		check_ajax_referer('delete-avhecgroup_' . $group_id);

		if (! current_user_can('manage_categories')) {
			die('-1');
		}
		$check = $this->catgrp->getGroup($group_id);
		if (false === $check) {
			die('1');
		}

		if ($this->catgrp->doDeleteGroup($group_id)) {
			die('1');
		} else {
			die('0');
		}
	}
}
?>
