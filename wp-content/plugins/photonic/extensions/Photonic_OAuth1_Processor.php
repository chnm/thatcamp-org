<?php
abstract class Photonic_OAuth1_Processor extends Photonic_Processor {
	/**
	 * Access Token URL
	 *
	 * @abstract
	 * @return string
	 */
	public abstract function access_token_URL();

	/**
	 * Authenticate URL
	 *
	 * @abstract
	 * @return string
	 */
	public abstract function authenticate_URL();

	/**
	 * Authorize URL
	 *
	 * @return string
	 */
	public abstract function authorize_URL();

	/**
	 * Request Token URL
	 *
	 * @return string
	 */
	public abstract function request_token_URL();

	/**
	 * The web-service invocation end-point for this provider. Mostly used for validating if the current token is still good for access.
	 *
	 * @return string
	 */
	public abstract function end_point();

	/**
	 * The web-service method to invoke to validate if the token is good.
	 *
	 * @return string
	 */
	public abstract function check_access_token_method();

	/**
	 * Get the authorize URL
	 *
	 * @param $token
	 * @param bool $sign_in
	 * @return string
	 */
	function get_authorize_URL($token, $sign_in = true) {
		if (empty($sign_in)) {
			return $this->authorize_URL() . "?oauth_token={$token['oauth_token']}";
		}
		else {
			return $this->authenticate_URL() . "?oauth_token={$token['oauth_token']}";
		}
	}

	/**
	 * Generates the signature for the OAuth call.
	 * See here for the signature-generation methodology: http://www.wackylabs.net/2011/12/oauth-and-flickr-part-2/
	 *
	 * @param null $api_call
	 * @param null $api_args
	 * @param null $method
	 * @param array $oauth_token
	 * @return string
	 */
	protected function generate_signature($api_call = null, $api_args = NULL, $method = null, $oauth_token = array()) {
		$encoded_key = Photonic_Processor::urlencode_rfc3986($this->api_secret) . '&';
		if (isset($oauth_token['oauth_token_secret'])) {
			$encoded_key .= Photonic_Processor::urlencode_rfc3986($oauth_token['oauth_token_secret']);
		}

		$method = is_null($method) ? 'POST' : $method;
		$params = array(
			'oauth_version' => $this->oauth_version,
			'oauth_nonce' => $this->nonce,
			'oauth_timestamp' => $this->oauth_timestamp,
			'oauth_consumer_key' => $this->api_key,
			'oauth_signature_method' => $this->oauth_signature_method()
		);

		if ($this->provider == 'smug') {
			$params['method'] = $api_call;
		}

		if (isset($oauth_token['oauth_token'])) {
			$params['oauth_token'] = $oauth_token['oauth_token'];
		}

		if (isset($oauth_token['oauth_verifier'])) {
			$params['oauth_verifier'] = $oauth_token['oauth_verifier'];
		}

		$params = (!empty($api_args)) ? array_merge($params, $api_args) : $params;

		$end_point = $this->provider == 'smug' ? $this->end_point() : $api_call;

		$string = Photonic_Processor::build_query($params);
		$base_string = $method . '&' . Photonic_Processor::urlencode_rfc3986($end_point) . '&' . Photonic_Processor::urlencode_rfc3986($string);
		$sig = base64_encode(hash_hmac('sha1', $base_string, $encoded_key, true));

		$this->signature_parameters = array(
			'parameters' => $params,
			'base_string' => $base_string,
			'key' => $encoded_key,
		);
		return $sig;
	}

	/**
	 * Gets an OAuth request token using the API Key and API Secret provided in the plugin's back-end options.
	 * Once a token has been successfully got, the user is sent to an Authorization page where he can allow access for your site.
	 *
	 * @param null $oauth_callback
	 * @return null
	 */
	function get_request_token($oauth_callback = null) {
		if (!empty($oauth_callback)) {
			$callback = $oauth_callback;
		}
		else {
			$callback = Photonic::get_callback_url();
		}

		if ($this->provider == 'flickr') {
			$method = 'GET';
		}
		else {
			$method = 'POST';
		}

		$signature = $this->generate_signature($this->request_token_URL(), array('oauth_callback' => $callback), $method);
		$parameters = array (
			'oauth_version' => $this->oauth_version,
			'oauth_nonce' => $this->nonce,
			'oauth_timestamp' => $this->oauth_timestamp,
			'oauth_callback' => $callback,
			'oauth_consumer_key' => $this->api_key,
			'oauth_signature_method' => $this->oauth_signature_method(),
			'oauth_signature' => $signature,
		);

		if ($this->provider == 'smug') {
			$parameters['method'] = $this->request_token_URL();
		}

		$end_point = $this->provider == 'smug' ? $this->end_point() : $this->request_token_URL();
		if ($method == 'GET') {
			$end_point .= '?'.Photonic_Processor::build_query($parameters);
			$parameters = null;
		}

		$response = Photonic::http($end_point, $method, $parameters);
		$token = $this->parse_token($response);

		$secret = 'photonic_'.$this->provider.'_api_secret';
		global $$secret;
		// We will hash the secret to store the cookie. Otherwise the cookie for the visitor will have the secret for the app for the plugin user.
		$secret = md5($$secret, false);

		if (isset($token['oauth_token']) && isset($token['oauth_token_secret'])) {
			setcookie('photonic-'.$secret.'-oauth-token', $token['oauth_token'], time() + 365 * 60 * 60 * 24, COOKIEPATH);
			setcookie('photonic-'.$secret.'-oauth-token-secret', $token['oauth_token_secret'], time() + 365 * 60 * 60 * 24, COOKIEPATH);
			setcookie('photonic-'.$secret.'-oauth-token-type', 'request', time() + 365 * 60 * 60 * 24, COOKIEPATH);
		}

		return $token;
	}

