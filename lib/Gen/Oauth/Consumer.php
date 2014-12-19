<?php

/**
 * interface for Oauth consumer.
 *
 * This interface defines the method used to fetch some oauth protected data.
 * This interface is an abstraction of oauth consumer method.
 * @author Anthony BARRE
 */
abstract class Gen_Oauth_Consumer{

	/****************************************************************************************************
	 SIGNATURE METHOD
	 ****************************************************************************************************/
	/**
	 * The HMAC-SHA1 signature method uses the HMAC-SHA1 signature algorithm as defined in [RFC2104]
	 */
	const PIMP_OAUTH_SIGN_METH_HMAC_SHA1 = "HMACSHA1";
	/**
	 * PLAINTEXT method does not provide any security protection and SHOULD only be used
	 * over a secure channel such as HTTPS.
	 */
	const PIMP_OAUTH_SIGN_METH_PLAINTEXT = "PLAINTEXT";
	/**
	 * The RSA-SHA1 signature method uses the RSASSA-PKCS1-v1_5 signature algorithm as defined in [RFC3447]
	 */
	const PIMP_OAUTH_SIGN_METH_RSA_SHA1  = "RSA-SHA1";

	/****************************************************************************************************
	 AUTH TYPE
	 ****************************************************************************************************/

	/**
	 * Passe les paramètres OAuth dans l'entête HTTP Authorization.
	 */
	const PIMP_OAUTH_AUTH_TYPE_AUTHORIZATION = "AUTHORIZATION";

	/**
	 * Ajoute les paramètres OAuth au corps de la requête HTTP POST.
	 */
	const PIMP_OAUTH_AUTH_TYPE_FORM = "FORM";

	/**
	 * Ajoute les paramètres OAuth à l'URI.
	 */
	const PIMP_OAUTH_AUTH_TYPE_URI = "URI";

	public $oauthVersion = "1.0";

	public $nonce;
	/****************************************************************************************************
	 TOKEN HANDLER
	 ****************************************************************************************************/

	abstract public function __construct($consumerKey, $consumerSecret,$authType = self::PIMP_OAUTH_AUTH_TYPE_AUTHORIZATION);

	/**
	 * use for the first stage of the process
	 * @param URL requestTokenUrl is given by the data server.
	 * @param optional URL callbackURL is the url where the user is redirected after the authorization
	 */
	abstract public function getRequestToken($requestTokenUrl,$callbackUrl);

	/**
	 * use for the second stage of the process
	 */
	public function getAuthorizeURL($authorizeUrl, $requestToken){
		return $authorizeUrl . "?oauth_token={$requestToken}";
	}

	/**
	 * use for the third stage of the process
	 *  URL $access_token_url  [,  string $auth_session_handle  [,  string $verifier_token  ]]
	 *  @param URL accessTokenUrl is given by the data server.
	 */
	abstract public function getAccessToken($accessTokenUrl,$requestToken,$requestTokenSecret,$verifiedToken);

	/****************************************************************************************************
	 DATA HANDLER
	 ****************************************************************************************************/

	/**
	 * fetch a OAuth protected resource
	 * Follow the  REST definition of GET : http://en.wikipedia.org/wiki/Representational_State_Transfer
	 */
	abstract public function fetch($action,$protectedResourceUrl,$accessToken,$accessTokenSecret,$properties=array());

	abstract public function disableSSLChecks();
	abstract public function enableSSLChecks();

	abstract public function setSignatureMethod($signatureMethod);
	abstract public function setNonce($nonce);
	abstract public function setOauthVersion($version);
	
}