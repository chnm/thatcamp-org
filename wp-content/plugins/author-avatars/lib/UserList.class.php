<?php

/**
 * User list class: provides a filtered and ordered list of users and different ways of outputting them.
 */
class UserList {

	/**
	 * Constructor
	 */
	function UserList() {
	}

	/**
	 * Array of users that are not displayed
	 */
	var $hiddenusers = array();

	/**
	 * Array of users that are to be displayed
	 */
	var $onlyusers = array();
	/**
	 * Array of blog ids which to take users from. (empty = only current blog, "-1" = all blogs)
	 */
	var $blogs = array();

	/**
	 * Array of role names. Only users belonging to one of these roles are displayed.
	 */
	var $roles = array( 'administrator', 'editor' );

	/**
	 * Grouping of users. For example set to "blog" to group users by blogs.
	 */
	var $group_by = '';

	/**
	 * Link the user to either the "authorpage", "blog" (wpmu) or "website"
	 */
	var $user_link = 'authorpage';

	/**
	 * Flag whether to show the username underneith their avatar.
	 */
	var $show_name = false;

	/**
	 * Flag wether to show the post count for each user after the username.
	 */
	var $show_postcount = false;
	/**
	 * Flag wether to show the post count for each user after the username.
	 */
	var $show_bbpress_post_count = false;

	/**
	 * Flag whether to show a user's biography
	 */
	var $show_biography = false;

	/**
	 * Maximum number of words in bio
	 */
	var $bio_length = -1;

	/**
	 * Flag whether to show a user's biography
	 */
	var $show_last_post = false;
	/**
	 * Flag whether to show a user's email
	 */
	var $show_email = false;

	/**
	 * Size of avatars.
	 */
	var $display_extra = array();

	/**
	 * Size of avatars.
	 */
	var $avatar_size = 0;

	/**
	 * Maximum number of users.
	 */
	var $limit = 0;

	/**
	 * Minimum number of posts which a user needs to have in order to be shown in the listing
	 */
	var $min_post_count = 0;

	/**
	 * Minimum number of posts which a user needs to have in order to be shown in the listing
	 */
	var $page_size = 0;
	var $aa_page = 0;
	/**
	 * The page number hrml
	 */
	var $pagingHTML = '';

	/**
	 * The order which the users are shown in.
	 */
	var $order = 'display_name';

	/**
	 * The direction which the users are sorted in.
	 * Possible values: 'ascending' / 'asc' or 'descending' / 'desc'.
	 */
	var $sort_direction = 'asc';

	/**
	 * Group wrapper template
	 * - {groups} is replaced by the list of groups
	 */
	var $group_wrapper_template = '<div class="grouped-author-list">{groups}</div>';

	/**
	 * Group template
	 * - {name} is replaced by the name of the group
	 * - {group} is replaced by the list of users
	 */
	var $group_template = '<div class="author-group"><strong>{name}</strong><br/>{group}</div>';

	/**
	 * Wrapper template
	 * - {users} is replaced by the list of users
	 */
	var $userlist_template = '<div class="author-list">{users}</div>';

	/**
	 * User template
	 * - {class} is replaced by user specific classes
	 * - {user} is replaced by the user avatar (and possibly name)
	 */
	var $user_template = '<div class="{class}">{user}</div>';

	/**
	 * Changes the template strings so the user is rendered in a html list.
	 *
	 * @param $ordered set to true to use an ordered list (<ol>) instead of an unordered one (<ul>)
	 *
	 * @return void
	 */
	function use_list_template( $ordered = false ) {
		if ( (bool) $ordered ) {
			$this->userlist_template = '<ol class="author-list">{users}</ol>';
		} else {
			$this->userlist_template = '<ul class="author-list">{users}</ul>';
		}
		$this->user_template = '<li class="{class}">{user}</li>';
	}

	/**
	 * Echos the list of users.
	 *
	 * @return void
	 */
	function output() {
		echo $this->get_output();
	}

	/**
	 * Echos the list of users.
	 *
	 * @return void
	 */
	public function ajax_output() {
		// pass all the value into class
		echo $this->get_output();
	}

	/**
	 * Returns the list of users.
	 *
	 * @uses apply_filters() Calls 'aa_userlist_empty' hook
	 * @return String the html formatted list of users
	 */
	function get_output() {
		// get users
		$users = $this->get_users();

		if ( empty( $users ) ) {
			return apply_filters( 'aa_userlist_empty', '<p class="no_users">' . __( 'No users found.', 'author-avatars' ) . '</p>' );
		} elseif ( ! empty( $this->group_by ) ) {
			return $this->format_groups( $this->page_users( $users ) );
		} else {
			return $this->format_users( $this->page_users( $users ) );
		}


	}


	/**
	 * Formats a grouped list of users
	 *
	 * @param Array $groups Array of an array of users. The array keys are used to retrieve the group name (see _group_name())
	 *
	 * @uses apply_filters() Calls 'aa_userlist_group_wrapper_template' hook
	 * @uses apply_filters() Calls 'aa_userlist_group_template' hook
	 * @return String the html formatted list of grouped users
	 */
	function format_groups( $groups ) {
		$html = '';
		foreach ( $groups as $id => $group_users ) {
			$tpl_vars = array(
				'{name}'  => $this->_group_name( $id ),
				'{group}' => $this->format_users( $group_users ),
			);

			$html .= str_replace( array_keys( $tpl_vars ), $tpl_vars, apply_filters( 'aa_userlist_group_template', $this->group_template ) );
		}

		return str_replace( '{groups}', $html, apply_filters( 'aa_userlist_group_wrapper_template', $this->group_wrapper_template ) );
	}

