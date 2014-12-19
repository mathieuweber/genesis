<?php
/**
 * An Http Response Header Message
 *
 * for more details please see:
 *
 * Hypertext Transfer Protocol -- HTTP/1.1
 * @link http://www.w3.org/Protocols/rfc2616/rfc2616.html
 *
 * An Http Response is composed of a Header and a Body
 * Gen_Http_Response enable to set both
 *
 * use {@link send()} to send the response to the Client
 * send() just makes use of sendHeaders() and outputBody()
 *
 * it implements an helper function {@link redirectUrl()}
 * to redirect a Request to another url
 *
 * @category   Gen
 * @package	Gen_Http
 */
class Gen_Http_Response
{
	/**
	 * Http Version
	 */
	const VERSION = 'HTTP/1.1';
	
	/**
	 * The Response Status Code
	 * defaulted to 200 OK
	 */
	protected $_statusCode = 200;
	
	/**
	 * Response Filename
	 * used to output a file
	 */
	protected $_file = null;
	
	/**
	 * The Response is JSON formatted (e.g. for flash HTTPStatusEvent not working)
	 * defaulted to false
	 */
	protected $_hasJsonHeader = false;
	
	public static $_httpStatus = array(
		100  => 'Continue',
		101  => 'Switching Protocols',
		200  => 'OK',
		201  => 'Created',
		202  => 'Accepted',
		203  => 'Non-Authoritative Information',
		204  => 'No Content',
		205  => 'Reset Content',
		206  => 'Partial Content',
		300  => 'Multiple Choices',
		301  => 'Moved Permanently',
		302  => 'Found',
		303  => 'See Other',
		304  => 'Not Modified',
		305  => 'Use Proxy',
		307  => 'Temporary Redirect',
		400  => 'Bad Request',
		401  => 'Unauthorized',
		402  => 'Payment Required',
		403  => 'Forbidden',
		404  => 'Not Found',
		405  => 'Method Not Allowed',
		406  => 'Not Acceptable',
		407  => 'Proxy Authentication Required',
		408  => 'Request Time-out',
		409  => 'Conflict',
		410  => 'Gone',
		411  => 'Length Required',
		412  => 'Precondition Failed',
		413  => 'Request Entity Too Large',
		414  => 'Request-URI Too Large',
		415  => 'Unsupported Media Type',
		416  => 'Requested range not satisfiable',
		417  => 'Expectation Failed',
		500  => 'Internal Server Error',
		501  => 'Not Implemented',
		502  => 'Bad Gateway',
		503  => 'Service Unavailable',
		504  => 'Gateway Time-out',
		505  => 'HTTP Version not supported'
	);
	
	/**
	 * Response Headers
	 */
	/** general header fields */
	const CACHE_CONTROL	  	= 'Cache-Control';
	const CONNECTION		= 'Connection';
	const DATE			    = 'Date';
	const PRAGMA			= 'Pragma';
	const TRAILER			= 'Trailer';
	const TRANSFER_ENCODING = 'Transfer-Encoding';
	const UPGRADE			= 'Upgrade';
	const VIA				= 'Via';
	const WARNING			= 'Warning';
	
	/** response header fields */
	const ACCEPT_RANGES	  	 = 'Accept-Ranges';
	const AGE				 = 'Age';
	const ETAG			     = 'ETag';
	const LOCATION		     = 'Location';
	const PROXY_AUTHENTICATE = 'Proxy-Authenticate';
	const RETRY_AFTER		 = 'Retry-After';
	const SERVER			 = 'Server';
	const VARY			   	 = 'Vary';
	const WWW_AUTHENTICATE   = 'WWW-Authenticate';
	  
	/** entity header fields */  
	const ALLOWED = 'Allow';
	const CONTENT_ENCODING = 'Content-Encoding';
	const CONTENT_LANGUAGE = 'Content-Language';
	const CONTENT_LENGTH   = 'Content-Length';
	const CONTENT_LOCATION = 'Content-Location';
	const CONTENT_MD5	   = 'Content-MD5';
	const CONTENT_RANGE	   = 'Content-Range';
	const CONTENT_TYPE	   = 'Content-Type';
	const CONTENT_DISPOSITION = 'Content-Disposition';
	const EXPIRES		   = 'Expires';
	const LAST_MODIFIED	   = 'Last-Modified';
	const TITLE	   = 'Title';
	
	/**
	 * The Headers values available to be sent
	 * @var array
	 */
	protected $_headers = array(
		self::CACHE_CONTROL  => '',
		self::CONNECTION	 => '',
		self::DATE		   => '',
		self::PRAGMA		 => '',
		self::DATE		   => '',
		self::LOCATION	   => '',
		self::SERVER		 => '',
		self::CONTENT_LENGTH => '',
		self::CONTENT_LANGUAGE => '',
		self::CONTENT_TYPE   => 'text/html; charset=utf-8',
		self::CONTENT_DISPOSITION => '',
		self::EXPIRES		=> '',
		self::LAST_MODIFIED  => '',
		self::TITLE  => ''
	);
	
	/**
	 * The Response Body
	 * @var string
	 */
	protected $_body = array();
	
	/**
	 * Sets the Status Code
	 *
	 * must be a valid status
	 * @throw Gen_Http_Exception
	 * @return Gen_Http_Response
	 */
	public function setStatusCode($statusCode)
	{
		$statusCode = (int) $statusCode;
		if(isset(self::$_httpStatus[$statusCode])) {
			$this->_statusCode = $statusCode;
		} else {
			require_once('Gen/Http/Exception.php');
			throw new Gen_Http_Exception(
				"Try to set invalid status: $statusCode in Gen::Http::Response::setStatusCode()"
			);
		}
		return $this;
	}
	
