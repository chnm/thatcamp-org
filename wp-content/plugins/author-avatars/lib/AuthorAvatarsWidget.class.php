<?php

/**
 * Author Avatars Widget: displays avatars of blog users.
 */
class AuthorAvatarsWidget extends WP_Widget {
	/**
	 * Default widget options
	 */
	var $defaults = array();

	/**
	 * Sets the defaults.
	 */
	function _setDefaults() {
		$this->defaults = Array(
			'title'          => __( 'Blog Authors', 'author-avatars' ),
			'hiddenusers'    => '',
			'whitelistusers' => '',
			'blogs'          => "",
			'roles'          => array( 'administrator', 'editor' ),
			'group_by'       => '',
			'display'        => array(
				'avatar_size'    => '',
				'limit'          => '',
				'min_post_count' => '',
				'order'          => 'display_name',
				'sort_direction' => 'asc',
				'user_link'      => 'authorpage',
				'bio_length'     => '',
			),
		);

		if ( AA_is_wpmu() ) {
			global $blog_id;
			if ( intval( $blog_id ) > 0 ) {
				$this->defaults['blogs'] = Array( intval( $blog_id ) );
			}
		}
	}


	/**
	 * Widget initialisation
	 */
	function __construct() {


		$this->_setDefaults();

		parent::__construct(
			'author_avatars', // id_base
			'Author Avatars', // name
			array( 'description' => __( 'Displays avatars of blog users.', 'author-avatars' ) ), // widget options
			array( 'width' => '600px' ) // control options
		);
	}

	/**
	 * Echo widget content = list of blog users.
	 */
	function widget( $args, $instance ) {
		require_once( 'UserList.class.php' );

		// parse hidden users string
		if ( ! empty( $instance['hiddenusers'] ) ) {
			$hiddenusers = explode( ',', $instance['hiddenusers'] );
			$hiddenusers = array_map( 'trim', $hiddenusers );
		} else {
			$hiddenusers = array();
		}

		// parse whitelist users string
		if ( ! empty( $instance['whitelistusers'] ) ) {
			$whitelistusers = explode( ',', $instance['whitelistusers'] );
			$whitelistusers = array_map( 'trim', $whitelistusers );
		} else {
			$whitelistusers = array();
		}
		$userlist = new UserList();

		$userlist->roles          = $instance['roles'];
		$userlist->blogs          = $instance['blogs'];
		$userlist->group_by       = $instance['group_by'];
		$userlist->hiddenusers    = $hiddenusers;
		$userlist->whitelistusers = $whitelistusers;

		if ( is_array( $instance['display'] ) ) {

//			$userlist->show_name               = in_array( 'show_name', $instance['display'] );
//			$userlist->show_email              = in_array( 'show_email', $instance['display'] );
//			$userlist->show_postcount          = in_array( 'show_postcount', $instance['display'] );
//			$userlist->show_bbpress_post_count = in_array( 'show_bbpress_post_count', $instance['display'] );
//			$userlist->show_biography          = in_array( 'show_biography', $instance['display'] );
//			$userlist->show_last_post          = in_array( 'show_last_post', $instance['display'] );
			// todo: add array_kay_exsists

			$userlist->user_link = ( array_key_exists( 'user_link', $instance['display'] ) ) ? $instance['display']['user_link'] : false;
			unset( $instance['display']['user_link'] );
			$userlist->avatar_size = ( array_key_exists( 'avatar_size', $instance['display'] ) ) ? $instance['display']['avatar_size'] : false;
			unset( $instance['display']['avatar_size'] );
			$userlist->limit = ( array_key_exists( 'limit', $instance['display'] ) ) ? $instance['display']['limit'] : false;
			unset( $instance['display']['limit'] );
			$userlist->min_post_count = ( array_key_exists( 'min_post_count', $instance['display'] ) ) ? $instance['display']['min_post_count'] : false;
			unset( $instance['display']['min_post_count'] );
			$userlist->order = ( array_key_exists( 'order', $instance['display'] ) ) ? $instance['display']['order'] : false;
			unset( $instance['display']['order'] );
			$userlist->sort_direction = ( array_key_exists( 'sort_direction', $instance['display'] ) ) ? $instance['display']['sort_direction'] : false;
			unset( $instance['display']['sort_direction'] );
			$userlist->bio_length = ( array_key_exists( 'bio_length', $instance['display'] ) ) ? $instance['display']['bio_length'] : false;
			unset( $instance['display']['bio_length'] );
		}
		$display_config_values = array(
			'user_link',
			'avatar_size',
			'limit',
			'min_post_count',
			'order',
			'sort_direction',
		);
		if ( is_array( $instance['display'] ) ) {

			$instance['display'] = apply_filters( 'aa_widget_display_list', $instance['display'] );

			$display = array();
			if ( ! empty( $instance['display'] ) ) {
				if ( ! is_array( $instance['display'] ) ) {
					$display = explode( ',', $instance['display'] );
				} else {
					$display = $instance['display'];
				}
			}

			$default_display_options = array(
				'show_name',
				'show_email',
				'show_biography',
				'show_postcount',
				'show_last_post',
				'show_bbpress_post_count',
			);
			// loop the old name=true settings and add them to the new array format
			foreach ( $default_display_options as $default_display_option ) {
				if ( isset( $instance['display'][ $default_display_option ] ) && ( strlen( $instance['display'][ $default_display_option ] ) > 0 ) ) {
					if ( true == $instance['display'][ $default_display_option ] && ! in_array( $default_display_option, $display ) ) {
						$display[] = $default_display_option;
					}
				}

			}
			// the defaults array and set the globals if found
			foreach ( $default_display_options as $default_display_option ) {
				if ( in_array( $default_display_option, $display ) ) {
					$userlist->$default_display_option = true;
				} else {
					$userlist->$default_display_option = false;
				}
			}

			$userlist->display_extra = array_diff( $display, $default_display_options );

		}


		//var_dump($userlist->display_extra);

		// extract widget arguments
		$before_widget = isset( $args['before_widget'] ) ? $args['before_widget'] : '';
		$before_title  = isset( $args['before_title'] ) ? $args['before_title'] : '';
		$after_title   = isset( $args['after_title'] ) ? $args['after_title'] : '';
		$after_widget  = isset( $args['after_widget'] ) ? $args['after_widget'] : '';


		// add the standard title filter
		$title = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'] );
		// build the widget html
		echo $before_widget;
		echo $before_title . $title . $after_title;

		$userlist->output();

		echo $after_widget;
	}