	/**
	 * Formats a list of users
	 *
	 * @param Array $groups An array of users.
	 *
	 * @uses apply_filters() Calls 'aa_userlist_template' hook
	 * @return String the html formatted list of users
	 */
	function format_users( $users ) {
		$html = '';
		foreach ( $users as $user ) {
			$html .= $this->format_user( $user );
		}

		return str_replace( '{users}', $html, apply_filters( 'aa_userlist_template', $this->userlist_template ) );
	}

	/**
	 * pages the list of users
	 *
	 * @param Array $groups An array of users.
	 *
	 * @return Array list of users
	 */
	function page_users( $users ) {
		if ( empty( $this->page_size ) ) {
			return $users;
		}
		$this->setup_page_users_ajax();
		$page_size = $this->page_size;

		if ( $this->aa_page > 0 ) {
			$offset = $this->aa_page * $page_size;
			// so we can't try to slice more than the array test offset is not more than array length
			// and is show the last page posiable
			if ( $offset >= count( $users ) ) {
				$offset = count( $users ) - $page_size;
			}
		} else {
			$offset = 0;
		}

		$this->pagingHTML = '<div class="aa_pageList"><div><a href="?aa_page=0" id="0">' . __( '<<', 'author-avatars' ) . '</a>';
		for ( $i = 0; $i <= count( $users ) / $page_size; $i ++ ) {
			$this->pagingHTML .= '<a href="?aa_page=' . $i . '" id="' . ( $i ) . '"';
			if ( $i == $this->aa_page ) {
				$this->pagingHTML .= 'class="current"';
			}
			$this->pagingHTML .= '>' . $i . '</a>';
		}
		$this->pagingHTML .= '<a href="?aa_page=' . ( $i - 1 ) . '" id="' . ( $i - 1 ) . '">' . __( '>>', 'author-avatars' ) . '</a></div></div>';


		return array_slice( $users, $offset, $page_size );
	}

	/**
	 * Ajax to update pages the list of users
	 *
	 *
	 * @return void
	 */
	function setup_page_users_ajax() {

		// create axjax calls
		wp_register_script( 'author-avatars-shortcode-paging', plugins_url( '../js/AuthorAvatarsShortcode.paging.ajax.js', __FILE__ ), array( 'jquery-ui-core' ), '', true );

		// pass values to JS
		$params = array(

			'hiddenusers'             => $this->hiddenusers,
			'blogs'                   => $this->blogs,
			'roles'                   => $this->roles,
			'group_by'                => $this->group_by,
			'user_link'               => $this->user_link,
			'show_name'               => $this->show_name,
			'show_postcount'          => $this->show_postcount,
			'show_bbpress_post_count' => $this->show_bbpress_post_count,
			'show_biography'          => $this->show_biography,
			'bio_length'			  => $this->bio_length,
			'show_last_post'          => $this->show_last_post,
			'show_email'              => $this->show_email,
			'avatar_size'             => $this->avatar_size,
			'limit'                   => $this->limit,
			'min_post_count'          => $this->min_post_count,
			'page_size'               => $this->page_size,
			'order'                   => $this->order,
			'sort_direction'          => $this->sort_direction,
			'postCommentNonce'        => wp_create_nonce( 'author-avatars-shortcode-paging-nonce' ),
			'action'                  => 'AA_shortcode_paging',
			'aa_page'                 => 0,
			'ajax_url'                => admin_url( 'admin-ajax.php' )
		);

		wp_enqueue_script( 'author-avatars-shortcode-paging' );
		wp_localize_script( 'author-avatars-shortcode-paging', 'shortCodeValues', $params );

	}


