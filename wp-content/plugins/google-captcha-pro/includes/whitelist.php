<?php
/**
 * Display content of "Whitelist" tab on settings page
 * @subpackage Google Captcha PRO
 * @since 1.27
 * @version 1.0.0
 */

if ( ! class_exists( 'Gglcptch_Pro_Whitelist' ) ) {
	if ( ! class_exists( 'WP_List_Table' ) )
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

	class Gglcptch_Pro_Whitelist extends WP_List_Table {
		private
			$basename,
			$order_by,
			$per_page,
			$paged,
			$order,
			$s;

		/**
		* Constructor of class
		*/
		function __construct( $plugin_basename ) {
			global $gglcptch_options;
			if ( empty( $gglcptch_options ) )
				$gglcptch_options = get_option( 'gglcptch_options' );
			parent::__construct( array(
				'singular'	=> 'IP',
				'plural'	=> 'IP',
				'ajax'		=> true,
				)
			);
			$this->basename	= $plugin_basename;
		}

		/**
		 * Display content
		 * @return void
		 */
		function display_content() {
			global $wp_version, $gglcptch_options; ?>
			<h1 class="wp-heading-inline"><?php _e( 'Google Captcha Whitelist', 'google-captcha-pro' ); ?></h1>
			<?php if ( ! ( isset( $_REQUEST['gglcptch_show_whitelist_form'] ) || isset( $_REQUEST['gglcptch_add_to_whitelist'] ) ) ) { ?>
				<form method="post" action="admin.php?page=google-captcha-whitelist.php" style="display: inline;">
					<button class="page-title-action" name="gglcptch_show_whitelist_form" value="on"<?php echo ( isset( $_POST['gglcptch_add_to_whitelist'] ) ) ? ' style="display: none;"' : ''; ?>><?php _e( 'Add New', 'google-captcha-pro' ); ?></button>
				</form>
			<?php }
			if ( isset( $_SERVER ) ) {
				$sever_vars = array( 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );
				foreach ( $sever_vars as $var ) {
					if ( isset( $_SERVER[ $var ] ) && ! empty( $_SERVER[ $var ] ) ) {
						if ( filter_var( $_SERVER[ $var ], FILTER_VALIDATE_IP ) ) {
							$my_ip = $_SERVER[ $var ];
							break;
						} else { /* if proxy */
							$ip_array = explode( ',', $_SERVER[ $var ] );
							if ( is_array( $ip_array ) && ! empty( $ip_array ) && filter_var( $ip_array[0], FILTER_VALIDATE_IP ) ) {
								$my_ip = $ip_array[0];
								break;
							}
						}
					}
				}
			}

			$this->display_notices();
			$this->prepare_items(); ?>
			<form class="form-table gglcptch_whitelist_form" method="post" action="admin.php?page=google-captcha-whitelist.php" <?php if ( ! ( isset( $_REQUEST['gglcptch_show_whitelist_form'] ) || isset( $_REQUEST['gglcptch_add_to_whitelist'] ) ) ) echo ' style="display: none;"'; ?>">
				<label><?php _e( 'IP to whitelist', 'google-captcha-pro' ); ?></label>
				<br />
				<textarea rows="2" cols="32" name="gglcptch_add_to_whitelist"></textarea>
				<?php if ( isset( $my_ip ) ) { ?>
					<br />
					<label id="gglcptch_add_my_ip">
						<input type="checkbox" name="gglcptch_add_to_whitelist_my_ip" value="1" />
						<?php _e( 'My IP', 'google-captcha-pro' ); ?>
						<input type="hidden" name="gglcptch_add_to_whitelist_my_ip_value" value="<?php echo $my_ip; ?>" />
					</label>
				<?php } ?>
				<div>
					<span class="bws_info" style="line-height: 2;"><?php _e( "Allowed formats", 'google-captcha-pro' ); ?>:&nbsp;<code>192.168.0.1, 192.168.0., 192.168., 192., 192.168.0.1/8, 123.126.12.243-185.239.34.54</code></span>
					<br/>
					<span class="bws_info" style="line-height: 2;"><?php _e( "Allowed diapason", 'google-captcha-pro' ); ?>:&nbsp;<code>0.0.0.0 - 255.255.255.255</code></span>
					<br />
					<span class="bws_info" style="line-height: 2;"><?php _e( "Allowed separators for IPs: a comma", 'google-captcha-pro' ); ?> (<code>,</code>), <?php _e( 'semicolon', 'google-captcha-pro' ); ?> (<code>;</code>), <?php _e( 'ordinary space, tab, new line or carriage return.', 'google-captcha-pro' ); ?></span>
				</div>
				<label><?php _e( 'Reason', 'google-captcha-pro' ) ?></label>
				<br />
				<textarea rows="2" cols="32" name="gglcptch_add_to_whitelist_reason"></textarea>
				<div>
					<span class="bws_info" style="line-height: 2;"><?php _e( "Allowed separators for reasons: a comma", 'google-captcha-pro' ); ?> (<code>,</code>), <?php _e( 'semicolon', 'google-captcha-pro' ); ?> (<code>;</code>), <?php _e( 'tab, new line or carriage return.', 'google-captcha-pro' ); ?></span>
				</div>
				<p>
					<input type="submit" class="button-secondary" value="<?php _e( 'Add IP to whitelist', 'google-captcha-pro' ); ?>" />
					<?php wp_nonce_field( $this->basename, 'gglcptch_nonce_name' ); ?>
				</p>
			</form>
			<form id="gglcptch_whitelist_search" method="post" action="admin.php?page=google-captcha-whitelist.php">
				<?php $this->search_box( __( 'Search IP', 'google-captcha-pro' ), 'search_whitelisted_ip' );
				wp_nonce_field( $this->basename, 'gglcptch_nonce_name' ); ?>
			</form>
			<form id="gglcptch_whitelist" method="post" action="admin.php?page=google-captcha-whitelist.php">
				<?php $this->display();
				wp_nonce_field( $this->basename, 'gglcptch_nonce_name' ); ?>
			</form>
		<?php }

		/**
		* Function to prepare data before display
		* @return void
		*/
		function prepare_items() {
			if ( isset( $_GET['orderby'] ) && in_array( $_GET['orderby'], array_keys( $this->get_sortable_columns() ) ) ) {
				switch ( $_GET['orderby'] ) {
					case 'ip':
						$this->order_by = 'ip_from_int';
						break;
					case 'ip_from':
						$this->order_by = 'ip_from_int';
						break;
					case 'ip_to':
						$this->order_by = 'ip_to_int';
						break;
					default:
						$this->order_by = esc_sql( $_GET['orderby'] );
						break;
				}
			} else {
				$this->order_by = 'add_time';
			}
			$this->order       = isset( $_REQUEST['order'] ) && in_array( strtoupper( $_REQUEST['order'] ), array( 'ASC', 'DESC' ) ) ? $_REQUEST['order'] : '';
			$this->paged       = isset( $_REQUEST['paged'] ) && is_numeric( $_REQUEST['paged'] ) ? $_REQUEST['paged'] : '';
			$this->s           = isset( $_REQUEST['s'] ) ? esc_html( trim( $_REQUEST['s'] ) ) : '';
			$this->per_page    = $this->get_items_per_page( 'gglcptch_per_page', 20 );

			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$primary               = 'ip';
			$this->_column_headers = array( $columns, $hidden, $sortable, $primary );
			$this->items           = $this->get_content();
			$this->set_pagination_args( array(
					'total_items' => $this->get_items_number(),
					'per_page'    => 20,
				)
			);
		}
		/**
		* Function to show message if empty list
		* @return void
		*/
		function no_items() {
			$label = isset( $_REQUEST['s'] ) ? __( 'Nothing found', 'google-captcha-pro' ) : __( 'No IP in the whitelist', 'google-captcha-pro' ); ?>
			<p><?php echo $label; ?></p>
		<?php }

		function get_columns() {
			$columns = array(
				'cb'         => '<input type="checkbox" />',
				'ip'         => __( 'IP Address', 'google-captcha-pro' ),
				'ip_from_to' => __( 'Range from / to', 'google-captcha-pro' ),
				'add_time'   => __( 'Date Added', 'google-captcha-pro' ),
				'add_reason' => __( 'Reason', 'google-captcha-pro' )
			);
			return $columns;
		}
		/**
		 * Get a list of sortable columns.
		 * @return array list of sortable columns
		 */
		function get_sortable_columns() {
			$sortable_columns = array(
				'ip'      		=> array( 'ip', true ),
				'ip_from_to'	=> array( 'ip_from_to', false ),
				'add_time' 		=> array( 'add_time', false )
			);
			return $sortable_columns;
		}
		/**
		 * Fires when the default column output is displayed for a single row.
		 * @param      string    $column_name      The custom column's name.
		 * @param      array     $item             The cuurrent letter data.
		 * @return    void
		 */
		function column_default( $item, $column_name ) {
			switch ( $column_name ) {
				case 'ip':
				case 'add_time':
				case 'add_reason':
					return $item[ $column_name ];
				case 'ip_from_to':
					return ( $item['ip_to'] == $item['ip_from'] && $item['ip_from'] == $item['ip'] ) ? '' : $item['ip_from'] . ' - ' . $item['ip_to'];
				default:
					/* Show whole array for bugfix */
					return print_r( $item, true );
			}
		}
		/**
		 * Function to manage content of column with checboxes
		 * @param     array     $item        The cuurrent letter data.
		 * @return    string                  with html-structure of <input type=['checkbox']>
		 */
		function column_cb( $item ) {
			/* customize displaying cb collumn */
			return sprintf(
				'<input type="checkbox" name="id[]" value="%s"/>', $item['ip']
			);
		}
		/**
		 * Function to manage content of column with IP-adresses
		 * @param     array     $item        The cuurrent letter data.
		 * @return    string                  with html-structure of <input type=['checkbox']>
		 */
		function column_ip( $item ) {
			$order_by = empty( $this->order_by ) ? '' : "&orderby={$this->order_by}";
			$order    = empty( $this->order )    ? '' : "&order={$this->order}";
			$paged    = empty( $this->paged )    ? '' : "&paged={$this->paged}";
			$s        = empty( $this->s )        ? '' : "&s={$this->s}";
			$url      = "?page=google-captcha-whitelist.php&gglcptch_remove={$item['id']}{$order_by}{$order}{$paged}{$s}";
			$actions = array(
				'delete' => '<a href="' . wp_nonce_url( $url, "gglcptch_nonce_remove_{$item['id']}" ) . '">' . __( 'Delete', 'google-captcha-pro' ) . '</a>'
			);
			return sprintf( '%1$s %2$s', $item['ip'], $this->row_actions( $actions ) );
		}

		function column_add_reason( $item ) {
			/* customize displaying add_reason collumn */
			$edit_link_class = $textarea_class = $button_class = $display_class = '';
			$edit_link_style = $textarea_style = '';

			if ( ! empty( $_REQUEST['gglcptch_edit_add_reason'] ) && $item['ip'] == $_REQUEST['gglcptch_edit_add_reason'] ) {
				$display_class = 'hidden';
				$edit_link_style = 'display:none';
			} else {
				$textarea_class = $button_class = 'hidden';
				$textarea_style = 'display:none';
			}

			$nonce_field	= wp_nonce_field( 'gglcptch_edit_' . $item['ip'], 'gglcptch_edit_whitelist_reason-'. $item['ip'], false, false );

			$display = sprintf(
				'<span class="gglcptch-add-reason %s">%s</span>',
				$display_class,
				nl2br( $item['add_reason'] )
			);

			$textarea = sprintf(
				'<textarea name="gglcptch-reason-%s" class="gglcptch-add-reason-textarea gglcptch-autoexpand %s" rows="3" data-min-rows="3" style="%s">%s</textarea>',
				$item['ip'],
				$textarea_class,
				$textarea_style,
				$item['add_reason']
			);

			$button_cancel = sprintf(
				'<button type="submit" class="button button-small gglcptch-add-reason-button %s" name="gglcptch_reason_cancel" value="">%s</button>',
				$button_class,
				__( 'Cancel', 'google-captcha-pro' )
			);

			$button_submit = sprintf(
				'<button type="submit" class="button button-small button-primary gglcptch-add-reason-button %s" name="gglcptch_reason_submit" value="%s">%s</button>',
				$button_class, $item['ip'],
				__( 'Update', 'google-captcha-pro' )
			);

			$edit_link_href = sprintf(
				'?page=%s&gglcptch_edit_add_reason=%s',
				$_REQUEST['page'],
				$item['ip']
			);

			$edit_link = array(
				'edit_add_reason'	=> sprintf(
					'<a href="%s" class="gglcptch_edit_reason_link %s" style="%s">%s</a>',
					$edit_link_href,
					$edit_link_class,
					$edit_link_style,
					__( 'Edit reason', 'google-captcha-pro' )
				)
			);
			return $nonce_field . $display . $textarea . $button_submit . $button_cancel . $this->row_actions( $edit_link );
		}
		/**
		 * List with bulk action for IP
		 * @return array   $actions
		 */
		function get_bulk_actions() {
			/* adding bulk action */
			return array( 'gglcptch_remove'=> __( 'Delete', 'google-captcha-pro' ) );
		}
		/**
		 * Get content for table
		 * @return  array
		 */
		function get_content() {
			global $wpdb;

			if ( empty( $this->s ) ) {
				$where = '';
			} else {
				$ip_int = filter_var( $this->s, FILTER_VALIDATE_IP ) ? sprintf( '%u', ip2long( $this->s ) ) : 0;
				$where =
						0 == $ip_int
					?
						" WHERE `ip` LIKE '%{$this->s}%' OR `ip_to` LIKE '%{$this->s}%' OR `ip_from` LIKE '%{$this->s}%'"
					:
						" WHERE ( `ip_from_int` <= {$ip_int} AND `ip_to_int` >= {$ip_int} )";
			}
			$order_by = empty( $this->order_by ) ? '' : " ORDER BY `{$this->order_by}`";
			$order    = empty( $this->order )    ? '' : strtoupper( " {$this->order}" );
			$offset   = empty( $this->paged )    ? '' : " OFFSET " . ( $this->per_page * ( absint( $this->paged ) - 1 ) );

			return $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}gglcptch_whitelist`{$where}{$order_by}{$order} LIMIT {$this->per_page}{$offset}", ARRAY_A );
		}

		/**
		 * Get number of all IPs which were added to database
		 * @since  1.6.9
		 * @param  void
		 * @return int    the number of IPs
		 */
		private function get_items_number() {
			global $wpdb;
			if ( empty( $this->s ) ) {
				$where = '';
			} else {
				$ip_int = filter_var( $this->s, FILTER_VALIDATE_IP ) ? sprintf( '%u', ip2long( $this->s ) ) : 0;
				$where =
						0 == $ip_int
					?
						" WHERE `ip` LIKE '%{$this->s}%' OR `ip_to` LIKE '%{$this->s}%' OR `ip_from` LIKE '%{$this->s}%'"
					:
						" WHERE ( `ip_from_int` <= {$ip_int} AND `ip_to_int` >= {$ip_int} )";
			}
			return absint( $wpdb->get_var( "SELECT COUNT(`id`) FROM `{$wpdb->prefix}gglcptch_whitelist`{$where}" ) );
		}

		/**
		 * Handle necessary reqquests and display notices
		 * @return void
		 */
		function display_notices() {
			global $wpdb, $gglcptch_options;
			$error = $message = '';
			$bulk_action = isset( $_REQUEST['action'] ) && 'gglcptch_remove' == $_REQUEST['action'] ? true : false;
			if ( ! $bulk_action )
				$bulk_action = isset( $_REQUEST['action2'] ) && 'gglcptch_remove' == $_REQUEST['action2'] ? true : false;
			/* Add IP in to database */
			if ( isset( $_POST['gglcptch_add_to_whitelist'] ) && ( ! empty( $_POST['gglcptch_add_to_whitelist'] ) || isset( $_POST['gglcptch_add_to_whitelist_my_ip'] ) ) && check_admin_referer( $this->basename, 'gglcptch_nonce_name' ) ) {
				if ( isset( $_POST['gglcptch_add_to_whitelist_my_ip'] ) ) {
					$list_ip[] = $_POST['gglcptch_add_to_whitelist_my_ip_value'];
				} else
					$list_ip = preg_split( "/[\s,;]+/", stripslashes( esc_html( trim( $_POST['gglcptch_add_to_whitelist'], " \s\r\n\t,;" ) ) ) );

				if ( empty( $list_ip ) ) {
					$error = __( 'Invalid data. See allowed formats', 'google-captcha-pro' );
				} else {
					$reasons_list = isset( $_POST['gglcptch_add_to_whitelist_reason'] ) ? stripslashes( esc_html( trim( $_POST['gglcptch_add_to_whitelist_reason'], " \s\r\n\t,;" ) ) ) : '' ;
					$reasons      = preg_split( "/[\r\n\t,;]+/", $reasons_list );
					$time         = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
					$i            = 0;
					$flag         = ( ! empty( $reasons ) ) && count( $reasons ) == count( $list_ip ) ? true : false;
					foreach ( $list_ip as $ip ) {
						$type_ip = $this->valid_ip( $ip );
						if ( $type_ip ) {
							$message_prefix = 'single_ip' == $type_ip ? 'IP' : __( 'Range', 'google-captcha-pro' );
							$ip_int         = sprintf( '%u', ip2long( $ip ) );
							$query_where    = 0 == $ip_int ? " `ip` LIKE '" . $ip . "'" : " ( `ip_from_int` <= " . $ip_int . " AND `ip_to_int` >= " . $ip_int . " ) ";
							$id             = $wpdb->get_var( "SELECT `id` FROM " . $wpdb->prefix . "gglcptch_whitelist WHERE" . $query_where . "LIMIT 1;" );
							/* check if IP already in database */
							if ( is_null( $id ) ) {
								$reason = $flag ? $reasons[ $i ] : $reasons_list;
								if ( $this->save_ip( $ip, $type_ip, $reason, $time ) )
									$message .= $message_prefix . '&nbsp;' . $ip . '&nbsp;' . __( 'added to the whitelist successfully', 'google-captcha-pro' ) . '<br />';
								else
									$error .= $message_prefix . '&nbsp;' . $ip . '&nbsp;' . __( 'not saved to the whitelist.', 'google-captcha-pro' ) . '<br />';
							} else {
								$error .= $message_prefix . '&nbsp;' . $ip . '&nbsp;' . __( 'is already in the whitelist', 'google-captcha-pro' ) . '<br />';
							}
						} else {
							$error .= $ip . '&nbsp;- ' . __( 'invalid format. See allowed formats.', 'google-captcha-pro' ) . '<br />';
						}
						$i ++;
					}
				}
				if ( empty( $error ) ) {
					$gglcptch_options['whitelist_is_empty'] = false;
					update_option( 'gglcptch_options', $gglcptch_options );
				}
			/* Remove IP from database */
			} elseif ( $bulk_action && check_admin_referer( $this->basename, 'gglcptch_nonce_name' ) ) {
				if ( ! empty( $_REQUEST['id'] ) ) {
					$list   = implode( ',', $_REQUEST['id'] );
					$result = $wpdb->query( "DELETE FROM `" . $wpdb->prefix . "gglcptch_whitelist` WHERE `id` IN (" . $list . ");" );
					if ( ! $wpdb->last_error ) {
						$message = sprintf( _n( "%s IP was deleted successfully", "%s IPs were deleted successfully", $result, 'google-captcha-pro' ), $result );
						$gglcptch_options['whitelist_is_empty'] = is_null( $wpdb->get_var( "SELECT `id` FROM `{$wpdb->prefix}gglcptch_whitelist` LIMIT 1" ) ) ? true : false;
						update_option( 'gglcptch_options', $gglcptch_options );
					} else {
						$error = __( 'Some errors occurred', 'google-captcha-pro' );
					}
				}
			} elseif ( isset( $_GET['gglcptch_remove'] ) && check_admin_referer( 'gglcptch_nonce_remove_' . $_GET['gglcptch_remove'] ) ) {
				$wpdb->delete( $wpdb->prefix . "gglcptch_whitelist", array( 'id' => $_GET['gglcptch_remove'] ) );
				if ( ! $wpdb->last_error ) {
					$message = __( "One IP was deleted successfully", 'google-captcha-pro' );
					$gglcptch_options['whitelist_is_empty'] = is_null( $wpdb->get_var( "SELECT `id` FROM `{$wpdb->prefix}gglcptch_whitelist` LIMIT 1" ) ) ? true : false;
					update_option( 'gglcptch_options', $gglcptch_options );
				} else {
					$error = __( 'Some errors occurred', 'google-captcha-pro' );
				}
			} elseif ( isset( $_POST['gglcptch_add_to_whitelist'] ) && empty( $_POST['gglcptch_add_to_whitelist'] ) ) {
				$error = __( 'You have not entered any IP', 'google-captcha-pro' );
			} elseif ( isset( $_REQUEST['s'] ) ) {
				if ( '' == $_REQUEST['s'] ) {
					$error = __( 'You have not entered any IP in to the search form.', 'google-captcha-pro' );
				} else {
					$message = __( 'Search results for', 'google-captcha-pro' ) . '&nbsp;:&nbsp;' . esc_html( $_REQUEST['s'] );
				}
			} elseif ( isset( $_REQUEST['action'] ) && 'whitelist' == $_GET['action'] && ! empty( $_REQUEST['gglcptch_reason_submit'] ) ) {
				$ip = $_REQUEST['gglcptch_reason_submit'];
				$ip_var_name = preg_replace( '/\./', '_', $ip );
				check_admin_referer( 'gglcptch_edit_' . $ip, 'gglcptch_edit_whitelist_reason-' . $ip_var_name );
				$reason = isset( $_POST['gglcptch-reason-' . $ip_var_name ] ) ? $_POST['gglcptch-reason-' . $ip_var_name ] : '';
				switch ( $this->edit_reason( $ip, $reason ) ) {
					case 'success':
						$message = sprintf( __( 'The reason for %s has been updated successfully', 'google-captcha-pro' ), $ip ) . '<br />';
						break;
					case 'no_change':
						$message = __( 'No changes was made for', 'google-captcha-pro' ) . "&nbsp;$ip<br />";
						break;
					case 'error':
						$error = __( 'Error while updating reason for', 'google-captcha-pro' ) . "&nbsp;$ip<br />";
						break;
					default:
						break;
				}
			}
			if ( ! empty( $message ) ) { ?>
				<div class="updated fade below-h2"><p><strong><?php echo $message; ?></strong></p></div>
			<?php }
			if ( ! empty( $error ) ) { ?>
				<div class="error below-h2"><p><strong><?php echo $error; ?></strong></p></div>
			<?php }
		}
		/**
		 * Function to check if IP (mask/diapason) is valid
		 * @param $ip_to_check  string  IP, mask or diapason to check
		 * @return bool False - if it's not valid IP, mask or diapason | string with the type of entered value - if valid IP, mask or diapason
		 */
		function valid_ip( $ip_to_check = null ) {
			if ( empty( $ip_to_check ) ) {
				return false;
			} else {
				/* if IP (or mask/diapason) is not empty*/
				if ( preg_match( '/^(25[0-5]|2[0-4][0-9]|[1][0-9]{2}|[1-9][0-9]|[0-9])(\.(25[0-5]|2[0-4][0-9]|[1][0-9]{2}|[1-9][0-9]|[0-9])){3}$/', $ip_to_check ) ) {
					/* single IP */
					return 'single_ip';
				} elseif ( preg_match( '/^(25[0-5]|2[0-4][0-9]|[1][0-9]{2}|[1-9][0-9]|[0-9])(\.(25[0-5]|2[0-4][0-9]|[1][0-9]{2}|[1-9][0-9]|[0-9])){3}\/(3[0-2]|[1-2][0-9]|[0-9])$/', $ip_to_check ) ) {
					/* normal mask like 128.45.25.0/8 */
					return 'normal_mask';
				} elseif ( preg_match( '/^(25[0-5]|2[0-4][0-9]|[1][0-9]{2}|[1-9][0-9]|[0-9])(\.(25[0-5]|2[0-4][0-9]|[1][0-9]{2}|[1-9][0-9]|[0-9])){0,2}\.$/', $ip_to_check ) ) {
					/* shorten mask like 192.168. or 128.45.25. */
					return 'shorten_mask';
				} elseif ( preg_match( '/^(25[0-5]|2[0-4][0-9]|[1][0-9]{2}|[1-9][0-9]|[0-9])(\.(25[0-5]|2[0-4][0-9]|[1][0-9]{2}|[1-9][0-9]|[0-9])){3}\-(25[0-5]|2[0-4][0-9]|[1][0-9]{2}|[1-9][0-9]|[0-9])(\.(25[0-5]|2[0-4][0-9]|[1][0-9]{2}|[1-9][0-9]|[0-9])){3}$/', $ip_to_check ) ) {
					/* diapason like 128.45.25.0-188.5.5.5 */
					$ip_to_check = explode( '-', $ip_to_check ); /*$ips[0] - diapason from, $ips[1] - diapason to*/
					if ( sprintf( '%u', ip2long( $ip_to_check[0] ) ) <= sprintf( '%u', ip2long( $ip_to_check[1] ) ) ) {
						return 'diapason';
					} else {
						return false;
					}
				} else {
					return false;
				}
			}
		}
		/**
		 * Save IP in database
		 */
		function save_ip( $ip, $type_ip, $reason, $time ) {
			global $wpdb;
			switch ( $type_ip ) {
				case 'single_ip': /* if insert single ip address */
					$ip_from_int = $ip_to_int = sprintf( '%u', ip2long( $ip ) ); /*because adding a single address diapason will contain one address*/
					$ip_from     = $ip_to = $ip;
					break;
				case 'shorten_mask': /* if insert ip mask like 'xxx.' or 'xxx.xxx.' or 'xxx.xxx.xxx.' */
					$dot_entry = substr_count( $ip, '.' );
					switch ( $dot_entry ) {
						case 3: /* in case if mask like xxx.xxx.xxx. */
							$ip_from = $ip . '0';
							$ip_to   = $ip . '255';
							break;
						case 2: /* in case if mask like xxx.xxx. */
							$ip_from = $ip . '0.0';
							$ip_to   = $ip . '255.255';
							break;
						case 1: /*i n case if mask like xxx. */
							$ip_from = $ip . '0.0.0';
							$ip_to   = $ip . '255.255.255';
							break;
						default: /* insurance */
							$ip_from = '0.0.0.0';
							$ip_to   = '0.0.0.0';
							break;
					}
					$ip_from_int = sprintf( '%u', ip2long( $ip_from ) );
					$ip_to_int   = sprintf( '%u', ip2long( $ip_to ) );
					break;
				case 'diapason': /* if insert diapason of ip addresses like xxx.xxx.xxx.xxx-yyy.yyy.yyy.yyy */
					$ips         = explode( '-', $ip ); /* $ips[0] - diapason from, $ips[1] - diapason to */
					$ip_from     = trim( $ips[0] );
					$ip_to       = trim( $ips[1] );
					$ip_from_int = sprintf( '%u', ip2long( $ip_from ) );
					$ip_to_int   = sprintf( '%u', ip2long( $ip_to ) );
					break;
				case 'normal_mask': /* if insert ip mask like xxx.xxx.xxx.xxx/yy */
					$mask        = explode( '/' , $ip ); /* $mask[0] - is ip address, $mask[1] - is cidr mask */
					$nmask       = 4294967295 - ( pow( 2 , 32 - $mask[1] ) - 1 ); /* calculation netmask in decimal view from cidr mask */
					$ip_from_int = ip2long( $mask[0] ) & $nmask; /* calculating network address signed (this is doing for correct worl with netmsk) */
					$ip_from_int = sprintf( '%u', $ip_from_int ); /* and now unsigned */
					$ip_to_int   = $ip_from_int + ( pow( 2 , 32 - $mask[1] ) - 1 ); /* calculating broadcast */
					$ip_from     = long2ip( $ip_from_int );
					$ip_to       = long2ip( $ip_to_int );
				default:
					break;
			}
			/* add a new row to db */
			$result = $wpdb->insert(
				$wpdb->prefix . "gglcptch_whitelist",
				array(
					'ip'          => $ip,
					'ip_from'     => $ip_from,
					'ip_to'       => $ip_to,
					'ip_from_int' => $ip_from_int,
					'ip_to_int'   => $ip_to_int,
					'add_time'    => $time,
					'add_reason'  => $reason,
				),
				'%s' /* all '%s' because max value in '%d' is 2147483647 */
			);
			return $result;
		}

		function edit_reason( $ip, $reason ) {
			global $wpdb;
			$db_reason = stripslashes( esc_html( trim( $reason, " \r\n\t,;" ) ) );
			$n = $wpdb->update(
				$wpdb->prefix . "gglcptch_whitelist",
				array( 'add_reason' => $db_reason ),
				array( 'ip' => $ip )
			);
			/* if number of touched rows != 0/false */
			if ( !! $n ) {
				return 'success';
			}
			if ( $n === 0) {
				return 'no_change';
			}
			if ( $wpdb->last_error ) {
				return 'error';
			}
		}
	}
}