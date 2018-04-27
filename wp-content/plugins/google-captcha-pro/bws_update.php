<?php 
/**
* getting update of non-free plugins
* @version 1.7.9
*/

if ( !function_exists ( 'bestwebsoft_wp_update_plugins' ) ) {
	function bestwebsoft_wp_update_plugins( $transient = false ) {
		global $bstwbsftwppdtplgns_options, $wp_version;

		$bws_transient_name = 'bws_plugins_update';

		if ( false === ( $response = get_transient( $bws_transient_name ) ) ) {	 
			if ( ! function_exists( 'get_plugins' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			$plugins = get_plugins();
			$active  = get_option( 'active_plugins', array() );		

			if ( is_array( $plugins ) && ! empty( $plugins ) && is_array( $active ) ) {

				/* delete non-BWS plugins */
				foreach ( $plugins as $key_plugin => $value_plugin ) {
					if ( "BestWebSoft" != $value_plugin["Author"] ) {
						unset( $plugins[ $key_plugin ] );
						foreach ( $active as $key => $value ) {
							if ( $value == $key_plugin ) {
								unset( $active[ $key ] );
								break;
							}
						}
					}
				}

				/* install the option defaults */
				if ( is_multisite() ) {
					if ( ! get_site_option( 'bstwbsftwppdtplgns_options' ) )
						add_site_option( 'bstwbsftwppdtplgns_options', array() );
					$bstwbsftwppdtplgns_options = get_site_option( 'bstwbsftwppdtplgns_options' );
				} else {
					if ( ! get_option( 'bstwbsftwppdtplgns_options' ) )
						add_option( 'bstwbsftwppdtplgns_options', array() );
					$bstwbsftwppdtplgns_options = get_option( 'bstwbsftwppdtplgns_options' );
				}

				$bstwbsftwppdtplgns_options['wrong_license_key'] = array();

				/* add keys for non-free plugins */
				foreach ( $plugins as $key => $value ) {
					$license_key = '';
					$dir_name = explode( "/", $key );
					if ( preg_match( '/-pro.php/i', $dir_name[1] ) || preg_match( '/_pro.php/i', $dir_name[1] ) ) {
						if ( file_exists( dirname( dirname( __FILE__ ) ) . '/' . $dir_name[0] . '/license_key.txt' ) ) {
							$license_key = trim( file_get_contents( dirname( dirname( __FILE__ ) ) . '/' . $dir_name[0] . '/license_key.txt' ) );
							if ( ! empty( $bstwbsftwppdtplgns_options[ $key ] ) && '' == $license_key ) {
								$plugins[ $key ]["bws_license_key"] = $bstwbsftwppdtplgns_options[ $key ];
							} else {
								$plugins[ $key ]["bws_license_key"] = $bstwbsftwppdtplgns_options[ $key ] = $license_key;
							}
						} elseif ( ! empty( $bstwbsftwppdtplgns_options[ $key ] ) ) {
							$plugins[ $key ]["bws_license_key"] = $bstwbsftwppdtplgns_options[ $key ];
						} else {
							unset( $plugins[ $key ], $active[ $key ] );

							$plugin_name = explode( '/', $key );
							$wp_schedule_event_name = str_replace( '-', '_', $plugin_name[0] ) . '_license_cron';

							if ( !wp_next_scheduled( $wp_schedule_event_name ) )
								wp_schedule_event( time() + 12 * 60 * 60, 'twicedaily', $wp_schedule_event_name );
							
							$bstwbsftwppdtplgns_options[ $key ] = '';
							$bstwbsftwppdtplgns_options['wrong_license_key'][ $key ] = true;
						}	
					} else {
						unset( $plugins[ $key ], $active[ $key ] );					
					}
				}
				
				if ( is_multisite() )
					update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
				else
					update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );

				if ( empty( $plugins ) )
					return $transient;

				$bloginfo_url = get_bloginfo( 'url' );

				$options = array(
					'timeout' 		=> 30,
					'body' 			=> array( 'plugins' => serialize( compact( 'plugins', 'active' ) ) ),
					'user-agent' 	=> 'WordPress/' . $wp_version . '; ' . $bloginfo_url
				);
	
				$raw_response = wp_remote_post( 'http://bestwebsoft.com/wp-content/plugins/paid-products/plugins/update-check/1.0/', $options );

				if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) )
					return $transient;

				$response = maybe_unserialize( wp_remote_retrieve_body( $raw_response ) );

				if ( is_array( $response ) && ! empty( $response ) ) {
					foreach ( $response as $key => $value ) {
						$plugin_name = explode( '/', $key );
						$wp_schedule_event_name = str_replace( '-', '_', $plugin_name[0] ) . '_license_cron';

						if ( ! preg_match( "~^(ht|f)tp(s?)\:\/\/[0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*(:(0-9)*)*(\/?)(.*)?$~", trim( $value->package ) ) ) {
							unset( $response[ $key ] );
							if ( ! function_exists( 'wp_mail' ) )
								require_once( ABSPATH . 'wp-includes/pluggable.php' );

							if ( "wrong_license_key" == $value->package ) {
								if ( ! wp_next_scheduled( $wp_schedule_event_name ) ) {
									wp_schedule_event( time()+12*60*60, 'twicedaily', $wp_schedule_event_name );
									$admin_email = get_option( 'admin_email' );
									$bloginfo_name = get_bloginfo( 'name' );							
									/* debug cron - send mail to BWS */
									$headers = 'From: ' .  $bloginfo_name . " <" . $admin_email . ">\n";
									$message = 'The cron task is added for ' . $plugin_name[0] . '  host: ' . $bloginfo_url;
									@wp_mail( 'plugin_pro@bestwebsoft.com', 'BestWebSoft Product', $message, $headers );
									/* send mail to admin of this site */
									$headers .= 'MIME-Version: 1.0' . "\n";
									$headers .= 'Content-type: text/html; charset=utf-8' . "\n";
									$message = '<html><head><title>BestWebSoft Product from ' . $bloginfo_name . '</title></head>
												<body>
												Dear Customer,<br/><br/>
												This is an automatically generated email, please do not reply.<br/><br/>
												We have noticed illegal use of the <strong>' . ucwords( str_replace( '-', ' ', $plugin_name[0] ) ) . '</strong> plugin on ' . $bloginfo_url . '.<br/>
												You can use one license of ' . ucwords( str_replace( '-', ' ', $plugin_name[0] ) ) . ' plugin for one domain only. Please check and edit your license or domain if necessary using you personal Client Area. We strongly recommend you to solve the problem within 24 hours, otherwise the Pro plugin will be deactivated. <a href="https://support.bestwebsoft.com/hc/en-us/articles/204240089" target="_blank">Learn More</a><br/><br/>
												Thank you for consideration.<br/><br/>
												Sincerely,<br/>
												BestWebSoft Customer Support<br/>
												<a href="https://support.bestwebsoft.com" target="_blank">Support Forum</a> | <a href="https://bestwebsoft.com" target="_blank">Official Website</a>
												</body></html>';
									@wp_mail( $admin_email, '[BestWebSoft Product] - Illegal Use Notification', $message, $headers );
								}								
								/* add key in array for display banner */
								$bstwbsftwppdtplgns_options['wrong_license_key'][ $key ] = true;
							} elseif ( isset( $value->trial ) && "duplicate_domen_for_trial" == $value->package ) {
								if ( ! wp_next_scheduled( $wp_schedule_event_name ) ) {
									wp_schedule_event( time()+12*60*60, 'twicedaily', $wp_schedule_event_name );
									$admin_email = get_option( 'admin_email' );
									$bloginfo_name = get_bloginfo( 'name' );
									/* debug cron - send mail to BWS */
									$headers = 'From: ' .  $bloginfo_name . " <" . $admin_email . ">\n";
									$message = 'Duplicate Trial license. The cron task is added for ' . $plugin_name[0] . '  host: ' . $bloginfo_url;
									@wp_mail( 'plugin_pro@bestwebsoft.com', 'BestWebSoft Product', $message, $headers );
									/* send mail to admin of this site */
									$headers .= 'MIME-Version: 1.0' . "\n";
									$headers .= 'Content-type: text/html; charset=utf-8' . "\n";
									$message = '<html><head><title>BestWebSoft Product from ' . $bloginfo_name . '</title></head>
												<body>
												<h4>Dear Customer,</h4>
												Thank you for choosing our product. This is an automatically generated email, please do not reply.<br/><br/>
												Pro Trial license can be installed only once.
												Since the Pro Trial license for <strong>' . ucwords( str_replace( '-', ' ', $plugin_name[0] ) ) . '</strong> has already been installed on ' . $bloginfo_url . ', in order to continue using the plugin you should buy a Pro license - <a href="https://bestwebsoft.com/products/">https://bestwebsoft.com/products/</a>, otherwise the plugin will be deactivated.<br/><br/>
												Sincerely,<br/>
												BestWebSoft Customer Support<br/>
												<a href="https://support.bestwebsoft.com" target="_blank">Help Center</a> | <a href="https://bestwebsoft.com" target="_blank">Website</a>
												</body></html>';
									@wp_mail( $admin_email, '[BestWebSoft Product] - Trial license', $message, $headers );
								}
								/* add key in array for display banner */
								$bstwbsftwppdtplgns_options['trial'][ $key ] = 'duplicate';
							} elseif ( isset( $value->trial ) && "time_out" == $value->package ) {
								if ( ! wp_next_scheduled( $wp_schedule_event_name ) ) {
									wp_schedule_event( time()+12*60*60, 'twicedaily', $wp_schedule_event_name );
									$admin_email = get_option( 'admin_email' );
									$bloginfo_name = get_bloginfo( 'name' );
									/* debug cron - send mail to BWS */
									$headers = 'From: ' .  $bloginfo_name . " <" . $admin_email . ">\n";
									$message = 'Trial license has expired. The cron task is added for ' . $plugin_name[0] . '  host: ' . $bloginfo_url;
									@wp_mail( 'plugin_pro@bestwebsoft.com', 'BestWebSoft Product', $message, $headers );
									/* send mail to admin of this site */
									$headers .= 'MIME-Version: 1.0' . "\n";
									$headers .= 'Content-type: text/html; charset=utf-8' . "\n";
									$message = '<html><head><title>BestWebSoft Product from ' . $bloginfo_name . '</title></head>
												<body>
												<h4>Dear Customer,</h4>
												Thank you for choosing our product. This is an automatically generated email, please do not reply.<br/><br/>'
												. ucwords( str_replace( '-', ' ', $plugin_name[0] ) ) . ' Pro Trial license for ' . $bloginfo_url . ' has been expired. To continue using the plugin you should buy a Regular license - <a href="https://bestwebsoft.com/products/">https://bestwebsoft.com/products/</a>. Otherwise the plugin will be deactivated.<br/><br/>
												Sincerely,<br/>
												BestWebSoft Customer Support<br/>
												<a href="https://support.bestwebsoft.com" target="_blank">Help Center</a> | <a href="https://bestwebsoft.com" target="_blank">Website</a>
												</body></html>';									
									@wp_mail( $admin_email, '[BestWebSoft Product] - Trial license', $message, $headers );
								}
							} elseif ( isset( $value->nonprofit ) && "time_out" == $value->package ) {
								if ( ! wp_next_scheduled( $wp_schedule_event_name ) ) {
									wp_schedule_event( time()+12*60*60, 'twicedaily', $wp_schedule_event_name );
									$admin_email = get_option( 'admin_email' );
									$bloginfo_name = get_bloginfo( 'name' );
									/* debug cron - send mail to BWS */
									$headers = 'From: ' .  $bloginfo_name . " <" . $admin_email . ">\n";
									$message = 'Nonprofit license has expired. The cron task is added for ' . $plugin_name[0] . '  host: ' . $bloginfo_url;
									@wp_mail( 'plugin_pro@bestwebsoft.com', 'BestWebSoft Product', $message, $headers );
									/* send mail to admin of this site */
									$headers .= 'MIME-Version: 1.0' . "\n";
									$headers .= 'Content-type: text/html; charset=utf-8' . "\n";
									$message = '<html><head><title>BestWebSoft Product from ' . $bloginfo_name . '</title></head>
											<body>
											<h4>Dear Customer,</h4>
											Thank you for choosing our product. This is an automatically generated email, please do not reply.<br/><br/>'
											. ucwords( str_replace( '-', ' ', $plugin_name[0] ) ) . ' Nonprofit Pro license for ' . $bloginfo_url . ' has been expired.<br/><br/>
											Please submit a support request to indicate that you want to continue using premium features. Otherwise the plugin will be deactivated.<br/><br/>
											<a href="https://support.bestwebsoft.com/hc/en-us/requests/new" target="_blank">Submit a Request</a>
											<br/><br/>
											Sincerely,<br/>
											BestWebSoft Customer Support<br/>
											<a href="https://support.bestwebsoft.com" target="_blank">Help Center</a> | <a href="https://bestwebsoft.com" target="_blank">Website</a>
											</body></html>';
									@wp_mail( $admin_email, '[BestWebSoft Product] - Nonprofit license', $message, $headers );
								}
							} else {
								wp_clear_scheduled_hook( $wp_schedule_event_name );
							}
						} else {
							wp_clear_scheduled_hook( $wp_schedule_event_name );
						}
						if ( isset( $value->time_out ) ) {
							$bstwbsftwppdtplgns_options['time_out'][ $key ] = $value->time_out;
						}	
						if ( isset( $value->trial ) ) {
							$bstwbsftwppdtplgns_options['trial'][ $key ] = 1;
						} else {
							unset( $bstwbsftwppdtplgns_options['trial'][ $key ] );
						}
						if ( isset( $value->nonprofit ) ) {
							$bstwbsftwppdtplgns_options['nonprofit'][ $key ] = 1;
						} else {
							unset( $bstwbsftwppdtplgns_options['nonprofit'][ $key ] );
						}
						if ( is_multisite() )
							update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
						else
							update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
					}

					set_transient( $bws_transient_name, $response, 60 * 5 );
				}
			}
		}

		/* it is not our cron function */
		if ( $transient && $response ) {
			if ( ! isset( $transient->response ) )
				$transient->response = array();
			
			$transient->response = array_merge( $transient->response, $response );
		}

		return $transient;
	}	
}

