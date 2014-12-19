<?php
require_once ('Gen/Oauth/Consumer.php');
require_once ('Oauth/OAuth.php');
require_once ('Gen/Http/Request.php');

class Gen_Oauth_AlternOauth extends Gen_Oauth_Consumer{
	/* @var OAuth object */
	private $_consumer;

	/* @var object OAuthSignatureMethod */
	private $_signMethod;

	/* @var string Contains the last HTTP status code returned. */
	public $http_code;

	/* @var string Contains the last HTTP headers returned. */
	public $http_info;

	/* Set timeout default. */
	public $timeout = 30;

	/* Set connect timeout. */
	public $connecttimeout = 30;

	/* Verify SSL Cert. */
	public $ssl_verifypeer = FALSE;

	/* Set the useragnet. */
	public $useragent = 'genesis';

	function __construct($consumerKey, $consumerSecret, $authType = self::PIMP_OAUTH_AUTH_TYPE_AUTHORIZATION){
		if(!class_exists('OAuthConsumer'))
		throw new Exception("La classe OAuthConsumer n'existe pas");
			
		$this->_consumer = new OAuthConsumer($consumerKey,$consumerSecret);
	}


	public function setOauthVersion($version){
		$this->oauthVersion = $version;
	}

	public function setNonce($nonce){
		$this->nonce = $nonce;
	}

	public function setSignatureMethod($signatureMethod){
		//manage the signature method;
		switch ($signatureMethod) {
			case self::PIMP_OAUTH_SIGN_METH_HMAC_SHA1:
				$this->_signMethod = new OAuthSignatureMethod_HMAC_SHA1();
				break;
			case self::PIMP_OAUTH_SIGN_METH_PLAINTEXT:
				$this->_signMethod = new OAuthSignatureMethod_RSA_SHA1();
				break;
			case self::PIMP_OAUTH_SIGN_METH_RSA_SHA1:
				$this->_signMethod = new OAuthSignatureMethod_PLAINTEXT();
				break;
			default:
				throw new OAuthException("Signature not known");
		}
	}

	/**
	 * Get a request_token from Twitter
	 *
	 * @returns a key/value array containing oauth_token and oauth_token_secret
	 */
	public function getRequestToken($requestTokenUrl,$callbackUrl){
		$parameters = array();
		if (!empty($callbackUrl)) {
			$parameters['oauth_callback'] = $callbackUrl;
		}

		$request = $this->oAuthRequest($requestTokenUrl, 'GET', NULL, $parameters);
		$token = OAuthUtil::parse_parameters($request);

		if($this->http_code != 200) {
			throw new OAuthException(print_r(array($this->http_code,$token),true));
		}
		return $token;
	}

	/**
	 * Exchange request token and secret for an access token and
	 * secret, to sign API calls.
	 *
	 * @returns array("oauth_token" => "the-access-token",
	 *				"oauth_token_secret" => "the-access-secret",
	 *				"user_id" => "9436992",
	 *				"screen_name" => "abraham")
	 */
	public function getAccessToken($accessTokenUrl,$requestToken,$requestTokenSecret,$verifiedToken){
		$parameters = array();
		if (!empty($verifiedToken)) {
			$parameters['oauth_verifier'] = $verifiedToken;
		}

		$token = new OAuthConsumer($requestToken, $requestTokenSecret);

		$request = $this->oAuthRequest($accessTokenUrl, 'GET', $token, $parameters);
		$token = OAuthUtil::parse_parameters($request);

		if($this->http_code != 200) {
			throw new OAuthException(print_r(array($this->http_code,$token),true));
		}

		return $token;
	}

	/**
	 * get a OAuth protected resource
	 * Follow the  REST definition of GET : http://en.wikipedia.org/wiki/Representational_State_Transfer
	 */
	public function fetch($action,$protectedResourceUrl,$accessToken,$accessTokenSecret,$properties=array()){
		$token = new OAuthConsumer($accessToken, $accessTokenSecret);

		$response = $this->oAuthRequest($protectedResourceUrl, $action, $token, $properties);

		if($this->http_code != 200) {
			throw new OAuthException(print_r(array($this->http_code,$response),true));
		}

		return $response;
	}

	public function disableSSLChecks(){
		$this->ssl_verifypeer = false;
	}

	public function enableSSLChecks(){
		$this->ssl_verifypeer = true;
	}


	/**
	 * Format and sign an OAuth / API request
	 */
	private function oAuthRequest($url, $method, $token, $parameters) {
		
		$request = OAuthRequest::from_consumer_and_token($this->_consumer, $token, $method, $url, $parameters);
		$request->sign_request($this->_signMethod, $this->_consumer, $token);
		
		switch ($method) {
			case 'GET':
				$httpRequest = Gen_Http_Request::send($request->to_url(), 'GET', array(), $this->ssl_verifypeer);
				break;
			default:
				$httpRequest = Gen_Http_Request::send($request->get_normalized_http_url(), $method, $request->to_postdata(), $this->ssl_verifypeer);
				break;
		}
		
		$this->http_info = $httpRequest['info'];
		$this->http_code = $httpRequest['status'];
		
		return $httpRequest['response'];
	}
}