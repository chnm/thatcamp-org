<?php
/**
* Collection of functions for form fields used in author avatars widget and shortcode wizard
*/
class AuthorAvatarsForm {
	/**
	* @var callback Callback for field "id" attributes.
	* @access private
	*/
	var $field_id_callback = null;
	/**
	* @var callback Callback for field "name" attributes.
	* @access private
	*/
	var $field_name_callback = null;
	/**
	* @var AuthorAvatarsSettings Reference to AuthorAvatarsSettings instance.
	* @access private
	*/
	var $settings = null;
	/**
	* @var List of all tabs created by renderTabStart().
	* @access private
	*/
	var $tabs = null;
	/**
	* Constructor
	*/
	function AuthorAvatarsForm() {
	require_once('FormHelper.class.php');
	$this->settings = AA_settings();
	$this->tabs = array();
	}
	/**
	* Set a field id callback handler
	*
	* @access public
	* @param callback $callback
	* @return void
	*/
	function setFieldIdCallback($callback) {
		if (is_callable($callback)) {
			$this->field_id_callback = $callback;
		}
		else {
			trigger_error('Error: callback function is not callable.', E_USER_ERROR);
			$this->field_id_callback = null;
		}
	}
	/**
	* Set a field name callback handler
	*
	* @access public
	* @param callback $callback
	* @return void
	*/
	function setFieldNameCallback($callback) {
		if (is_callable($callback)) {
			$this->field_name_callback = $callback;
		}
		else {
			trigger_error('Error: callback function is not callable.', E_USER_ERROR);
			$this->field_name_callback = null;
		}
	}
	/**
	* Return the field id 
	*
	* @access protected
	* @param string $id
	* @return string
	*/
	function _getFieldId($id) {
		if ($this->field_id_callback != null) {
			$id = call_user_func($this->field_id_callback, $id);
		}
		return $id;
	}
	/**
	* Return the field name 
	*
	* @access protected
	* @param string $name
	* @return string
	*/
	function _getFieldName($name) {
		if ($this->field_name_callback != null) {
			$name = call_user_func($this->field_name_callback, $name);
		}
		return $name;
	}
	/**
	* Renders the blog filter select field.
	*
	* @param mixed $values the field values
	* @param string $name the field name
	* @return string
	*/
	function renderFieldBlogs($values=array(), $name = 'blogs') {
		$html = '';
		if ($this->settings->blog_selection_allowed()) {
			$id = $this->_getFieldId($name);
			$name = $this->_getFieldName($name);
			$html .= '<p>' . FormHelper::choice($name, Array(-1 => "All") + $this->_getAllBlogs(), $values, array(
			'id' => $id,
			'multiple' => true, 
			'label' => '<strong>'. __('Show users from blogs', 'author-avatars') .':</strong><br />',
			'help' => '<br/><small>'. __('If no blog is selected only users from the current blog are displayed.', 'author-avatars') .'</small>',
			)) . '</p>';
		}
		return $html;
	}
	/**
	* Retrieves all blogs, and returns them as an associative array (blog id -> blog name)
	*
	* The list only contains public blogs which are not marked as archived, deleted
	* or spam and the list is ordered by blog name.
	*
	* @see http://codex.wordpress.org/WPMU_Functions/get_blog_list
	* @access private
	* @return Array of blog names
	*/
	function _getAllBlogs() {
		global $wpdb;
		$blogs = $wpdb->get_results( $wpdb->prepare("SELECT blog_id, path FROM $wpdb->blogs WHERE site_id = %d AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0'", $wpdb->siteid), ARRAY_A );
		$blog_list = array();
		foreach ( (array) $blogs as $details ) {
			$blog_list[ $details['blog_id'] ] = get_blog_option( $details['blog_id'], 'blogname', $details['path']) .' ('. $details['blog_id'] .')';
		}
		asort($blog_list);
		return $blog_list;
	}
	/**
	* Renders the group by field, which is either a dropdown field or a single checkbox if only one option is available.
	*
	* @param mixed $values the field values
	* @param string $name the field name
	* @return string
	*/
	function renderFieldGroupBy($values=array(), $name='group_by') {
		$group_by_options = Array();
		if ($this->settings->blog_selection_allowed()) {
			$group_by_options['blog'] = __('Group by blogs', 'author-avatars');
		}
		$html = '';
		if (!empty($group_by_options)) {
			$attributes = array();
			$attributes['id'] = $this->_getFieldId($name);
			$attributes['expanded'] = true;
			if (count($group_by_options) > 1) {
				$attributes['label'] = __('User list grouping', 'author-avatars'). ': <br/>';
			}
			else {
				$attributes['wrapper_tag'] = 'p';
			}
			$name = $this->_getFieldName($name);
			$html = FormHelper::choice($name, $group_by_options, $values, $attributes);
		}
		return $html;
	}
	/**
	* Renders the roles field
	*
	* @param mixed $values the field values
	* @param string $name the field name
	* @return string
	*/
	function renderFieldRoles($values=array(), $name='roles') {
		$roles = $this->_getAllRoles();
		$roles =  array_merge($roles, array('Commentator' => __('Commentators', 'author-avatars')));
		global $coauthors_plus;
		if( $coauthors_plus ){
			$roles =  array_merge($roles, array('coauthors_plus' => __('Co-Author Plus Guest Authors', 'author-avatars')));		
		}

		$html = '';
		if (!empty($roles)) {		
			$attributes = array(
			'id' => $this->_getFieldId($name),
			'expanded' => true,
			'multiple' => true,
			'wrapper_tag' => 'p',
			'label' => '<strong>'. __('Show roles', 'author-avatars') .':</strong><br/>',
		);
		if (AA_is_wpmu() && AA_is_bp()) {
			if (version_compare(BP_VERSION, '1.3', '<=')) {
				$attributes['help'] = '<br /><small class="warning">Warning: Commentator avatars are not displayed properly with BuddyPress versions &lt;= 1.3!</small>';
			}
		}
		$name = $this->_getFieldName($name);
			$html .= FormHelper::choice($name, $roles, $values, $attributes);
		}
		return $html;
	}
	/**
	* Retrieves all roles, and returns them as an associative array (key -> role name) 
	*
	* @access private
	* @return Array of role names.
	*/
	function _getAllRoles() {
		global $wpdb;
		$roles_data = get_option($wpdb->prefix.'user_roles');
		$roles = array();
		foreach($roles_data as $key => $role) {
			$roles[$key] = $this->__roleStripLevel($role['name']);
		}
	return $roles;
	}
	/**
	* Strips the user level from a role name (see option user_roles)
	*
	* @access private
	* @param $element A role name, $role['name']
	* @return the clean role name without user level added on the end.
	*/
	function __roleStripLevel($element) {
		$parts = explode('|', $element);
		return $parts[0];
	}
	/**
	* Renders the hiddenusers input text field.
	*
	* @param string $value the field value
	* @param string $name the field name
	* @return string
	*/
	function renderFieldHiddenUsers($value='', $name='hiddenusers') {
		$attributes = array(
		'id' => $this->_getFieldId($name),
		'label' => '<strong>'. __('Hidden users', 'author-avatars') .':</strong><br/>',
		'help' => '<br/><small>('. __('Comma separate list of user login ids', 'author-avatars'). ')</small>',
		'rows' => 2,
		'style' => 'width:95%;'
		);
		$name = $this->_getFieldName($name);
		return '<p>' . FormHelper::input('text', $name, $value, $attributes) . '</p>';
	}
	/**
	* Renders the set of display options (as it's used in the widget atm)
	*
	* @param string $display_values 
	* @param string $name_base
	* @return string
	* @deprecated this is used on the old widget admin should be replaced by the new tab base one...
	*/
	function renderDisplayOptions ($display_values=array(), $name_base='display') {
		$html = '';
		$html .= $this->renderFieldDisplayOptions($display_values, $name_base);
		$html .= $this->renderFieldUserLink($display_values, $name_base . '[user_link]');
		$html .= $this->renderFieldOrder($display_values['order'], $name_base .'[order]');
		$html .= $this->renderFieldSortDirection($display_values['sort_direction'], $name_base .'[sort_direction]');
		$html .= '<br />';
		$html .= $this->renderFieldLimit($display_values['limit'], $name_base .'[limit]');
		$html .= '<br />';
		$html .= $this->renderFieldMinPostCount($display_values['min_post_count'], $name_base .'[min_post_count]');
		$html .= '<br />';
		$html .= $this->renderFieldAvatarSize($display_values['avatar_size'], $name_base . '[avatar_size]');
		return $html;
	}
	/**
	* Renders the display options checkbox matrix (show name?)
	*
	* @param mixed $values the field values
	* @param string $name the field name
	* @return string
	*/
	function renderFieldDisplayOptions($values=array(), $name='display') {
		$display_options = Array(
			'show_name' => __('Show name', 'author-avatars'),
			'show_email' => __('Show email', 'author-avatars'),
			'show_biography' => __('Show biography', 'author-avatars'),
			'show_postcount' => __('Show number of posts', 'author-avatars'),
		);
		if (AA_is_bbpress()) {
			$display_options['show_bbpress_post_count'] = __('Show BBPress Post Count', 'author-avatars');
		}

		$attributes = array(
			'id' => $this->_getFieldId($name),
			'expanded' => true,
			'multiple' => true,
			'label' => '<strong>'. __('Display options', 'author-avatars') .'</strong><br/>',
			'wrapper_tag' => 'p',
		);
		$name = $this->_getFieldName($name);
		return FormHelper::choice($name, $display_options, $values, $attributes);
	}
	/**
	* Renders the "order by" dropdown
	*
	* @param mixed $values the field values
	* @param string $name the field name
	* @return string
	*/
	function renderFieldOrder($values=array(), $name='order') {
		$order_options = Array(
			'date_registered' => __('Date of Registration', 'author-avatars'),
			'display_name' => __('Display Name', 'author-avatars'),
			'first_name' => __('First Name', 'author-avatars'),
			'last_name' => __('Last Name', 'author-avatars'),
			'user_login' => __('Login Name', 'author-avatars'),
			'post_count' => __('Number of Posts', 'author-avatars'),
			'random' => __('Random', 'author-avatars'),
			'user_id' => __('User Id', 'author-avatars'),
			'recent_post_activity' => __('Recent Posts Activity', 'author-avatars'),
			'recent_site_activity' => __('Recent Sitewide Activity', 'author-avatars'),
		);
		if (AA_is_bbpress()) {
			$order_options['bbpress_post_count'] = __('BBPress Post Count', 'author-avatars');
		}
		if (AA_is_bp()) {
			$order_options['budy_press_recent_activity'] = __('BudyPress last activity', 'author-avatars');
		}
		$attributes = array(
			'id' => $this->_getFieldId($name),
			'label' => __('Sorting order', 'author-avatars') . ': ',
		);
		$name = $this->_getFieldName($name);
		return '<p>'. FormHelper::choice($name, $order_options, $values, $attributes) .'</p>';
	}
	/**
	* Renders the "user_link" dropdown
	*
	* @param mixed $values the field values
	* @param string $name the field name
	* @return string
	*/
	function renderFieldUserLink($values=array(), $name='user_link') {
		$user_link_options = Array(
			'' => '-',
			'authorpage' => __('Author Page', 'author-avatars'),
			'website' => __('Website', 'author-avatars'),
		);
		if (AA_is_bp()) {
			$user_link_options['bp_memberpage'] = __('BP Member Page', 'author-avatars');
		}
		if (AA_is_wpmu()) {
			$user_link_options['blog'] = __('Blog', 'author-avatars');
		}
		if (AA_is_bbpress()) {
			$user_link_options['bbpress_memberpage'] = __('BBpress Member Page', 'author-avatars');
		}	
		$attributes = array(
			'id' => $this->_getFieldId($name),
			'label' => __('Link users to', 'author-avatars') . ': ',
		);
		$name = $this->_getFieldName($name);
		return '<p>'. FormHelper::choice($name, $user_link_options, $values, $attributes) .'</p>';
	}
	/**
	* Renders the "order by" dropdown
	*
	* @param mixed $values the field values
	* @param string $name the field name
	* @return string
	*/
	function renderFieldSortDirection($values=array(), $name='sort_direction') {
		$order_options = Array(
			'asc' => __('Ascending', 'author-avatars'),
			'desc' => __('Descending', 'author-avatars')
		);
		$attributes = array(
			'id' => $this->_getFieldId($name),
			'label' => __('Sorting direction', 'author-avatars') . ': ',
		);
		$name = $this->_getFieldName($name);
		return '<p>'. FormHelper::choice($name, $order_options, $values, $attributes) .'</p>';
	}
	/**
	* Renders the "limit" input field
	*
	* @param string $value the field value
	* @param string $name the field name
	* @return string
	*/
	function renderFieldLimit($value='', $name='limit') {
		$attributes = array(
			'id' => $this->_getFieldId($name),
			'label' => __('Max. number of avatars shown', 'author-avatars') . ': ',
			'size' => '5'
		);
		$name = $this->_getFieldName($name);
		return '<p>'. FormHelper::input('text', $name, $value, $attributes) .'</p>';
	}
	/**
	* Renders the "limit" input field
	*
	* @param string $value the field value
	* @param string $name the field name
	* @return string
	*/
	function renderPageLimit($value='', $name='page_size') {
		$attributes = array(
			'id' => $this->_getFieldId($name),
			'label' => __('Max. number of avatars per page', 'author-avatars') . ': ',
			'size' => '5'
		);
		$name = $this->_getFieldName($name);
		return '<p>'. FormHelper::input('text', $name, $value, $attributes) .'</p>';
	}
	/**
	* Renders the "min_post_count" input field
	*
	* @param string $value the field value
	* @param string $name the field name
	* @return string
	*/
	function renderFieldMinPostCount($value='', $name='min_post_count') {
		$attributes = array(
			'id' => $this->_getFieldId($name),
			'label' => __('Required minimum number of posts', 'author-avatars') . ': ',
			'size' => '5'
		);
		$name = $this->_getFieldName($name);
		return '<p>'. FormHelper::input('text', $name, $value, $attributes) .'</p>';
	}
	/**
	* Renders the avatar size input field.
	*
	* @param string $value the field value
	* @param string $name the field name
	* @param bool $preview If set to true (default) the "avatar_size_preview" div is rendered. jQuery UI and "js/widget.admin.js" needs to included in order for this to work.
	*/
	function renderFieldAvatarSize($value='', $name='avatar_size', $preview=true) {
		$attributes = array(
			'id' => $this->_getFieldId($name),
			'label' => __('Avatar Size', 'author-avatars') . ': ',
			'help' => 'px',
			'class' => 'avatar_size_input',
			'size' => '5'
		);
		$name = $this->_getFieldName($name);
		$html = '<p>'. FormHelper::input('text', $name, $value, $attributes) .'</p>';
		if ($preview == true) {
			global $user_email;
			get_currentuserinfo();
			$html .= '<div class="avatar_size_preview" style="background-color: #666; border: 1px solid #eee; width: 200px; height: 200px; padding: 10px;">'. get_avatar($user_email, $value) .'</div>'; 
		}
		return $html;
	}
	/**
	* Renders the shortcode type selection field
	*
	* @param mixed $values the field values
	* @param string $name the field name
	* @return string
	*/
	function renderFieldShortcodeType($values=array(), $name='shortcode_type') {
		$type_options = array(
			'show_avatar' => __('Single Avatar', 'author-avatars'),
			'authoravatars' => __('List of Users', 'author-avatars'),
		);
		$attributes = array(
			'id' => $this->_getFieldId($name),
			'label' => '<strong>' . __('Shortcode Type', 'author-avatars') . ':</strong><br/>',
			'expanded' => true,
			'inline' => true,
		);
		$name = $this->_getFieldName($name);
		return '<p>'. FormHelper::choice($name, $type_options, $values, $attributes) .'</p>';
	}
	/**
	* Renders the email/userid input field for the show_avatar wizard
	*
	* @param string $value the field value
	* @param string $name the field name
	* @return string
	*/
	function renderFieldEmail($value='', $name='email') {
		$attributes = array(
			'id' => $this->_getFieldId($name),
			'style' => 'width: 95%;',
		);
		$name = $this->_getFieldName($name);
		return  FormHelper::input('text', $name, $value, $attributes) .'</p>';
	}
	/**
	* Renders the User pick input field for the show_avatar wizard
	*
	* @param string $value the field value
	* @param string $name the field name
	* @return string
	*/
	function renderFieldUsers($values=array(), $name='user_id') {
		$attributes = array(
			'id' => $this->_getFieldId($name),
			'label' => '<strong>' . __('Email address or user id', 'author-avatars')  . ':</strong><br/>',
			'style' => 'width: 95%;',
		);

		$users = $this->_get_all_users(array(-1 => __('Custom value', 'author-avatars')));
		return '<p>'. FormHelper::choice($name, $users, $values, $attributes) ;
	}
	/**
	* Retrieves all roles, and returns them as an associative array (key -> role name) 
	*
	* @access private
	* @return Array of role names.
	*/
	function _get_all_users($users = array()) {
		global $wpdb;
		$user_data = get_users();
		foreach($user_data as $key => $user) {
			$users["".$user->ID.""] = $user->user_nicename;
		}
	return $users;
	}