/* 
* The upgrader_process_complete action hook is run when the download process for a plugin install or update finishes. 
 $upgrader_object - (Plugin_Upgrader) Plugin_Upgrader instance. In other contexts, $this, might be a Theme_Upgrader or Core_Upgrade instance.
 $options (array)
       'action'   -(string) Type of action. Default 'update'.
       'type'     -(string) Type of update process. Accepts 'plugin', 'theme', or 'core'.  Default:'plugin'
       'bulk'     - (bool) Whether the update process is a bulk update. Default true.
       'packages' -  (array) Array of plugin, theme, or core packages to update.
*
*/
if ( ! function_exists ( 'bestwebsoft_upgrader_process_complete' ) ) {
	function bestwebsoft_upgrader_process_complete( $upgrader_object, $options ) {
		if ( isset( $options['type'] ) && $options['type'] == 'plugin' ) {
			/* for updating bws transient */
			delete_transient( 'bws_plugins_update' );
		}
   }
}

/**
 * Schedule plugin update checks.
 */
if ( ! function_exists ( 'bestwebsoft_wp_schedule_update_checks' ) ) {
	function bestwebsoft_wp_schedule_update_checks() { 
		if ( ! wp_next_scheduled( 'bestwebsoft_wp_update_plugins' ) && !defined( 'WP_INSTALLING' ) )
			wp_schedule_event( time(), 'twicedaily', 'bestwebsoft_wp_update_plugins');
	}
}