	/**
	 * Updates a particular instance of the widget.
	 * This function is called every time the widget is saved, and used to validate the data.
	 *
	 * @access protected
	 *
	 * @param $new_instance The new widget options, sent from the widget control form.
	 * @param $old_instance The options of the old instance in case we're updating a widget. This is empty if we're creating a new widget.
	 * @param The           instance of widget options which is saved to the database.
	 *
	 * @return array $instance
	 */
	function update( $new_instance, $old_instance ) {

		$instance                   = $old_instance;
		$instance['title']          = esc_html( $new_instance['title'] );
		$instance['hiddenusers']    = esc_html( $new_instance['hiddenusers'] );
		$instance['whitelistusers'] = esc_html( $new_instance['whitelistusers'] );
		$instance['roles']          = (array) $new_instance['roles'];
		$instance['blogs']          = ( isset( $new_instance['blogs'] ) ) ? (array) $new_instance['blogs'] : array();
		$instance['group_by']       = ( isset( $new_instance['group_by'] ) ) ? esc_html( $new_instance['group_by'] ) : '';
		$instance['display']        = (array) $new_instance['display'];

		$instance['display']['avatar_size'] = absint( $instance['display']['avatar_size'] );

		if ( empty( $instance['blogs'] ) ) {
			$instance['blogs'] = $this->defaults['blogs'];
		}

		return $instance;
	}