	/**
	 * Formats the given user as html.
	 *
	 * @param WP_User $user The user to format (object of type WP_User).
	 *
	 * @uses apply_filters() Calls 'aa_user_template' hook
	 * @return String html
	 */
	function format_user( $user ) {
		$tpl_vars = array( '{class}' => '', '{user}' => '' );

		$avatar_size = intval( $this->avatar_size );
		if ( ! $avatar_size ) {
			$avatar_size = false;
		}

		$name = "";
		if ( $this->show_name ) {
			$name = $user->display_name;
		}

		$alt = $title = $name;

		$divcss = array( 'user' );
		if ( $this->show_name ) {
			$divcss[] = 'with-name';
		}

		$link      = false;
		$link_type = $this->user_link;

		// always use 'website' for commentators
		$type = ( isset( $user->type ) ) ? $user->type : null;

		if ( $user->user_id == - 1 && "guest-author" != $type ) {
			$link_type = 'website';
		}

		switch ( $link_type ) {
			case 'authorpage':
				if ( "guest-author" == $type ) {
					$link = get_author_posts_url( $user->user_id, $user->user_nicename );
				} else {
					$link = get_author_posts_url( $user->user_id );
				}
				break;
			case 'website':
				if ( "guest-author" == $type ) {
					$link = get_the_author_meta( 'url', $user->ID );
				} else {
					$link = $user->user_url;
					if ( empty( $link ) || $link == 'http://' ) {
						$link = false;
					}
				}

				break;
			case 'blog':
				if ( AA_is_wpmu() ) {
					$blog = get_active_blog_for_user( $user->user_id );
					if ( ! empty( $blog->siteurl ) ) {
						$link = $blog->siteurl;
					}
				}
				break;
			case 'bp_memberpage':
				if ( function_exists( 'bp_core_get_user_domain' ) ) {
					$link = bp_core_get_user_domain( $user->user_id );
				} elseif ( function_exists( 'bp_core_get_userurl' ) ) { // BP versions < 1.1
					$link = bp_core_get_userurl( $user->user_id );
				}
				break;
			case 'bbpress_memberpage':
				if ( function_exists( 'bbp_get_user_profile_url' ) ) {
					$link = bbp_get_user_profile_url( $user->user_id );
				}
				if ( empty( $link ) || $link == 'http://' ) {
					$link = false;
				}
				break;
		}

		if ( $this->show_postcount ) {
			$postcount = 0;

			if ( $user->user_id == - 1 && "guest-author" != $type ) {
				$postcount = $this->get_comment_count( $user->user_email );
				$title .= ' (' . sprintf( _n( "%d comment", "%d comments", $postcount, 'author-avatars' ), $postcount ) . ')';
			} else {
				// this is passing 1 for coauthors

				if ( "guest-author" == $type && $user->linked_account ) {
					$linked_user = get_user_by( 'login', $user->linked_account );
					// fetch the linked account and show thats count
					$postcount = $this->get_user_postcount( $linked_user->ID );
				} else {
					$postcount = $this->get_user_postcount( $user->user_id );
				}

				$title .= ' (' . sprintf( _n( "%d post", "%d posts", $postcount, 'author-avatars' ), $postcount ) . ')';
			}
			$name .= sprintf( apply_filters( 'aa_post_count', ' (%d)', $postcount ), $postcount );
		}

		if ( $this->show_bbpress_post_count && AA_is_bbpress() ) {
			$BBPRESS_postcount = 0;
			if ( function_exists( 'bbp_get_user_topic_count_raw' ) ) {
				$BBPRESS_postcount = bbp_get_user_topic_count_raw( $user->user_id ) + bbp_get_user_reply_count_raw( $user->user_id );
				$title .= ' (' . sprintf( _n( "%d BBPress post", "%d BBPress posts", $BBPRESS_postcount, 'author-avatars' ), $BBPRESS_postcount ) . ')';
			}
			$name .= sprintf( ' (%d)', $BBPRESS_postcount );
		}

		$biography = false;

		if ( $this->show_biography ) {
			if ( "guest-author" != $type && $user->user_id > 0 ) {
				$biography = get_the_author_meta( 'description', $user->user_id );
			} else {
				$biography = ( isset( $user->description ) ) ? $user->description : '';
			}

			$biography = apply_filters( 'aa_user_biography_filter', $biography );


			// trim $biography to bio_length
			if( 0 <=  $this->bio_length ){
				$biography =   $this->truncate_html( wpautop( $biography, true ) , apply_filters( 'aa_user_bio_length', $this->bio_length ) );
			}

			$divcss[] = 'with-biography bio-length-'.$this->bio_length;
			$name     = '<strong>' . $name . '</strong>';
			if ( empty( $biography ) ) {
				$divcss[] = 'biography-missing';
			}
		}

		$show_last_post = false;

		if ( $this->show_last_post ) {
			$show_last_post = $this->aa_get_last_post( $user->user_id );
			$show_last_post = apply_filters( 'aa_user_show_last_post_filter', $show_last_post );
			$divcss[] = 'with-last-post';

			if ( empty( $show_last_post ) ) {
				$divcss[] = 'last-post-missing';
			}
		}

		$email = false;
		if ( $this->show_email && $user->user_email ) {
			$userEmail = $user->user_email;
			$email     = "<a href='mailto:" . $userEmail . "''>" . $userEmail . "</a>";
			$divcss[]  = 'with-email';
			if ( empty( $email ) ) {
				$divcss[] = 'email-missing';
			}
		}

		if ( $user->user_id == - 1 ) {
			// use email for commentators
			$avatar = get_avatar( $user->user_email, $avatar_size );
		} else {
			if ( function_exists( 'bp_core_fetch_avatar' ) ) {
				$avatar = bp_core_fetch_avatar( array(
					'item_id' => $user->user_id,
					'width'   => $avatar_size,
					'height'  => $avatar_size,
					'type'    => 'full',
					'alt'     => $alt,
					'title'   => $title
				) );
			} else {
				$avatar = get_avatar( $user->user_id, $avatar_size );
			}
		}

		/* Strip all existing links (a tags) from the get_avatar() code to
		* remove e.g. the link which is added by the add-local-avatar plugin
		* @see http://wordpress.org/support/topic/309878 */
		if ( ! empty( $link ) ) {
			$avatar = preg_replace( '@<\s*\/?\s*[aA]\s*.*?>@', '', $avatar );
		}

		if ( ! function_exists( 'bp_core_fetch_avatar' ) ) {
			/* strip alt and title parameter */
			$avatar = preg_replace( '@alt=["\'][\w]*["\'] ?@', '', $avatar );
			$avatar = preg_replace( '@title=["\'][\w]*["\'] ?@', '', $avatar );
			/* insert alt and title parameters */
			if ( ! stripos( $avatar, 'title=' ) ) {
				$avatar = preg_replace( '@ ?\/>@', ' title="' . $title . '" />', $avatar );
			}
			if ( ! stripos( $avatar, 'alt=' ) ) {
				$avatar = preg_replace( '@ ?\/>@', ' alt="' . $alt . '"  />', $avatar );
			}
		}

		$html = '';
		$html .= sprintf( apply_filters( 'aa_user_avatar_template', '<span class="avatar" title="%s">%s</span>',$title, $avatar ), $title, $avatar  );
		if ( $this->show_name || $this->show_bbpress_post_count || $this->show_postcount ) {
			$html .=  sprintf( apply_filters( 'aa_user_name_template', '<span class="name">%s</span>', $name ), $name );		}

		if ( $link ) {
			$html = sprintf( apply_filters( 'aa_user_link_template', '<a href="%s" title="%s">%s</a>', $link , $title  , $html ), $link , $title , $html );
		}

		if ( $email ) {
			$html .=  sprintf( apply_filters( 'aa_user_email_template', '<div class="email">%s</div>', $email ), $email );
		}

		if ( $biography ) {
			$html .= sprintf( apply_filters( 'aa_user_biography_template', '<div class="biography">%s</div>', $biography ), $biography );
		}

		if ( $show_last_post ) {
			$html .= sprintf( apply_filters( 'aa_user_last_post_template', '<div class="show_last_post">%s</div>', $show_last_post ), $show_last_post );
		}

		if ( ! empty ( $this->display_extra ) ) {
			$html .= apply_filters( 'aa_user_display_extra', $this->display_extra, $user );
		}

		$tpl_vars['{class}'] = implode( $divcss, ' ' );
		$tpl_vars['{user}']  =  apply_filters( 'aa_user_final_content', $html, $user );

		return str_replace( array_keys( $tpl_vars ), $tpl_vars, apply_filters( 'aa_user_template', $this->user_template, $user ) );
	}

