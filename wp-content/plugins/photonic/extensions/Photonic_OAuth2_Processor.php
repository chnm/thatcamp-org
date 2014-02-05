<?php

abstract class Photonic_OAuth2_Processor extends Photonic_Processor {
	public $scope, $response_type, $client_id, $client_secret, $state;

	function __construct() {
		parent::__construct();
	}

	public abstract function authentication_url();

	public abstract function access_token_url();

	public function redirect_url() {
		return get_site_url();
	}

	public function get_authorization_url() {
		$url = add_query_arg('test', 'test');
		$url = remove_query_arg('test', $url);
		$parameters = array(
			'response_type' => $this->response_type,
			'redirect_uri' => $this->redirect_url(),
			'client_id' => $this->client_id,
			'scope' => $this->scope,
			'access_type' => 'offline',
			'state' => md5($this->client_secret.$this->provider).'::'.urlencode($url),
		);
		return $this->authentication_url()."?".$this->build_query($parameters);
	}

	/**
	 * Takes an OAuth request token and exchanges it for an access token.
	 *
	 * @param $request_token
	 */
	function get_access_token($request_token) {
		$code = $request_token['code'];
		$state_args = explode('::', $request_token['state']);
		$secret = md5($this->client_secret, false);

		if ($state_args[0] == md5($this->client_secret.$this->provider)) {
			$url = urldecode($state_args[1]);
			$response = Photonic::http($this->access_token_URL(), 'POST', array(
				'code' => $code,
				'grant_type' => 'authorization_code',
				'client_id' => $this->client_id,
				'client_secret' => $this->client_secret,
				'redirect_uri' => $this->redirect_url(),
			));
			if (is_wp_error($response)) {
				$url = add_query_arg('error', $response->get_error_code(), $url);
			}
			else if ($response == null) {
				$url = add_query_arg('error', 'null', $url);
			}
			else {
				$body = $response['body'];
				$body = json_decode($body);
				if (isset($_COOKIE['photonic-' . $secret . '-oauth-token'])) {
					unset($_COOKIE['photonic-' . $secret . '-oauth-token']);
				}
				if (isset($_COOKIE['photonic-' . $secret . '-oauth-refresh-token']) && isset($body->refresh_token)) {
					unset($_COOKIE['photonic-' . $secret . '-oauth-refresh-token']);
				}
				if (isset($_COOKIE['photonic-' . $secret . '-oauth-token-type'])) {
					unset($_COOKIE['photonic-' . $secret . '-oauth-token-type']);
				}
				if (isset($_COOKIE['photonic-' . $secret . '-oauth-token-created'])) {
					unset($_COOKIE['photonic-' . $secret . '-oauth-token-created']);
				}
				if (isset($_COOKIE['photonic-' . $secret . '-oauth-token-expires'])) {
					unset($_COOKIE['photonic-' . $secret . '-oauth-token-expires']);
				}
				$cookie_expiration = 365 * 24 * 60 * 60;
				setcookie('photonic-' . $secret . '-oauth-token', $body->access_token, time() + $cookie_expiration, COOKIEPATH);
				if (isset($body->refresh_token)) {
					setcookie('photonic-' . $secret . '-oauth-refresh-token', $body->refresh_token, time() + $cookie_expiration, COOKIEPATH);
				}
				setcookie('photonic-' . $secret . '-oauth-token-type', $body->token_type, time() + $cookie_expiration, COOKIEPATH);
				setcookie('photonic-' . $secret . '-oauth-token-created', time(), time() + $cookie_expiration, COOKIEPATH);
				setcookie('photonic-' . $secret . '-oauth-token-expires', $body->expires_in, time() + $cookie_expiration, COOKIEPATH);
			}
		}
		else {
			$url = remove_query_arg(array('token', 'code', 'state'));
		}
		wp_redirect($url);
		exit();
	}

	function refresh_token($refresh_token) {
		$response = Photonic::http($this->access_token_url(), 'POST', array(
			'client_id' => $this->client_id,
			'client_secret' => $this->client_secret,
			'refresh_token' => $refresh_token,
			'grant_type' => 'refresh_token'
		));
		if (!is_wp_error($response)) {
			$secret = md5($this->client_secret, false);
			$body = $response['body'];
			$body = json_decode($body);
			if (isset($_COOKIE['photonic-' . $secret . '-oauth-token'])) {
				unset($_COOKIE['photonic-' . $secret . '-oauth-token']);
			}
			if (isset($_COOKIE['photonic-' . $secret . '-oauth-token-type'])) {
				unset($_COOKIE['photonic-' . $secret . '-oauth-token-type']);
			}
			if (isset($_COOKIE['photonic-' . $secret . '-oauth-token-created'])) {
				unset($_COOKIE['photonic-' . $secret . '-oauth-token-created']);
			}
			if (isset($_COOKIE['photonic-' . $secret . '-oauth-token-expires'])) {
				unset($_COOKIE['photonic-' . $secret . '-oauth-token-expires']);
			}
			$cookie_expiration = 365 * 24 * 60 * 60;
			setcookie('photonic-' . $secret . '-oauth-token', $body->access_token, time() + $cookie_expiration, COOKIEPATH);
			if (isset($body->refresh_token)) {
				setcookie('photonic-' . $secret . '-oauth-refresh-token', $body->refresh_token, time() + $cookie_expiration, COOKIEPATH);
			}
			setcookie('photonic-' . $secret . '-oauth-token-type', $body->token_type, time() + $cookie_expiration, COOKIEPATH);
			setcookie('photonic-' . $secret . '-oauth-token-created', time(), time() + $cookie_expiration, COOKIEPATH);
			setcookie('photonic-' . $secret . '-oauth-token-expires', $body->expires_in, time() + $cookie_expiration, COOKIEPATH);
		}
	}

	function is_token_expired($token) {
		if (empty($token)) {
			return true;
		}
		if (!isset($token['oauth_token']) || !isset($token['oauth_token_created']) || !isset($token['oauth_token_expires'])) {
			return true;
		}
		$current = time();
		if ($token['oauth_token_created'] + $token['oauth_token_expires'] < $current) {
			return true;
		}
		return false;
	}
}