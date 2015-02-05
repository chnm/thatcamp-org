<?php
	class Yop_Poll_Public extends Yop_Poll_Plugin {
		protected function init() {
			$this->add_action( 'init', 'public_loader' );
		}

		public function public_loader() {
			$this->add_action( 'plugins_loaded', 'load_translation_file', 1 );
		}

		public function load_translation_file() {
			$plugin_path = $this->_config->plugin_dir . '/' . $this->_config->languages_dir;
			load_plugin_textdomain( 'yop_poll', false, $plugin_path );
		}
}