if ( ! function_exists ( 'bestwebsoft_wp_update_plugins_cron' ) ) {
	function bestwebsoft_wp_update_plugins_cron() {
		bestwebsoft_wp_update_plugins();
	}
}

/*============ Function for adding all functionality for updating ====================*/
if ( ! function_exists( 'bestwebsoft_license_cron_task' ) ) {
	function bestwebsoft_license_cron_task( $current_plugin, $free_ver_of_current_plugin = false ) {
		global $bstwbsftwppdtplgns_options;
		
		delete_transient( 'bws_plugins_update' );
		bestwebsoft_wp_update_plugins();

		if ( empty( $bstwbsftwppdtplgns_options ) )
			$bstwbsftwppdtplgns_options = ( is_multisite() ) ? get_site_option( 'bstwbsftwppdtplgns_options' ) : get_option( 'bstwbsftwppdtplgns_options' );

		/* if we don't solve the problem */
		if ( isset( $bstwbsftwppdtplgns_options['wrong_license_key'][ $current_plugin ] )
			||
			( isset( $bstwbsftwppdtplgns_options['nonprofit'][ $current_plugin ] ) &&
			  isset( $bstwbsftwppdtplgns_options['time_out'][ $current_plugin ] ) &&
			  strtotime( $bstwbsftwppdtplgns_options['time_out'][ $current_plugin ] ) < strtotime( date("m/d/Y") ) )
			) {
						
			/* update current plugin */
			$plugin_name = explode( '/', $current_plugin );	
			/* curl-content for wrong license */
			$ch_content = curl_init();
			curl_setopt( $ch_content, CURLOPT_URL, 'http://bestwebsoft.com/wp-content/plugins/paid-products/plugins/license-fail/' . $plugin_name[1] );
			curl_setopt( $ch_content, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch_content, CURLOPT_HEADER, 0 );
			$res_content = curl_exec( $ch_content ); 
			$res_content = trim( $res_content );
			$httpCode_content = curl_getinfo( $ch_content, CURLINFO_HTTP_CODE );
			curl_close( $ch_content );
			if ( isset( $res_content ) && 404 != $httpCode_content ) {
				if ( $free_ver_of_current_plugin )
					bestwebsoft_download_free_plugin( $free_ver_of_current_plugin );

				$file = @fopen( dirname( dirname( __FILE__ ) ) . '/' . $plugin_name[0] . '/bws_update.php' , "w+" );
				$file_plugin = @fopen( dirname( dirname( __FILE__ ) ) . '/' . $current_plugin, "w+" );
				if ( $file && $file_plugin ) {
					/* debug cron */
					$admin_email = get_option( 'admin_email' );
					$bloginfo_name = get_bloginfo( 'name' );
					$bloginfo_url = get_bloginfo( 'url' );
					$headers = 'From: ' .  $bloginfo_name . " <" . $admin_email . ">\n";
					$message = 'The file is updated. The cron task is done ' . $plugin_name[1] . '  host: ' . $bloginfo_url;
					if ( ! function_exists( 'wp_mail' ) )
						require_once( ABSPATH . 'wp-includes/pluggable.php' );

					@wp_mail( 'plugin_pro@bestwebsoft.com', 'BestWebSoft Product', $message, $headers );
					/* debug cron */

					@fwrite( $file, "<?php ?>" );
					@fclose( $file );
					@fwrite( $file_plugin, '<?php ' . $res_content . ' ?>' );
					@fclose( $file_plugin );
					/* delete hook */
					if ( wp_next_scheduled( str_replace( '-', '_', $plugin_name[0] ) . '_license_cron' ) )
						wp_clear_scheduled_hook( str_replace( '-', '_', $plugin_name[0] ) . '_license_cron' );
				}
				@chmod( dirname( dirname( __FILE__ ) ) . '/' . $plugin_name[0] . '/bws_update.php', octdec( 755 ) );
				@chmod( dirname( dirname( __FILE__ ) ) . '/' . $current_plugin, octdec( 755 ) );
			}
		}
	}
}