	/**
	 * Takes an OAuth request token and exchanges it for an access token.
	 *
	 * @param $request_token
	 */
	function get_access_token($request_token) {
		if ($this->provider == 'flickr') {
			$method = 'GET';
		}
		else {
			$method = 'POST';
		}

		$signature = $this->generate_signature($this->access_token_URL(), array(), $method, $request_token);
		$parameters = array (
			'oauth_consumer_key' => $this->api_key,
			'oauth_nonce' => $this->nonce,
			'oauth_signature' => $signature,
			'oauth_signature_method' => $this->oauth_signature_method(),
			'oauth_timestamp' => $this->oauth_timestamp,
			'oauth_token' => $request_token['oauth_token'],
			'oauth_version' => $this->oauth_version,
		);

		if (isset($request_token['oauth_verifier'])) {
			$parameters['oauth_verifier'] = $request_token['oauth_verifier'];
		}

		if ($this->provider == 'smug') {
			$parameters['method'] = $this->access_token_URL();
		}

		$end_point = $this->provider == 'smug' ? $this->end_point() : $this->access_token_URL();
		if ($method == 'GET') {
			$end_point .= '?'.Photonic_Processor::build_query($parameters);
			$parameters = null;
		}

		$response = Photonic::http($end_point, $method, $parameters);
		$token = $this->parse_token($response);

		$secret = 'photonic_'.$this->provider.'_api_secret';
		global $$secret;
		// We will has the secret to store the cookie. Otherwise the cookie for the visitor will have the secret for the app for the plugin user.
		$secret = md5($$secret, false);

		if (isset($token['oauth_token']) && isset($token['oauth_token_secret'])) {
			setcookie('photonic-'.$secret.'-oauth-token', $token['oauth_token'], time() + 365 * 60 * 60 * 24, COOKIEPATH);
			setcookie('photonic-'.$secret.'-oauth-token-secret', $token['oauth_token_secret'], time() + 365 * 60 * 60 * 24, COOKIEPATH);
			setcookie('photonic-'.$secret.'-oauth-token-type', 'access', time() + 365 * 60 * 60 * 24, COOKIEPATH);
			if (isset($token['oauth_verifier'])) {
				setcookie('photonic-'.$secret.'-oauth-verifier', $token['oauth_verifier'], time() + 365 * 60 * 60 * 24, COOKIEPATH);
			}
		}

		return $token;
	}

	/**
	 * Tests to see if the OAuth Access Token that is cached is still valid. This is important because a user might have manually revoked
	 * access for your app through the provider's control panel.
	 *
	 * @param $request_token
	 * @return array|WP_Error
	 */
	function check_access_token($request_token) {
		if ($this->provider == 'flickr') {
			$method = 'GET';
		}
		else {
			$method = 'POST';
		}

		$signature = $this->generate_signature($this->check_access_token_method(), array('method' => $this->check_access_token_method()), $method, $request_token);
		$parameters = array (
			'oauth_consumer_key' => $this->api_key,
			'oauth_nonce' => $this->nonce,
			'oauth_signature' => $signature,
			'oauth_signature_method' => $this->oauth_signature_method(),
			'oauth_timestamp' => $this->oauth_timestamp,
			'oauth_token' => $request_token['oauth_token'],
			'oauth_version' => $this->oauth_version,
			'method' => $this->check_access_token_method(),
		);

		$end_point = $this->end_point();
		if ($method == 'GET') {
			$end_point .= '?'.Photonic_Processor::build_query($parameters);
			$parameters = null;
		}

		$response = Photonic::http($end_point, $method, $parameters);
		return $response;
	}

	/**
	 * Takes the response for the "Check access token", then tries to determine whether the check was successful or not.
	 *
	 * @param $response
	 * @return bool
	 */
	public function is_access_token_valid($response) {
		if (is_wp_error($response)) {
			return false;
		}

		$body = $response['body'];
		$body = json_decode($body);

		if ($body->stat == 'fail') {
			return false;
		}
		return true;
	}

	/**
	 * Checks if authentication has been enabled and the user has authenticated. If so, it signs the call, then adds the additional parameters to it.
	 * This method also attaches the oauth_signature to the parameters.
	 *
	 * @param $api_method
	 * @param $method
	 * @param $parameters
	 * @return mixed
	 */
	public function sign_call($api_method, $method, $parameters) {
		$allow_oauth = 'photonic_'.$this->provider.'_allow_oauth';
		global $$allow_oauth;
		if ($$allow_oauth) {
			$cookie = Photonic::parse_cookie();
			if (isset($cookie[$this->provider]) && isset($cookie[$this->provider]['oauth_token']) && isset($cookie[$this->provider]['oauth_token_secret']) &&
					isset($cookie[$this->provider]['oauth_token_type']) && $cookie[$this->provider]['oauth_token_type'] == 'access') {
				$token = array('oauth_token' => $cookie[$this->provider]['oauth_token'], 'oauth_token_secret' => $cookie[$this->provider]['oauth_token_secret']);
				$this->nonce = $this->nonce();
				$this->oauth_timestamp = time();
				$signature = $this->generate_signature($api_method, $parameters, $method, $token);
				if (isset($this->signature_parameters) && isset($this->signature_parameters['parameters'])) {
					$this->signature_parameters['parameters']['oauth_signature'] = $signature;
					return $this->signature_parameters['parameters'];
				}
			}
		}
		return $parameters;
	}
}