	/**
	 * Returns a filtered and sorted list of users
	 *
	 * @return Array of users (WP_User objects), filtered, sorted and limited to the maximum number.
	 */
	function get_users() {

		global $blog_id;
		$random_order = false;

		$cache_id = join( "_", $this->roles ) . "_" . $blog_id;
		if ( ! empty( $this->blogs ) ) {
			$cache_id .= "_" . join( "_", $this->blogs );
		}
		// if onlyusers then add
		if ( ! empty( $this->onlyusers ) ) {
			$cache_id .= "_" . join( "_", $this->onlyusers );
		}
		// if limit set then add
		if ( ! empty( $this->limit ) ) {
			$cache_id .= "_".$this->limit;
		}
		// if order set then add
		if ( ! empty( $this->order ) ) {
			$cache_id .= "_" . $this->order;
			$random_order = ('random' == $this->order)?true:false;
		}
		// if hidden user set then add
		if ( ! empty( $this->hiddenusers ) ) {
			$cache_id .= "_" . join( "_", $this->hiddenusers );
		}


		// if the use is loged in wipe any cache
		if ( is_user_logged_in() ) {
			delete_transient( $cache_id );
		}

		$users = get_transient( $cache_id );

		if ( false === $users ) {
			$users = array();
			if ( ! empty( $this->onlyusers ) ) {
				$args          = array(
					'include' => $this->onlyusers,
					'fields'  => 'all_with_meta'
				);
				$users_objects = get_users( $args );
				if ( false != $users_objects ) {
					$users = $users_objects;
				}
			} else {
				// get all users
				$users = $this->get_blog_users( $this->roles );

				// add commentators if requested
				if ( in_array( 'Commentator', $this->roles ) ) {
					$commentators = $this->get_commentators();
					if ( is_array( $users ) && is_array( $commentators ) ) {
						$users = array_merge( $users, $commentators );
					} else if ( is_array( $commentators ) ) {
						$users = $commentators;
					}
				}
				// lets get all the co-author not maped to WP users

				if ( in_array( 'coauthors_plus', $this->roles ) ) {
					global $coauthors_plus;
					$args = array( 'orderby' => 'term_order', 'order' => 'ASC', );
					//	$args = array(
					// 		'optioncount'      => false,
					// 		'show_fullname'    => true,
					// 		'hide_empty'       => false,
					// 		'feed'             => '',
					// 		'feed_image'       => '',
					// 		'feed_type'        => '',
					// 		'echo'             => false,
					// 		'html'             => false,
					// 		'number'           => 99,
					// );
					$coauthors = array();

					//	$coauthor_terms = coauthors_wp_list_authors( $args );

					$coauthor_terms = get_terms( $coauthors_plus->coauthor_taxonomy, $args );

					if ( is_array( $coauthor_terms ) && ! empty( $coauthor_terms ) ) {
						foreach ( $coauthor_terms as $coauthor ) {
							$coauthor_slug = preg_replace( '#^cap\-#', '', $coauthor->slug );
							$post_author   = $coauthors_plus->get_coauthor_by( 'user_nicename', $coauthor_slug );

							// In case the user has been deleted while plugin was deactivated
							if ( ! empty( $post_author ) ) {
								$post_author->user_id  = - 1; // to stop the fliter from breaking
								$post_author->user_url = $post_author->website;
								$coauthors[]           = $post_author;
							}

						}
						$users = array_merge( $users, $coauthors );
					}
				}
			}

			// filter them
			$this->_filter( $users );

			// sort them
			$this->_sort( $users );

			// group them
			$this->_group( $users );

			// and limit the number
			if ( intval( $this->limit ) > 0 ) {
				$users = AA_atrim( $users, intval( $this->limit ) );
			}

			set_transient( $cache_id, $users, 1 * HOUR_IN_SECONDS );
		}

		if( $random_order ){
			$this->_sort( $users );
		}
		return $users;
	}

	/**
	 * @param $user_id
	 *
	 * @return null|string
	 */
	function aa_get_last_post( $user_id ){
		$args=array(
			'author' => $user_id ,
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => 1,
			'ignore_sticky_posts'=> 1
		);
		$my_query = null;
		$out = null;
		$my_query = new WP_Query($args);
		if( $my_query->have_posts() ) {
			while ($my_query->have_posts()) : $my_query->the_post();
				$id = $my_query->posts[0]->ID;
				$out .= sprintf('<a href="%s" rel="bookmark" title="Permanent Link to %s">%s</a>',
					get_the_permalink( $id ),
					the_title_attribute( array( 'echo'=>false, 'post'=>$id ) ),
					get_the_title( $id )
				);
			endwhile;
		}
		wp_reset_query();  // Restore global post data stomped by the_post().
		return $out;
	}