	/**
	 * Returns the Status Code
	 *
	 * @return string
	 */
	public function getStatusCode()
	{
		return $this->_statusCode;
	}
	
	/** @alias for getStatusCode() */
	public function getStatus() { return $this->getStatusCode(); }
	
	/**
	 * Builds a Status-Line
	 *
	 * example: HTTP/1.1 404 Not Found
	 *
	 * @return string
	 */
	public function getStatusLine()
	{
		return self::VERSION . ' ' . $this->_statusCode . ' ' . self::$_httpStatus[$this->_statusCode];
	}
	
	public function setFile($file)
	{
		if (!file_exists($file)) {
			return $this->notFound('File not found : ' . $file);
		}
		$this->_file = $file;
		$fileName = basename($file);
		$this->setHeader(self::TITLE, $fileName);
		$this->setContentLength(filesize($file));
		return $this;
	}
	
	public function setJsonHeader($jsonHeader)
	{
		$this->_hasJsonHeader = $jsonHeader;
		return $this;
	}
	
	public function hasJsonHeader()
	{
		return $this->_hasJsonHeader;
	}
	
	public function setHeader($key, $value)
	{
		$key = (string) $key;
		$value = (string) $value;
		if (isset($this->_headers[$key])) {
			/** prevent header injections
			 * CRLF not authorized in Http1.1 specifications
			 */
			$value = str_replace("\n", '' ,$value);
			$value = str_replace("\r", '' ,$value);
			
			$this->_headers[$key] = $value;
		} else {
			require_once('Gen/Http/Exception.php');
			throw new Gen_Http_Exception(
				"Trying to set unsupported header: $key in Gen_Http_Response::setHeader()"
			);
		}
		return $this;
	}
	
	public function setContentType($contentType)
	{
		$this->setHeader(self::CONTENT_TYPE, (string) $contentType);
		return $this;
	}
	
	public function setContentLanguage($contentLanguage)
	{
		$this->setHeader(self::CONTENT_LANGUAGE, (string) $contentLanguage);
		return $this;
	}
	
	public function setContentLength($contentLength)
	{
		$this->setHeader(self::CONTENT_LENGTH, (string) $contentLength);
		return $this;
	}
	
	public function isSent()
	{
		return headers_sent();
	}
	
	public function sendHeaders()
	{
		if (!$this->isSent()) {
			/** send status line */
			if (!$this->hasJsonHeader()) {
				header($this->getStatusLine());
			} else {
				header(self::VERSION . ' 200 ' . self::$_httpStatus[200]);
			}
			foreach ($this->_headers as $key => $value) {
				$header = $value ? "$key: $value" : null;
				/** send header fields only if set */
				if ($header) {
					header($header);
				}
			}
		}
		return $this;
	}
	
	/**
	 * Gets the Response body
	 * @return array
	 */
	public function getBody()
	{
		return $this->_body;
	}
	
	public function setBody(array $body)
	{
		$this->_body = $body;
		return $this;
	}
	
	public function resetBody()
	{
		unset($this->_body);
		$this->_body = array();
		return $this;
	}
	
	public function appendBody($content)
	{
		$this->_body[] = $content;
		return $this;
	}
	
	public function prependBody($content)
	{
		array_unshift($this->_body, $content);
		return $this;
	}
	
	public function outputBody()
	{
		switch(true)
		{
			case $this->hasJsonHeader():
				echo json_encode(array(
					'status' => $this->getStatusLine(),
					'statusCode' => $this->getStatus(),
					'data' => $this->__toString()
				), JSON_FORCE_OBJECT);
				break;
			
			case $this->_file :
				$this->outputFile();
				break;
			
			default :
				echo $this->__toString();
				break;
		}
	}
	
	public function outputFile()
	{
		ob_clean();
		flush();
		readfile($this->_file);
	}
	
	public function send()
	{
		if ($this->hasJsonHeader()) {
			$this->setContentType('application/json; charset=utf-8');
		}
		
		$this->sendHeaders();
		$this->outputBody();
	}
	
	public function reset()
	{
		$this->resetBody();
		return $this;
	}
	
	public function redirect($url)
	{
		$this->setStatusCode(302);
		$this->setHeader(self::LOCATION, $url);
		return $this;
	}
	
	public function permanentRedirect($url)
	{
		$this->setStatusCode(301);
		$this->setHeader(self::LOCATION, $url);
		return $this;
	}
	
	public function error($message = null)
	{
		$this->reset();
		$this->setStatusCode(500); //500 Internal error
		$this->appendBody((string) $message);
		return $this;
	}
	
	public function notFound($message = null)
	{
		$this->reset();
		$this->setStatusCode(404); //404 Not Found
		$this->appendBody((string) $message);
		return $this;
	}
	
	public function unauthorized($message = null)
	{
		$this->reset();
		$this->setStatusCode(401); //401 Unauthorized Acces
		$this->appendBody((string) $message);
		return $this;
	}
	
	public function timeout($message = null)
	{
		$this->reset();
		$this->setStatusCode(408); //408 Request Time-out
		$this->appendBody((string) $message);
		return $this;
	}
	
	public function badRequest($message = null)
	{
		$this->reset();
		$this->setStatusCode(400); //400 Bad Request
		$this->appendBody((string) $message);
		return $this;
	}
	
	public function methodNotAllowed($message = null)
	{
		$this->reset();
		$this->setStatusCode(405); //405 Method not allowed
		$this->appendBody((string) $message);
		return $this;
	}
	
	public function __toString()
	{
		return implode("\n", $this->_body);
	}
}