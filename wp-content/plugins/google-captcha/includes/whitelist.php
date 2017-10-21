<?php
/**
 * Display content of "Whitelist" tab on settings page
 * @subpackage Google Captcha
 * @since 1.27
 * @version 1.0.0
 */

if ( ! class_exists( 'Gglcptch_Whitelist' ) ) {
	if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}

	class Gglcptch_Whitelist extends WP_List_Table {
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
			if ( empty( $gglcptch_options ) ) {
				$gglcptch_options = get_option( 'gglcptch_options' );
			}
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
			<h1 class="wp-heading-inline"><?php _e( 'Google Captcha Whitelist', 'google-captcha' ); ?></h1>
			<?php if ( ! ( isset( $_REQUEST['gglcptch_show_whitelist_form'] ) || isset( $_REQUEST['gglcptch_add_to_whitelist'] ) ) ) { ?>
				<form method="post" action="admin.php?page=google-captcha-whitelist.php" style="display: inline;">
					<button class="page-title-action" name="gglcptch_show_whitelist_form" value="on"<?php echo ( isset( $_POST['gglcptch_add_to_whitelist'] ) ) ? ' style="display: none;"' : ''; ?>><?php _e( 'Add New', 'google-captcha' ); ?></button>
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
				<label><?php _e( 'IP to whitelist', 'google-captcha' ); ?></label>
				<br />
				<input type="text" maxlength="31" name="gglcptch_add_to_whitelist" />
				<?php if ( isset( $my_ip ) ) { ?>
					<br />
					<label id="gglcptch_add_my_ip">
						<input type="checkbox" name="gglcptch_add_to_whitelist_my_ip" value="1" />
						<?php _e( 'My IP', 'google-captcha' ); ?>
						<input type="hidden" name="gglcptch_add_to_whitelist_my_ip_value" value="<?php echo $my_ip; ?>" />
					</label>
				<?php } ?>
				<div>
					<span class="bws_info" style="line-height: 2;"><?php _e( "Allowed formats", 'google-captcha' ); ?>:&nbsp;<code>192.168.0.1</code></span>
					<br/>
					<span class="bws_info" style="line-height: 2;"><?php _e( "Allowed diapason", 'google-captcha' ); ?>:&nbsp;<code>0.0.0.0 - 255.255.255.255</code></span>
				</div>
				<?php gglcptch_pro_block( 'gglcptch_whitelist_banner' ); ?>
				<p>
					<input type="submit" class="button-secondary" value="<?php _e( 'Add IP to whitelist', 'google-captcha' ) ?>" />
					<?php wp_nonce_field( $this->basename, 'gglcptch_nonce_name' ); ?>
				</p>
			</form>
			<form id="gglcptch_whitelist_search" method="post" action="admin.php?page=google-captcha-whitelist.php">
				<?php $this->search_box( __( 'Search IP', 'google-captcha' ), 'search_whitelisted_ip' );
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
			$label = isset( $_REQUEST['s'] ) ? __( 'Nothing found', 'google-captcha' ) : __( 'No IP in the whitelist', 'google-captcha' ); ?>
			<p><?php echo $label; ?></p>
		<?php }

		function get_columns() {
			$columns = array(
				'cb'		=> '<input type="checkbox" />',
				'ip'		=> __( 'IP Address', 'google-captcha' ),
				'add_time'	=> __( 'Date Added', 'google-captcha' )
			);
			return $columns;
		}
		/**
		 * Get a list of sortable columns.
		 * @return array list of sortable columns
		 */
		function get_sortable_columns() {
			$sortable_columns = array(
				'ip'      => array( 'ip', true ),
				'add_time' => array( 'add_time', false )
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
					return $item[ $column_name ];
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
				'<input type="checkbox" name="id[]" value="%s"/>', $item['id']
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
				'delete' => '<a href="' . wp_nonce_url( $url, "gglcptch_nonce_remove_{$item['id']}" ) . '">' . __( 'Delete', 'google-captcha' ) . '</a>'
			);
			return sprintf('%1$s %2$s', $item['ip'], $this->row_actions( $actions ) );
		}
		/**
		 * List with bulk action for IP
		 * @return array   $actions
		 */
		function get_bulk_actions() {
			/* adding bulk action */
			return array( 'gglcptch_remove'=> __( 'Delete', 'google-captcha' ) );
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
						" WHERE `ip` LIKE '%{$this->s}%'"
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
						" WHERE `ip` LIKE '%{$this->s}%'"
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
			if ( ! $bulk_action ) {
				$bulk_action = isset( $_REQUEST['action2'] ) && 'gglcptch_remove' == $_REQUEST['action2'] ? true : false;
			}

			/* Add IP to the database */
			if (
				isset( $_POST['gglcptch_add_to_whitelist'] ) &&
				( ! empty( $_POST['gglcptch_add_to_whitelist'] ) || isset( $_POST['gglcptch_add_to_whitelist_my_ip'] ) ) &&
				check_admin_referer( $this->basename, 'gglcptch_nonce_name' )
			) {
				$add_ip = isset( $_POST['gglcptch_add_to_whitelist_my_ip'] ) ? $_POST['gglcptch_add_to_whitelist_my_ip_value'] : $_POST['gglcptch_add_to_whitelist'];

				$valid_ip = filter_var( stripslashes( esc_html( trim( $add_ip ) ) ), FILTER_VALIDATE_IP );
				if ( $valid_ip ) {
					$ip_int = sprintf( '%u', ip2long( $valid_ip ) );
					$id = $wpdb->get_var( "SELECT `id` FROM " . $wpdb->prefix . "gglcptch_whitelist WHERE ( `ip_from_int` <= " . $ip_int . " AND `ip_to_int` >= " . $ip_int . " ) OR `ip` LIKE '" . $valid_ip . "' LIMIT 1;" );
					/* check if IP already in database */
					if ( is_null( $id ) ) {
						$time = current_time( 'mysql' );
						$wpdb->insert(
							$wpdb->prefix . "gglcptch_whitelist",
							array(
								'ip'          => $valid_ip,
								'ip_from_int' => $ip_int,
								'ip_to_int'   => $ip_int,
								'add_time'    => $time
							)
						);
						if ( ! $wpdb->last_error ) {
							$message = __( 'IP added to the whitelist successfully.', 'google-captcha' );
						} else {
							$error = __( 'Some errors occurred.', 'google-captcha' );
						}
					} else {
						$error = __( 'IP is already in the whitelist.', 'google-captcha' );
					}
				} else {
					$error = __( 'Invalid IP. See allowed formats.', 'google-captcha' );
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
						$message = sprintf( _n( "%s IP was deleted successfully.", "%s IPs were deleted successfully.", $result, 'google-captcha' ), $result );
						$gglcptch_options['whitelist_is_empty'] = is_null( $wpdb->get_var( "SELECT `id` FROM `{$wpdb->prefix}gglcptch_whitelist` LIMIT 1" ) ) ? true : false;
						update_option( 'gglcptch_options', $gglcptch_options );
					} else {
						$error = __( 'Some errors occurred.', 'google-captcha' );
					}
				}
			} elseif ( isset( $_GET['gglcptch_remove'] ) && check_admin_referer( 'gglcptch_nonce_remove_' . $_GET['gglcptch_remove'] ) ) {
				$wpdb->delete( $wpdb->prefix . "gglcptch_whitelist", array( 'id' => $_GET['gglcptch_remove'] ) );
				if ( ! $wpdb->last_error ) {
					$message = __( "One IP was deleted successfully.", 'google-captcha' );
					$gglcptch_options['whitelist_is_empty'] = is_null( $wpdb->get_var( "SELECT `id` FROM `{$wpdb->prefix}gglcptch_whitelist` LIMIT 1" ) ) ? true : false;
					update_option( 'gglcptch_options', $gglcptch_options );
				} else {
					$error = __( 'Some errors occurred.', 'google-captcha' );
				}
			} elseif ( isset( $_POST['gglcptch_add_to_whitelist'] ) && empty( $_POST['gglcptch_add_to_whitelist'] ) && ! isset( $_POST['bws_hide_premium_options'] ) ) {
				$error = __( 'You have not entered any IP.', 'google-captcha' );
			} elseif ( isset( $_REQUEST['s'] ) ) {
				if ( '' == $_REQUEST['s'] ) {
					$error = __( 'You have not entered any IP in to the search form.', 'google-captcha' );
				} else {
					$message = __( 'Search results for', 'google-captcha' ) . '&nbsp;:&nbsp;' . esc_html( $_REQUEST['s'] );
				}
			}
			if ( ! empty( $message ) ) { ?>
				<div class="updated fade below-h2"><p><strong><?php echo $message; ?></strong></p></div>
			<?php }
			if ( ! empty( $error ) ) { ?>
				<div class="error below-h2"><p><strong><?php echo $error; ?></strong></p></div>
			<?php }
		}
	}
}