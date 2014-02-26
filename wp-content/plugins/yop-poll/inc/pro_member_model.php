<?php
	class Yop_Poll_Pro_Member_Model {
		protected static $_instance		= NULL;
		protected $pro_options			= NULL;
		public $api_response			= NULL;
		public $api_status				= NULL;
		public $api_error_msg			= NULL;
		public $api_success_msg			= NULL;
		public $api_return_data			= array();

		var $error						= NULL;

		public static function getInstance( ) {
			if ( !isset( self::$_instance ) )
				self::$_instance = new Yop_Poll_Pro_Member_Model( );
			return self::$_instance;
		}

		public function Yop_Poll_Pro_Member_Model( ) {
			self::$_instance 	= $this;
			$this->pro_options	= get_option( 'yop_poll_pro_options' );
		}

		public function get_api_verify_access( $api_key ) {
			$url				= $this->pro_options['pro_api_server_url'] . '/api/auth/verify_access/';
			$params				= array( 'api_key' => $api_key );
			$result				= self::curl_post( $url, $params );
			if ( $this->parse_api_result( $result ) )
				return true;
			return false;
		}

		public function register_pro_member( $pro_key ) {
			$url				= $this->pro_options['pro_api_server_url'] . '/api/register/pro_member';
			$params				= array(
				'pro_key' 	=> $pro_key,
				'siteurl'	=> site_url()
				);
			$result				= self::curl_post( $url, $params );
			if ( $this->parse_api_result( $result ) )
				return true;
			return false;
		}

		private function parse_api_result( $result ) {
			$pattern			= '\[(\[?)(response)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
			if ( preg_match( "/$pattern/s", $result, $matches ) ) {
				$response	= json_decode( $matches[5], true );
				if ( $response ) {
					$this->api_response		= $response;
					$this->api_status		= $response['status'];
					$this->api_error_msg	= $response['error_message'];
					$this->api_success_msg	= $response['success_message'];
					$this->api_return_data	= $response['return_data'];
					if ( $this->api_status == 'success') {
						return true;
					}
					else {
						$this->error	= $this->api_error_msg;
						return false;
					}
				}
			}
			$this->error	= __( 'Invalid Response From Api Server!', 'yop_poll' );
			return false;
		}

		public static function curl_post( $url, array $post = NULL, array $options = array() ) {
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

		public static function curl_get( $url, array $get = NULL, array $options = array()) {
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