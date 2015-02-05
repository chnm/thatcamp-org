<?php
	class YOP_POLL_Mentenance {

		private function network_propagate( $pfunction, $networkwide ) {
			if ( function_exists ( 'is_multisite' ) && is_multisite () ) {
				if ( $networkwide ) {
					$old_blog   = $GLOBALS['wpdb']->blogid;
					$blogids    = $GLOBALS['wpdb']->get_col ( "SELECT blog_id FROM {$GLOBALS['wpdb']->blogs}" );
					foreach ( $blogids as $blog_id ) {
						switch_to_blog ( $blog_id );
						yop_poll_create_table_names( $GLOBALS['wpdb']->prefix );
						call_user_func ( array ( $this, $pfunction ), $networkwide );
					}
					switch_to_blog ( $old_blog );
					yop_poll_create_table_names( $GLOBALS['wpdb']->prefix );
					return;
				}
			}
			call_user_func ( array ( $this, $pfunction ), $networkwide );
		}

		function propagate_activation ($networkwide ) {
			$this->network_propagate ( 'activate', $networkwide );
		}

		function propagate_deactivation( $networkwide ) {
			$this->network_propagate ( 'deactivate', $networkwide );
		}
		
		private function install_default_options() {

			$default_poll_options	= array(
				'is_default_answer'	=> 'no',
				'poll_start_date'	=> current_time( 'mysql' ),
				'poll_end_date'		=> '01-01-2038 23:59:59',
			);        	
			update_option( 'yop_poll_options', $default_poll_options );
		}
		
		private function uninstall_default_options() {
			delete_option( "yop_poll_version" );
			delete_option( "yop_poll_options" );
		}

		public function activate( $networkwide ) {
			if ( ! current_user_can ( 'activate_plugins' ) ) {
				$error = new WP_Error ( 'Wordpress_version_error', __yop_poll( 'You need permissions to activate this plugin' ), __yop_poll( 'Error: Wordpress Activation Permissions Problem' ) );
			}

			if ( ! version_compare ( $GLOBALS['wp_version'], YOP_POLL_WP_VERSION, '>=' ) ) {
				$error = new WP_Error ( 'Wordpress_version_error', sprintf ( __yop_poll( 'You need at least Wordpress version %s to use this plugin' ), YOP_POLL_WP_VERSION ), __yop_poll( 'Error: Wordpress Version Problem' ) );                
			} 

			if ( isset ( $error ) && is_wp_error ( $error ) ) {
				wp_die ( $error->get_error_message (), $error->get_error_data () );
			}


			require_once ( ABSPATH . 'wp-admin/includes/upgrade.php');
			require_once ( YOP_POLL_INC . 'db_schema.php' );
			$this->install_default_options();
			$capObj	= YOP_POLL_Capabilities::get_instance();
			$capObj->install_capabilities();
			Yop_Poll_DbSchema::install_database ();
		}
		
		public function deactivate( $networkwide ) {
			require_once (YOP_POLL_INC . 'db_schema.php');
			//Yop_Poll_DbSchema::delete_database_tables ();
			$capObj	= YOP_POLL_Capabilities::get_instance();
			$capObj->uninstall_capabilities();
			$this->uninstall_default_options();
		}

		function new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
			if ( ! function_exists ( 'is_plugin_active_for_network' ) )
				require_once ( ABSPATH . '/wp-admin/includes/plugin.php' );
			if ( is_plugin_active_for_network ( YOP_POLL_SHORT_PLUGIN_FILE ) ) {
				$old_blog = $GLOBALS['wpdb']->blogid;
				switch_to_blog ( $blog_id );
				yop_poll_create_table_names( $GLOBALS['wpdb']->prefix );
				$this->activate ( NULL );
				switch_to_blog ( $old_blog );
				yop_poll_create_table_names( $GLOBALS['wpdb']->prefix );
			}
		}
		
		function delete_blog( $blog_id ) {
			$old_blog = $GLOBALS['wpdb']->blogid;
			switch_to_blog ( $blog_id );
			yop_poll_create_table_names( $GLOBALS['wpdb']->prefix );
			$this->deactivate ( NULL );
			require_once (YOP_POLL_INC . 'db_schema.php');
			Yop_Poll_DbSchema::delete_database_tables ();
			$capObj	= YOP_POLL_Capabilities::get_instance();
			$capObj->uninstall_capabilities();
			$this->uninstall_default_options();
			switch_to_blog ( $old_blog );
			yop_poll_create_table_names( $GLOBALS['wpdb']->prefix );
		}
		
		function update() {
		}
		
}