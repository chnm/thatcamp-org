<?php
	class Yop_Poll_Facebook_Connect_Model {
		public $facebook_app_available	= false;
		protected static $_instance		= NULL;
		private $facebok_user			= NULL;
		private $facebook_credentials	= NULL;

		var $error						= NULL;

		public static function getInstance( ) {
			if ( !isset( self::$_instance ) )
				self::$_instance = new Yop_Poll_Facebook_Connect_Model( );
			return self::$_instance;
		}

		public function Yop_Poll_Facebook_Connect_Model( ) {
			self::$_instance 	= $this;
		}

		private function load_facebook_credentials( $test = false ) {
			$this->facebook_credentials	= get_option( 'yop_poll_facebook_credentials', false );
			if ( $test ) {
                $result	= $this->curl_get( $this->facebook_credentials['connect_url'] );
                $temp	= parse_json( $result );
                if ( 'true'	=== $temp['success'] ) {

				}
				else {
					$this->error	= __( 'Sorry! Connection with server failed!', 'yop_poll' );
					return false;
				}
			}
		}

		private function test_conection() {

		}

		private function curl_post( $url, array $post = NULL, array $options = array() ) {
			$defaults	= array(
				CURLOPT_POST			=> 1,
				CURLOPT_HEADER			=> 0,
				CURLOPT_URL				=> $url,
				CURLOPT_FRESH_CONNECT	=> 1,
				CURLOPT_RETURNTRANSFER	=> 1,
				CURLOPT_FORBID_REUSE	=> 1,
				CURLOPT_TIMEOUT			=> 4,
				CURLOPT_POSTFIELDS		=> http_build_query($post)
			);

			$ch	= curl_init();
			curl_setopt_array($ch, ( $options + $defaults ) );
			if( ! $result = curl_exec( $ch ) ) {
				$this->error	= curl_error( $ch );
				return false;
			}
			curl_close( $ch );
			return $result;
		}

		private function curl_get( $url, array $get = NULL, array $options = array()) {
			$defaults	= array(
				CURLOPT_URL				=> $url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($get),
				CURLOPT_HEADER			=> 0,
				CURLOPT_RETURNTRANSFER	=> TRUE,
				CURLOPT_TIMEOUT			=> 4
			);

			$ch	= curl_init();
			curl_setopt_array( $ch, ( $options + $defaults ) );
			if( ! $result = curl_exec( $ch ) ) {
				$this->error	= curl_error( $ch );
				return false;
			}
			curl_close($ch);
			return $result;
		}
	}