	/**
	 * Returns array of all users from all blogs specified in field $blogs.
	 * If $blogs is empty only users from the current blog are returned.
	 *
	 * @return Array of users (WP_User objects).
	 */
	function get_blog_users( $roles ) {
		global $wpdb;

		if ( AA_is_wpmu() && ! empty( $this->blogs ) ) {

			// make sure all values are integers
			$this->blogs = array_map( 'intval', $this->blogs );

			// if -1 is in the array display all users (no filtering)
			if ( in_array( '-1', $this->blogs ) ) {
				$blogs_condition = "meta_key LIKE '" . $wpdb->base_prefix . "%capabilities'";
			} // else filter by set blog ids
			else {
				$blogs           = array_map( create_function( '$v', 'global $wpdb; return "\'" . $wpdb->get_blog_prefix($v) . "capabilities\'";' ), $this->blogs );
				$blogs_condition = 'meta_key IN (' . implode( ', ', $blogs ) . ')';
			}
		} else {
			$blogs_condition = "meta_key = '" . $wpdb->prefix . "capabilities'";
		}

		$roleQuery = "";
		foreach ( $roles as $role ) {
			$role = "%" . $role . "%";
			$or   = "";
			if ( $roleQuery ) {
				$or = " or ";
			}
			$roleQuery .= $wpdb->prepare( $or . "meta_value like %s", $role );
		}
		if ( $roleQuery ) {
			$roleQuery = " AND(" . $roleQuery . ")";
		}

		$query = "SELECT user_id, user_login, display_name, user_email, user_url, user_registered, meta_key, meta_value FROM $wpdb->users, $wpdb->usermeta" .
		         " WHERE " . $wpdb->users . ".ID = " . $wpdb->usermeta . ".user_id AND " . $blogs_condition . " AND user_status = 0" . $roleQuery;

		$users = $wpdb->get_results( $query );

		return $users;
	}

	/**
	 * Returns an array of all commentators
	 *
	 * @return array of users (commentators)
	 */
	function get_commentators() {
		global $wpdb;

		$query = "SELECT
			'-1' as user_id,
			comment_author_email as 'user_login',
			comment_author as 'display_name',
			comment_author_email as 'user_email',
			comment_author_url as'user_url',
			comment_date as 'user_registered',
			'wp_capabilities' as 'meta_key',
			'" . serialize( array( 'Commentator' => true ) ) . "' as 'meta_value'
			FROM " . $wpdb->comments . "
			WHERE comment_author_email <> '' AND comment_approved = 1 AND comment_type NOT IN( 'trackback', 'pingback' )";

		$commentators = $wpdb->get_results( $query );

		return $commentators;
	}

	/**
	 * Filters the given array of users by $roles and $hiddenusers if set.
	 *
	 * @access private
	 *
	 * @param $users Array of users (WP_User objects). (by reference)
	 *
	 * @return void
	 */
	function _filter( &$users ) {
		if ( is_array( $users ) ) {
			// arrays for keeping track of all 'valid' user ids and commentator emails
			$user_ids    = array();
			$user_emails = array();

			foreach ( $users as $id => $usr ) {
				$user = & $users[ $id ];
				$add  = true;

				// Check user role
				// if we have set some roles to restrict by
				$type = ( isset( $user->type ) ) ? $user->type : null;
				// don't fileter gust authors
				if ( "guest-author" != $type ) {
					if ( is_array( $this->roles ) && ! empty( $this->roles ) ) {
						if ( ! isset( $user->user_roles ) ) {
							if ( isset( $user->meta_value ) ) {
								$user->user_roles = array_keys( unserialize( $user->meta_value ) );
							} else {
								$user->user_roles = $user->roles;
							}

						}
						// if the current user does not have one of those roles
						if ( ! AA_array_in_array( $user->user_roles, $this->roles ) ) {
							// do not add this user
							$add = false;
						}
					}
				}

				// Hide hidden users
				if (
					// if we have set some users which we want to hide
					is_array( $this->hiddenusers ) && ! empty( $this->hiddenusers ) &&
					// and the current user is one of them
					( in_array( $user->user_login, $this->hiddenusers ) || in_array( $user->user_id, $this->hiddenusers ) )
				) {
					// do not add this user
					$add = false;
				}

				// real user
				if ( $user->user_id != - 1 ) {
					// Remove duplicates
					if (
						// if we're not grouping anything
						empty( $this->group_by ) &&
						// and the current value has already been added
						in_array( $user->user_id, $user_ids )
					) {
						// do not add this user
						$add = false;
					}

					// Remove users with zero posts
					if (
						// if the flag is set to remove respective users
						$this->min_post_count > 0 &&
						// and they have zero posts
						$this->get_user_postcount( $user->user_id ) < $this->min_post_count
					) {
						// do not add this user
						$add = false;
					}
				} // commentator
				else {
					if (
						// if we're not grouping anything
						empty( $this->group_by ) &&
						// and the current value has already been added
						in_array( $user->user_email, $user_emails )
					) {
						// do not add this user
						$add = false;
					}
				}

				if ( $add === true ) {
					// store current user_id/user_email for uniqueness check
					$user_ids[]    = $user->user_id;
					$user_emails[] = $user->user_email;
				} else {
					// remove the current user from the array
					unset( $users[ $id ] );
				}
			}
		}
	}