/**
 * Check trial license 
 */
if ( ! function_exists( 'bestwebsoft_trial_license_cron_task' ) ) {
	function bestwebsoft_trial_license_cron_task( $current_plugin, $free_ver_of_current_plugin = free ) {
		/* Trial license */
		global $bstwbsftwppdtplgns_options, $wpdb;
		/* if we dont't solve the problem */
		if ( isset( $bstwbsftwppdtplgns_options['trial'][ $current_plugin ] ) && (
			( isset( $bstwbsftwppdtplgns_options['time_out'][ $current_plugin ] ) && strtotime( $bstwbsftwppdtplgns_options['time_out'][ $current_plugin ] ) < strtotime( date("m/d/Y") ) )
			|| $bstwbsftwppdtplgns_options['trial'][ $current_plugin ] == 'duplicate' ) ) {
			/* update current plugin */
			$plugin_name = explode( '/', $current_plugin );	
			/* curl-content for wrong license */
			$ch_content = curl_init();
			curl_setopt( $ch_content, CURLOPT_URL, 'http://bestwebsoft.com/wp-content/plugins/paid-products/plugins/trial-expired/' . $plugin_name[1] );
			curl_setopt( $ch_content, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch_content, CURLOPT_HEADER, 0 );
			$res_content = curl_exec( $ch_content ); 
			$res_content = trim( $res_content );
			$httpCode_content = curl_getinfo( $ch_content, CURLINFO_HTTP_CODE );
			curl_close( $ch_content );
			if ( isset( $res_content ) && 404 != $httpCode_content ) {
				if ( $free_ver_of_current_plugin )
					bestwebsoft_download_free_plugin( $free_ver_of_current_plugin );

				$file = @fopen( dirname( dirname( __FILE__ ) ) . '/' . $plugin_name[0] . '/bws_update.php' , "w+" );
				$file_plugin = @fopen( dirname( dirname( __FILE__ ) ) . '/' . $current_plugin, "w+" );
				if ( $file && $file_plugin ) {
					/* debug cron */
					$admin_email = get_option( 'admin_email' );
					$bloginfo_name = get_bloginfo( 'name' );
					$bloginfo_url = get_bloginfo( 'url' );

					$headers = 'From: ' .  $bloginfo_name . " <" . $admin_email . ">\n";
					if ( $bstwbsftwppdtplgns_options['trial'][ $current_plugin ] == 'duplicate' )
						$message = 'PRO Trial license duplicate. The file is updated. The cron task is done ' . $plugin_name[1] . '  host: ' . $bloginfo_url;
					else
						$message = 'PRO Trial license expired. The file is updated. The cron task is done ' . $plugin_name[1] . '  host: ' . $bloginfo_url;
					if ( ! function_exists( 'wp_mail' ) )
						require_once( ABSPATH . 'wp-includes/pluggable.php' );
					@wp_mail( 'plugin_pro@bestwebsoft.com', 'BestWebSoft Product', $message, $headers );
					/* debug cron */
					
					@fwrite( $file, "<?php ?>" );
					@fclose( $file );
					@fwrite( $file_plugin, '<?php ' . $res_content . ' ?>' );
					@fclose( $file_plugin );

					/* delete hook */
					if ( wp_next_scheduled( str_replace( '-', '_', $plugin_name[0] ) . '_license_cron' ) )
						wp_clear_scheduled_hook( str_replace( '-', '_', $plugin_name[0] ) . '_license_cron' );
				}

				@chmod( dirname( dirname( __FILE__ ) ) . '/' . $plugin_name[0] . '/bws_update.php', octdec( 755 ) );
				@chmod( dirname( dirname( __FILE__ ) ) . '/' . $current_plugin, octdec( 755 ) );
			}
		}
	}
}

