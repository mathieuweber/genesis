<?php
require_once ('Gen/Oauth/Consumer.php');


class Gen_Oauth_PeclOauth extends Gen_Oauth_Consumer{

	
	const PIMP_OAUTH_SIGN_METH_HMAC_SHA1 = OAUTH_SIG_METHOD_HMACSHA1;
	
	/****************************************************************************************************
												 AUTH TYPE											   
	****************************************************************************************************/
	const PIMP_OAUTH_AUTH_TYPE_AUTHORIZATION = OAUTH_AUTH_TYPE_AUTHORIZATION;
	const PIMP_OAUTH_AUTH_TYPE_FORM = OAUTH_AUTH_TYPE_FORM;
	const PIMP_OAUTH_AUTH_TYPE_URI = OAUTH_AUTH_TYPE_URI;
	/**
	 * @var OAuth object
	 */
	private $_oauth;

	function __construct($consumerKey, $consumerSecret,$authType = self::PIMP_OAUTH_AUTH_TYPE_AUTHORIZATION){
		if(!class_exists('OAuth'))
			throw new Exception("La classe OAuth n'existe pas");

		//only HMAC_SHA1 is supported 
		$this->_oauth = new OAuth($consumerKey,$consumerSecret, self::PIMP_OAUTH_SIGN_METH_HMAC_SHA1, $authType);
	}
	
	
	public function setOauthVersion($version){
		$this->_oauth->setVersion($version);
	}
	
	public function setNonce($nonce){
		$this->_oauth->setNonce($nonce);
	} 
	
	
	public function setSignatureMethod($signatureMethod){
		//seul HMAC_SHA1 est supporté
		//throw new Exception("SignatureMethod not implemented");
	}
	
	public function getRequestToken($requestTokenUrl,$callbackUrl){
		$token =  $this->_oauth->getRequestToken($requestTokenUrl,$callbackUrl);
		
		$httpInfo = $this->_oauth->getLastResponseInfo();
		if($httpInfo["http_code"] != 200) {
			throw new OauthException("{$httpInfo["http_code"]} : Failed fetching request token, response was: " . $this->_oauth->getLastResponse());
		}
		return $token;
	}
	
	public function getAccessToken($accessTokenUrl,$requestToken,$requestTokenSecret,$verifiedToken){
		$this->_oauth->setToken($requestToken,$requestTokenSecret);
		
		if(!empty($verifiedToken))
			$token =  $this->_oauth->getAccessToken($accessTokenUrl,NULL,$verifiedToken);
		else
			$token =  $this->_oauth->getAccessToken($accessTokenUrl);
			
		$httpInfo = $this->_oauth->getLastResponseInfo();
		
		if($httpInfo["http_code"] != 200) {
			throw new OauthException("{$httpInfo["http_code"]} : Failed fetching request token, response was: " . $this->_oauth->getLastResponse());
		}
		return $token;
	}
	
	/**
	 * get a OAuth protected resource
	 * Follow the  REST definition of GET : http://en.wikipedia.org/wiki/Representational_State_Transfer
	 */
	public function fetch($action,$protectedResourceUrl,$accessToken,$accessTokenSecret,$properties=array()){
		$this->_oauth->setToken($accessToken,$accessTokenSecret);
		$data =  $this->_oauth->fetch($protectedResourceUrl, $properties, $action, array());
		
		$httpInfo = $this->_oauth->getLastResponseInfo();
		
		if($httpInfo["http_code"] != 200) {
			throw new OauthException("{$httpInfo["http_code"]} :Failed fetching request token, response was: " . $this->_oauth->getLastResponse());
		}
		
		return $data;
	}
	
	public function disableSSLChecks(){
		$this->_oauth->disableSSLChecks();
	}
	
	public function enableSSLChecks(){
		$this->_oauth->enableSSLChecks();
	}
}