	/**
	 * Returns 1 if the sort direction is "ascending" and -1 if it is "descending"
	 *
	 * @access private
	 * @return int '-1' if field $sort_direction is 'desc', '1' otherwise.
	 */
	function _sort_direction() {
		if ( $this->sort_direction == 'desc' || $this->sort_direction == 'descending' ) {
			return - 1;
		} else {
			return 1;
		}
	}

	/**
	 * Sorts the given array of users.
	 *
	 * @access private
	 *
	 * @param Array $users Array of users (WP_User objects). (by reference)
	 * @param String $order The key to sort by. Can be one of the following: random, user_id, user_login, display_name.
	 *
	 * @return void
	 */
	function _sort( &$users, $order = false ) {
		if ( ! $order ) {
			$order = $this->order;
		}

		switch ( $order ) {
			case 'random':
				shuffle( $users );
				break;
			case 'user_id':
				@usort( $users, array( $this, '_users_cmp_id' ) );
				break;
			case 'user_login':
				@usort( $users, array( $this, '_users_cmp_login' ) );
				break;
			case 'display_name':
				@usort( $users, array( $this, '_users_cmp_name' ) );
				break;
			case 'first_name':
				@usort( $users, array( $this, '_users_cmp_first_name' ) );
				break;
			case 'last_name':
				@usort( $users, array( $this, '_users_cmp_last_name' ) );
				break;
			case 'post_count':
				@usort( $users, array( $this, '_user_cmp_postcount' ) );
				break;
			case 'bbpress_post_count':
				@usort( $users, array( $this, '_user_cmp_BBPRESS_post_count' ) );
				break;
			case 'date_registered':
				@usort( $users, array( $this, '_user_cmp_regdate' ) );
				break;
			case 'recent_site_activity':
				@usort( $users, array( $this, '_user_cmp_site_activity' ) );
				break;
			case 'recent_activity': // load posts as the default for old settings
			case 'recent_post_activity':
				@usort( $users, array( $this, '_user_cmp_post_activity' ) );
				break;
			case 'budy_press_recent_activity':
				@usort( $users, array( $this, '_user_cmp_budypress_activity' ) );
				break;
		}
	}

	/**
	 * Given two users, this function compares the user_ids.
	 *
	 * @access private
	 *
	 * @param WP_User $a
	 * @param WP_User $b
	 *
	 * @return int result of a string compare of the user_ids.
	 */
	function _users_cmp_id( $a, $b ) {
		if ( $a->user_id == $b->user_id ) {
			return 0;
		}

		return $this->_sort_direction() * ( $a->user_id < $b->user_id ? 1 : - 1 );
	}

	/**
	 * Given two users, this function compares the user_logins.
	 *
	 * @access private
	 *
	 * @param WP_User $a
	 * @param WP_User $b
	 *
	 * @return int result of a string compare of the user_logins.
	 */
	function _users_cmp_login( $a, $b ) {
		return $this->_sort_direction() * strcasecmp( remove_accents( $a->user_login ), remove_accents( $b->user_login ) );
	}

	/**
	 * Given two users, this function compares the user's display names.
	 *
	 * @access private
	 *
	 * @param WP_User $a
	 * @param WP_User $b
	 *
	 * @return int result of a string compare of the user display names.
	 */
	function _users_cmp_name( $a, $b ) {
		return $this->_sort_direction() * strcasecmp( remove_accents( $a->display_name ), remove_accents( $b->display_name ) );
	}

	/**
	 * Given two users, this function compares the user's display names.
	 *
	 * @access private
	 *
	 * @param WP_User $a
	 * @param WP_User $b
	 *
	 * @return int result of a string compare of the user first names.
	 */
	function _users_cmp_first_name( $a, $b ) {
		$an = remove_accents( $this->get_user_firstname( $a->user_id ) );
		$bn = remove_accents( $this->get_user_firstname( $b->user_id ) );

		return $this->_sort_direction() * strcasecmp( $an, $bn );
	}

	/**
	 * Given a user id returns the first name of the respective user.
	 *
	 * @param int $user_id
	 *
	 * @return string first name of user
	 */
	function get_user_firstname( $user_id ) {
		return get_user_meta( $user_id, 'first_name', true );
	}

	/**
	 * Given two users, this function compares the user's last names.
	 *
	 * @access private
	 *
	 * @param WP_User $a
	 * @param WP_User $b
	 *
	 * @return int result of a string compare of the user display names.
	 */
	function _users_cmp_last_name( $a, $b ) {
		$an = remove_accents( $this->get_user_lastname( $a->user_id ) );
		$bn = remove_accents( $this->get_user_lastname( $b->user_id ) );

		return $this->_sort_direction() * strcasecmp( $an, $bn );
	}

	/**
	 * Given a user id returns the last name of the respective user.
	 *
	 * @param int $user_id
	 *
	 * @return string last name of user
	 */
	function get_user_lastname( $user_id ) {
		return get_user_meta( $user_id, 'last_name', true );
	}

	/**
	 * Given two users, this function compares the user's post count.
	 *
	 * @access private
	 *
	 * @param WP_User $a
	 * @param WP_User $b
	 *
	 * @return int result of a string compare of the user display names.
	 */
	function _user_cmp_postcount( $a, $b ) {
		$ac = $this->get_user_postcount( $a->user_id );
		$bc = $this->get_user_postcount( $b->user_id );

		if ( $ac == $bc ) {
			return 0;
		}

		return $this->_sort_direction() * ( $ac < $bc ? - 1 : 1 );
	}