	/**
	 * Builds the widget control form.
	 *
	 * @access protected
	 *
	 * @param $instance Array of widget options. If empty then we're creating a new widget.
	 *
	 * @return void
	 */
	function form( $instance ) {
		$instance = array_merge( $this->defaults, $instance );

		require_once( 'AuthorAvatarsForm.class.php' );
		$form = new AuthorAvatarsForm();
		$form->setFieldIdCallback( array( $this, 'get_field_id' ) );
		$form->setFieldNameCallback( array( $this, 'get_field_name' ) );

		if ( ! class_exists( 'AAFormHelper' ) ) {
			require_once( 'AAFormHelper.class.php' );
		}

		// widget title
		$widget_title = '<p>' . AAFormHelper::input( 'text', $this->get_field_name( 'title' ), $instance['title'],
				array(
					'label' => '<strong>' . __( 'Title', 'author-avatars' ) . ':</strong> ',
					'class' => 'widefat',
					'id'    => $this->get_field_id( 'title' ),
				)
			) . '</p>';

		// BASIC TAB
		$basic_left = $widget_title;


		if ( ! isset( $instance['display']['user_link'] ) ) {
			$instance['display']['user_link'] = array();
		}
		if ( ! isset( $instance['display']['avatar_size'] ) ) {
			$instance['display']['avatar_size'] = '';
		}

		$basic_left .= $form->renderFieldRoles( $instance['roles'] );
		$basic_left .= $form->renderFieldUserLink( $instance['display']['user_link'], 'display][user_link' );

		$basic_right = $form->renderFieldDisplayOptions( $instance['display'] );
		$basic_right .= $form->renderFieldAvatarSize( $instance['display']['avatar_size'], 'display][avatar_size' );
		$basic_right .= '<div class="avatar_donate">' . AA_donateButton( 'link' ) . '</div>';

		$basic = '<h5>' . __( 'Basic', 'author-avatars' ) . '</h5>';
		$basic .= $form->renderColumns( $basic_left, $basic_right );

		// ADVANCED TAB
		$adv_left = '';

		if ( array_key_exists( 'order', $instance['display'] ) ) {
			$adv_left .= $form->renderFieldOrder( $instance['display']['order'], 'display][order' );
		} else {
			$adv_left .= $form->renderFieldOrder( array(), 'display][order' );
		}
		echo($adv_left);
		if ( array_key_exists( 'sort_direction', $instance['display'] ) ) {
			$adv_left .= $form->renderFieldSortDirection( $instance['display']['sort_direction'], 'display][sort_direction' );
		}else {
			$adv_left .= $form->renderFieldSortDirection( array(), 'display][sort_direction' );
		}
		if ( array_key_exists( 'limit', $instance['display'] ) ) {
			$adv_left .= $form->renderFieldLimit( $instance['display']['limit'], 'display][limit' );
		}else {
			$adv_left .= $form->renderFieldLimit( '', 'display][limit' );
		}
		if ( array_key_exists( 'bio_length', $instance['display'] ) ) {
			$adv_left .= $form->renderFieldMaxBioLength( $instance['display']['bio_length'], 'display][bio_length' );
		}else {
			$adv_left .= $form->renderFieldMaxBioLength( '', 'display][bio_length' );
		}
		if ( array_key_exists( 'min_post_count', $instance['display'] ) ) {
			$adv_left .= $form->renderFieldMinPostCount( $instance['display']['min_post_count'], 'display][min_post_count' );
		}else {
			$adv_left .= $form->renderFieldMinPostCount( '', 'display][min_post_count' );
		}
		if ( array_key_exists( 'hiddenusers', $instance ) ) {
			$adv_left .= $form->render_field_hidden_users( $instance['hiddenusers'] );
		}
		if ( array_key_exists( 'whitelistusers', $instance ) ) {
			$adv_left .= $form->render_field_white_list_users( $instance['whitelistusers'] );
		}

		$adv_right = "";
		// incase we don't have any blogs
		if ( AA_is_wpmu() && ! empty( $instance['blogs'] ) ) {
			$adv_right .= $form->renderFieldBlogs( $instance['blogs'] );
		}


		$adv_right .= $form->renderFieldGroupBy( $instance['group_by'] );

		$advanced = '<h5>' . __( 'Advanced', 'author-avatars' ) . '</h5>';
		$advanced .= $form->renderColumns( $adv_left, $adv_right );

		echo '<div class="aa-widget-control-panel">' . $basic . $advanced . '</div>';

		// hidden "submit=1" field (do we still need this?, FIXME)
		echo AAFormHelper::input( 'hidden', $this->get_field_name( 'submit' ), '1', array( 'id' => $this->get_field_id( 'submit' ) ) );
	}

	function get_field_name( $varname ) {
		// replace all x[y] by x][y
		$varname = str_replace( ']', '', $varname );
		$varname = str_replace( '[', '][', $varname );

		return parent::get_field_name( $varname );
	}
}