/* chech if free version of plugin is downloaded and download it */
if ( ! function_exists ( 'bestwebsoft_download_free_plugin' ) ) {
	function bestwebsoft_download_free_plugin( $free_ver_of_current_plugin ) {
		if ( ! function_exists( 'get_plugins' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$all_plugins = get_plugins();
		if ( ! array_key_exists( $free_ver_of_current_plugin, $all_plugins ) ) {
			$plugin_name_free = explode( '/', $free_ver_of_current_plugin );
			$url = 'http://downloads.wordpress.org/plugin/' . $plugin_name_free[0] . '.zip';
			$uploadDir = wp_upload_dir();
			$received_content = file_get_contents( $url );
			if ( $received_content ) {
				if ( is_writable( $uploadDir["path"] ) ) {
					$file_put_contents = $uploadDir["path"] . '/' . $plugin_name_free[0] . '.zip';
					if ( file_put_contents( $file_put_contents, $received_content ) ) {
						if ( class_exists( 'ZipArchive' ) ) {
							$zip = new ZipArchive();
							if ( $zip->open( $file_put_contents ) === TRUE ) {
								$zip->extractTo( WP_PLUGIN_DIR );
								$zip->close();
							}
						} elseif ( class_exists( 'Phar' ) ) {
							$phar = new PharData( $file_put_contents );
							$phar->extractTo( WP_PLUGIN_DIR );
						} else {
							return;
						}
						@unlink( $file_put_contents );
					}
				}	
			}
		}
	}
}

/**
* Retrieve plugin info from the configured API endpoint.
* 
* @uses wp_remote_get()
* 
* @param array $queryArgs Additional query arguments to append to the request. Optional.
* @return plugin_info
*/
if ( ! function_exists( 'bestwebsoft_request_info' ) ) {
	function bestwebsoft_request_info( $native_slug ) {
		$queryArgs = array();
		//Query args to append to the URL. Plugins can add their own by using a filter callback (see addQueryArgFilter()).
		$queryArgs['installed_version'] = '';
		$queryArgs = apply_filters( 'puc_request_info_query_args-' . $native_slug, $queryArgs );
		
		/* options for the wp_remote_get() call. Plugins can filter these, too. */
		$options = array(
			'timeout' => 10,
			'headers' => array( 'Accept' => 'application/json' )
		);
		$options = apply_filters( 'puc_request_info_options-' . $native_slug, $options );
		
		$url = 'http://bestwebsoft.com/wp-content/plugins/paid-products/plugins/info/' . $native_slug . '.json'; 

		if ( !empty( $queryArgs ) )
			$url = add_query_arg( $queryArgs, $url );
		
		$result = wp_remote_get( $url, $options );

		/* Try to parse the response */
		$plugin_info = null;
		if ( !is_wp_error( $result ) && isset( $result['response']['code'] ) && ( $result['response']['code'] == 200 ) && !empty( $result['body'] ) ) {

			$apiResponse = json_decode( $result['body'] );

			if ( empty( $apiResponse ) || !is_object( $apiResponse ) ) {
				return null;
			} elseif ( empty( $apiResponse->name ) || empty( $apiResponse->version ) ) {
				return null;
			} else {
				$plugin_info = new stdClass();
				foreach ( get_object_vars( $apiResponse ) as $key => $value ) {
					$plugin_info->$key = $value;
				}
			}
		}

		$plugin_info = apply_filters( 'puc_request_info_result-' . $native_slug, $plugin_info, $result );
		return $plugin_info;
	}
}

/** Transform plugin info into the format used by the native WordPress.org API */
if ( ! function_exists( 'bestwebsoft_to_wp_format' ) ) {
	function bestwebsoft_to_wp_format( $plugin_info ) {
		$info = new StdClass;
		$info = $plugin_info;
		
		/* The custom update API is built so that many fields have the same name and format as those returned by the native WordPress.org API. These can be assigned directly. */
		$sameFormat = array(
			'name', 'slug', 'version', 'requires', 'tested', 'rating', 'upgrade_notice',
			'num_ratings', 'downloaded', 'homepage', 'last_updated',
		);

		foreach ( $sameFormat as $field ) {
			$info->$field = ( isset( $plugin_info->$field ) ) ? $plugin_info->$field : null;
		}
		
		$info->author = !empty( $plugin_info->author_homepage ) ? sprintf( '<a href="%s">%s</a>', $plugin_info->author_homepage, $plugin_info->author ) : $plugin_info->author;
		
		if ( is_object( $plugin_info->sections ) ) {
			$info->sections = get_object_vars( $plugin_info->sections );
		} elseif ( is_array( $plugin_info->sections ) ) {
			$info->sections = $plugin_info->sections;
		} else {
			$info->sections = array( 'description' => '' );
		}

		if ( !empty( $plugin_info->banners ) ) {
			$info->banners = is_object( $plugin_info->banners ) ? get_object_vars( $plugin_info->banners ) : $plugin_info->banners;
			$info->banners = array_intersect_key( $info->banners, array( 'high' => true, 'low' => true ) );
		}

		return $info;
	}
}

if ( ! function_exists( 'bestwebsoft_inject_info' ) ) {
	function bestwebsoft_inject_info( $result, $action, $args, $native_slug ) {
		if ( ! ( $action == 'plugin_information' && isset( $args->slug ) && $args->slug == $native_slug ) )
			return $result;

		$plugin_info = bestwebsoft_request_info( $native_slug );
		$plugin_info = apply_filters( 'puc_pre_inject_info-' . $native_slug, $plugin_info );

		if ( $plugin_info )
			return bestwebsoft_to_wp_format( $plugin_info );
	
		return $result;
	}
}

/* Inject BWS non-free plugin updates into the response array. */
add_filter( 'pre_set_site_transient_update_plugins', 'bestwebsoft_wp_update_plugins' );
add_filter( 'pre_set_transient_update_plugins', 'bestwebsoft_wp_update_plugins' );

add_action( 'admin_init', 'bestwebsoft_wp_schedule_update_checks' );
add_action( 'bestwebsoft_wp_update_plugins', 'bestwebsoft_wp_update_plugins_cron' );
add_action( 'upgrader_process_complete', 'bestwebsoft_upgrader_process_complete', 10, 2 );