	/**
	 * Given two users, this function compares the user's post count.
	 *
	 * @access private
	 *
	 * @param WP_User $a
	 * @param WP_User $b
	 *
	 * @return int result of a string compare of the user display names.
	 */
	function _user_cmp_BBPRESS_post_count( $a, $b ) {
		$ac = bbp_get_user_topic_count_raw( $a->user_id ) + bbp_get_user_reply_count_raw( $a->user_id );
		$bc = bbp_get_user_topic_count_raw( $b->user_id ) + bbp_get_user_reply_count_raw( $b->user_id );

		if ( $ac == $bc ) {
			return 0;
		}

		return $this->_sort_direction() * ( $ac < $bc ? - 1 : 1 );
	}

	/**
	 * Returns the postcount for a given user.
	 * On WPMU sites posts are counted from all blogs in field $blogs and summed up.
	 *
	 * @param int $user_id
	 *
	 * @return int post count
	 */
	function get_user_postcount( $user_id ) {
		$total = 0;
		if ( AA_is_wpmu() && ! empty( $this->blogs ) ) {
			$blogs = $this->blogs;
			// all blogs -> only search the user's blogs
			if ( in_array( '-1', (array) $this->blogs ) ) {
				$blogs = (array) $this->get_user_blogs( $user_id );
			}
			foreach ( $blogs as $blog_id ) {
				switch_to_blog( $blog_id );
				$total += count_user_posts( $user_id );
			}
			// reset to current blog done out side to save lot of switching
			restore_current_blog();
		} else {
			$total += count_user_posts( $user_id );
		}

		return $total;
	}

	/**
	 * Returns the comment count for a given email address.
	 *
	 * @param string $user_email
	 *
	 * @return int number of comments
	 */
	function get_comment_count( $user_email ) {
		static $comment_counts = array();

		// retrieve counts for all commentators
		if ( empty( $comment_counts ) ) {
			global $wpdb;
			$query   = 'SELECT comment_author_email, COUNT(*) AS total FROM ' . $wpdb->comments . ' WHERE comment_approved = 1 GROUP BY comment_author_email';
			$results = $wpdb->get_results( $query );
			foreach ( $results as $result ) {
				$comment_counts[ $result->comment_author_email ] = $result->total;
			}
		}

		if ( array_key_exists( $user_email, $comment_counts ) ) {
			return $comment_counts[ $user_email ];
		}

		return 0;
	}

	/**
	 * Given two users, this function compares the date on which the user registered.
	 *
	 * @access private
	 *
	 * @param WP_User $a
	 * @param WP_User $b
	 *
	 * @return int result of a string compare of the user's register date.
	 */
	function _user_cmp_regdate( $a, $b ) {
		return $this->_sort_direction() * strcasecmp( $a->user_registered, $b->user_registered );
	}

	/**
	 * Given two users, this function compares the time of last user activity sitewide.
	 *
	 * @access private
	 *
	 * @param WP_User $a
	 * @param WP_User $b
	 *
	 * @return int result of a string compare of the user's recent activity.
	 */
	function _user_cmp_site_activity( $a, $b ) {
		$a_activity = $this->get_user_last_site_activity( $a->user_id );
		$b_activity = $this->get_user_last_site_activity( $b->user_id );

		return $this->_sort_direction() * strcasecmp( $a_activity, $b_activity );
	}

	/**
	 * Returns the time of last activity for a given user all post and pages.
	 *
	 * Returns the date of
	 * the latest post or page published by the given user.
	 *
	 * @param int $user_id
	 *
	 * @return string last activity date
	 *
	 * look at using bbp_get_user_last_posted to get the buddypress value
	 */
	function get_user_last_site_activity( $user_id ) {

		global $wpdb;
		$query = $wpdb->prepare(
			"SELECT p.post_date
			FROM $wpdb->posts p
			WHERE
				p.post_status = 'publish'
				AND
				p.post_author = %d
			ORDER BY p.post_date
			DESC LIMIT 1",
			$user_id
		);

		return $wpdb->get_var( $query );
	}

	/**
	 * Given two users, this function compares the time of last user activity for posts.
	 *
	 * @access private
	 *
	 * @param WP_User $a
	 * @param WP_User $b
	 *
	 * @return int result of a string compare of the user's recent activity.
	 */
	function _user_cmp_post_activity( $a, $b ) {
		$a_activity = $this->get_user_last_post_activity( $a->user_id );
		$b_activity = $this->get_user_last_post_activity( $b->user_id );

		return $this->_sort_direction() * strcasecmp( $a_activity, $b_activity );
	}

	/**
	 * Returns the time of last activity for a given user.
	 *
	 * Returns the date of
	 * the latest post published by the given user.
	 *
	 * @param int $user_id
	 *
	 * @return string last activity date
	 *
	 * look at using bbp_get_user_last_posted to get the buddypress value
	 */
	function get_user_last_post_activity( $user_id ) {

		global $wpdb;
		$query = $wpdb->prepare(
			"SELECT p.post_date
			FROM $wpdb->posts p
			WHERE
				p.post_status = 'publish'
				AND
				p.post_type = 'post'
				AND
				p.post_author = %d
			ORDER BY p.post_date
			DESC LIMIT 1",
			$user_id
		);

		return $wpdb->get_var( $query );
	}

	/**
	 * Given two users, this function compares the time of last user activity in buddypress.
	 *
	 * @access private
	 *
	 * @param WP_User $a
	 * @param WP_User $b
	 *
	 * @return int result of a string compare of the user's recent activity.
	 */
	function _user_cmp_budypress_activity( $a, $b ) {
		$a_activity = $this->get_user_last_buddypress_activity( $a->user_id );
		$b_activity = $this->get_user_last_buddypress_activity( $b->user_id );

		return $this->_sort_direction() * strcasecmp( $a_activity, $b_activity );
	}

