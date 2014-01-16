<?php

if ( !class_exists( 'mssls_setup' ) ) {

	class mssls_setup extends bit51_mssls {

		/**
		 * Verify case is set correctly and continue or die
		 */
		function __construct( $case = false ) {

			if ( !$case ) {
				die( 'error' );
			}

			switch ( $case ) {
				case 'activate': //active plugin
					$this->activate_execute();
					break;

				case 'deactivate': //deactivate plugin
					$this->deactivate_execute();
					break;

				case 'uninstall': //uninstall plugin
					$this->uninstall_execute();
					break;
			}
		}

		/**
		 * Entrypoint for activation
		 */
		function on_activate() {
			new mssls_setup( 'activate' );
		}

		/**
		 * Entrypoint for deactivation
		 */
		function on_deactivate() {

			$devel = false; //set to true to uninstall for development

			if ( $devel ) {
				$case = 'uninstall';
			} else {
				$case = 'deactivate';
			}

			new mssls_setup( $case );
		}

		/**
		 * Entrypoint for uninstall
		 */
		function on_uninstall() {
			if ( __FILE__ != WP_UNINSTALL_PLUGIN ) { //verify they actually clicked uninstall
				return;
			}

			new mssls_setup( 'uninstall' );
		}

		/**
		 * Execute activation functions
		 */
		function activate_execute() {
			$this->default_settings(); //verify and set default options

			$options = get_option( $this->plugindata );

			//update if version numbers don't match
			if ( isset( $options['version'] ) && $options['version'] != $this->pluginversion ) {
				$this->update_execute();
			}

			$options['version'] = $this->pluginversion; //set new version number

			//remove no support nag if it's been more than six months
			if ( !isset( $options['activatestamp'] ) || $options['activatestamp'] < ( time() - 15552000 ) ) {
				if ( isset( $options['no-nag'] ) ) {
					unset( $options['no-nag'] );
				}

				//set activate timestamp to today (they'll be notified again in a month)
				$options['activatestamp'] = time();
			}

			update_option( $this->plugindata, $options ); //save new plugin data
		}

		/**
		 * Execute update functions
		 */
		function update_execute() {
		}

		/**
		 * Execute deactivation functions
		 */
		function deactivate_execute() {
		}

		/**
		 * Execute uninstall functions
		 */
		function uninstall_execute() {

			//remove all settings
			foreach ( $this->settings as $settings ) {
				foreach ( $settings as $setting => $option ) {
					delete_option( $setting );
				}
			}

			//delete plugin information (version, etc)
			delete_option( $this->plugindata );
		}
	}
}