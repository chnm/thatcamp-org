<?php
	class Yop_Poll_Config {
		protected $config;

		public function __construct( array $config ) {
			$this->config = $config;
		}

		public function __get( $name ) {
			$value = false;
			if ( array_key_exists( $name, $this->config ) ) {
				$value = $this->config[$name];
			}
			return $value;
		}
	}