	/**
	 * Returns the time of last activity for a given user.
	 *
	 * For BuddyPress this function uses the `last_activity` meta
	 * data value maintained by BuddyPress. return a very old date in none found
	 *
	 * @param int $user_id
	 *
	 * @return string last activity date
	 *
	 * look at using bbp_get_user_last_posted to get the buddypress value
	 */
	function get_user_last_buddypress_activity( $user_id ) {
		return gmdate( 'Y-m-d H:i:s', (int) get_user_meta( $user_id, 'last_activity' ) );
	}

	/**
	 * Get blogs of user
	 *
	 * @param int $user_id
	 *
	 * @return Array of blog ids
	 */
	function get_user_blogs( $user_id ) {
		global $wpdb;

		$user = get_userdata( (int) $user_id );
		if ( ! $user ) {
			return false;
		}

		$blogs = $match = array();
		foreach ( (array) $user as $key => $value ) {
			if ( false !== strpos( $key, '_capabilities' ) &&
			     0 === strpos( $key, $wpdb->base_prefix ) &&
			     preg_match( '/' . $wpdb->base_prefix . '(\d+)_capabilities/', $key, $match )
			) {
				$blogs[] = $match[1];
			}
		}

		return $blogs;
	}

	/**
	 * Group the given set of users if set in field "group_by"
	 *
	 * @param Array of WP_User objects, by reference
	 *
	 * @access private
	 * @return void
	 */
	function _group( &$users ) {
		if ( empty( $this->group_by ) ) {
			return;
		}

		switch ( $this->group_by ) {
			case 'blog':
				if ( AA_is_wpmu() ) {
					$users_new = array();

					global $wpdb;
					$pattern = '/' . $wpdb->base_prefix . '([0-9]+)_capabilities/';

					foreach ( $users as $user ) {
						$key     = $user->meta_key;
						$matches = array();

						// blog id 1
						if ( $key == $wpdb->base_prefix . 'capabilities' ) {
							$users_new[1][] = $user;
						} // other blogs
						else if ( preg_match( $pattern, $key, $matches ) > 0 ) {
							$users_new[ $matches[1] ][] = $user;
						}
					}

					if ( ! empty( $users_new ) ) {
						$users = $users_new;
					}
				}

				break;
		}
	}

	/**
	 * Retrieves the name for a group
	 *
	 * @param int Group identifier
	 *
	 * @access private
	 * @return string
	 */
	function _group_name( $id ) {
		$name = 'Group #' . $id;
		if ( ! empty( $this->group_by ) ) {
			switch ( $this->group_by ) {
				case 'blog':
					$name = get_blog_option( $id, 'blogname' );
					break;
			}
		}

		return $name;
	}


	/**
	 * truncateHtml can truncate a string up to a number of characters while preserving whole words and HTML tags
	 *
	 * @param string $text String to truncate.
	 * @param integer $length Length of returned string, including ellipsis.
	 * @param string $ending Ending to be appended to the trimmed string.
	 * @param boolean $exact If false, $text will not be cut mid-word
	 * @param boolean $considerHtml If true, HTML tags would be handled correctly
	 *
	 * @return string Trimmed string.
	 */
	function truncate_html($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true) {
		if ($considerHtml) {
			// if the plain text is shorter than the maximum length, return the whole text
			if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}
			// splits all html-tags to scanable lines
			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
			$total_length = strlen($ending);
			$open_tags = array();
			$truncate = '';
			foreach ($lines as $line_matchings) {
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (!empty($line_matchings[1])) {
					// if it's an "empty element" with or without xhtml-conform closing slash
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
						// do nothing
						// if tag is a closing tag
					} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
						// delete tag from $open_tags list
						$pos = array_search($tag_matchings[1], $open_tags);
						if ($pos !== false) {
							unset($open_tags[$pos]);
						}
						// if tag is an opening tag
					} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
						// add tag to the beginning of $open_tags list
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					// add html-tag to $truncate'd text
					$truncate .= $line_matchings[1];
				}
				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				if ($total_length+$content_length> $length) {
					// the number of characters which are left
					$left = $length - $total_length;
					$entities_length = 0;
					// search for html entities
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
						// calculate the real length of all entities in the legal range
						foreach ($entities[0] as $entity) {
							if ($entity[1]+1-$entities_length <= $left) {
								$left--;
								$entities_length += strlen($entity[0]);
							} else {
								// no more characters left
								break;
							}
						}
					}
					$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
					// maximum lenght is reached, so get off the loop
					break;
				} else {
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}
				// if the maximum length is reached, get off the loop
				if($total_length>= $length) {
					break;
				}
			}
		} else {
			if (strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = substr($text, 0, $length - strlen($ending));
			}
		}
		// if the words shouldn't be cut in the middle...
		if (!$exact) {
			// ...search the last occurance of a space...
			$spacepos = strrpos($truncate, ' ');

			if ( isset($spacepos ) ) {
				$br_pos = strrpos( substr( $truncate, $spacepos-4, $spacepos ), '<' );
				if( isset( $br_pos ) ){
					$truncate = substr( $truncate, 0, $spacepos - ( 4 - ( $br_pos ) ) );
				}else{
					// ...and cut the text in this position
					$truncate = substr($truncate, 0, $spacepos);
				}
			}
		}
		// add the defined ending to the text

		$truncate .= $ending;
		if($considerHtml) {
			// close all unclosed html-tags
			foreach ($open_tags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}
		return $truncate;
	}
}