	/**
	* Renders the alignment radio fields for the show_avatar wizard
	*
	* @param mixed $values the field values
	* @param string $name the field name
	* @return string
	*/
	function renderFieldAlignment($values='', $name='alignment') {
		$alignment_options = array(
			'' => __('None', 'author-avatars'),
			'left' => __('Left', 'author-avatars'),
			'center' => __('Center', 'author-avatars'),
			'right' => __('Right', 'author-avatars'),
		);
		$attributes = array(
			'id' => $this->_getFieldId($name),
			'label' => '<strong>' . __('Alignment', 'author-avatars') . '</strong><br/>',
			'expanded' => true,
			'inline' => true,
			'class' => 'alignment',
		);
		$name = $this->_getFieldName($name);
		return '<p>'. FormHelper::choice($name, $alignment_options, $values, $attributes) .'</p>';
	}
	/**
	* Renders an opening tab div
	* 
	* @param string $title The tab title
	* @param string $id tab id (optional). Generated from $title if empty.
	* @return string
	*/
	function renderTabStart($title, $id = '') {
		if (empty($id)) $id = 'tab-'. $title;
			$id = FormHelper::cleanHtmlId($id);
		if (isset($this->tabs[$id])) {
			trigger_error('Warning: id "'. $id .'" has already been used as tab identifier.', E_USER_WARNING);
		}
		else {
			$this->tabs[$id] = $title;
		}
		return '<div id="'. $id .'">';
	}
	/**
	* Renders a closing tab div.
	*
	* @return string
	*/
	function renderTabEnd() {
		return '</div>';
	}
	/**
	* Renders the list of all tabs 
	*
	* @return string
	*/
	function renderTabList() {
		if (empty($this->tabs)) {
			trigger_error('Tabs array is empty. Nothing to render.', E_USER_WARNING);
		return;
		}
		$html = "\n".'<ul>';
		foreach ($this->tabs as $id => $title) {
			$html .= "\n\t".'<li><a href="#'. $id .'">'. $title .'</a></li>';
		}
		$html .= "\n".'</ul>';
		return $html;
	}
	/**
	* Renders the two given bits of html in columns next to each other.
	*
	* @param string $left Contents of the left column
	* @param string $right Contents of the right column
	* @param string html
	*/
	function renderColumns($left='', $right='') {
		if (empty($left) || empty($right)) return $left . $right;
			$html = '<div class="aa-columns aa-clearfix">';
			$html .= '<div class="column-left">'. $left .'</div>';
			$html .= '<div class="column-right">'. $right .'</div>';
			$html .= '</div>';
		return $html;
	}

}

?>