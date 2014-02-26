<?php
	/**
	* This is the base class that admin and public extend.
	* It makes the config class available to both subclasses and has two methods for plugging into WP filters and actions.
	* @abstract
	*/
	abstract class Yop_Poll_Plugin {
		protected $_config;

		/**
		* This constructor initializes the configuration data and then prepares init function
		*
		* @param Yop_Poll_Config $config
		* @return Yop_Poll_Plugin
		*/
		public function __construct( Yop_Poll_Config $config ) {
			$this->_config = $config;
			$this->init();
		}

		/**
		* This function is used by admin and public to setup filters and actions
		* @abstract
		* @access protected
		*/
		abstract protected function init();

		/**
		* This function will be used mainly in admin class for WP actions
		*
		* @param mixed $action
		* @param mixed $function
		* @param mixed $priority
		* @param mixed $accepted_args
		*/
		protected function add_action( $action, $function = '', $priority = 10, $accepted_args = 1 ) {
			add_action( $action, array($this, $function == '' ? $action : $function ), $priority, $accepted_args );
		}

		protected function remove_action( $action, $function = '' ) {
			remove_action( $action, array($this, $function == '' ? $action : $function ) );
		}

		/**
		* This function will be used mainly in public class for WP filters
		*
		* @param mixed $filter wp filter
		* @param mixed $function your function to be executed with wp filter
		* @param mixed $priority number between 1 and 10
		* @param mixed $accepted_args
		*/
		protected function add_filter( $filter, $function, $priority = 10, $accepted_args = 1 ) {
			add_filter( $filter, array($this, $function == '' ? $filter : $function ), $priority, $accepted_args );
		}
}