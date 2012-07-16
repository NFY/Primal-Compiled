<?php 
namespace Primal;

/**
 * Primal Request Object
 * Some functions in this class are based on https://github.com/chriso/klein.php
 *
 * @package Primal
 */

class Response {
	private static $messages = array(
		// Informational
		100=>'100 Continue',
		101=>'101 Switching Protocols',
		
		// Successful
		200=>'200 OK',
		201=>'201 Created',
		202=>'202 Accepted',
		203=>'203 Non-Authoritative Information',
		204=>'204 No Content',
		205=>'205 Reset Content',
		206=>'206 Partial Content',
		
		// Redirection
		300=>'300 Multiple Choices',
		301=>'301 Moved Permanently',
		302=>'302 Found',
		303=>'303 See Other',
		304=>'304 Not Modified',
		305=>'305 Use Proxy',
		306=>'306 (Unused)',
		307=>'307 Temporary Redirect',
		
		// Client Error
		400=>'400 Bad Request',
		401=>'401 Unauthorized',
		402=>'402 Payment Required',
		403=>'403 Forbidden',
		404=>'404 Not Found',
		405=>'405 Method Not Allowed',
		406=>'406 Not Acceptable',
		407=>'407 Proxy Authentication Required',
		408=>'408 Request Timeout',
		409=>'409 Conflict',
		410=>'410 Gone',
		411=>'411 Length Required',
		412=>'412 Precondition Failed',
		413=>'413 Request Entity Too Large',
		414=>'414 Request-URI Too Long',
		415=>'415 Unsupported Media Type',
		416=>'416 Requested Range Not Satisfiable',
		417=>'417 Expectation Failed',
		418=>'418 I\'m a teapot',
		
		// Server Error
		500=>'500 Internal Server Error',
		501=>'501 Not Implemented',
		502=>'502 Bad Gateway',
		503=>'503 Service Unavailable',
		504=>'504 Gateway Timeout',
		505=>'505 HTTP Version Not Supported'
	);
	
	protected static $singleton;
	static function Singleton() {
		return (!static::$singleton) ? static::$singleton = new static() : static::$singleton;
	}
    
	

	/**
	 * Checks if the request is secured (HTTPS) and redirects if it isn't.
	 *
	 * @param boolean $required 
	 * @return boolean
	 */
    public function secure() {
		if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS'])==='on') return;
		header("Location: https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
    }


	/**
	 * Sets a response header
	 *
	 * @param string $key Header name
	 * @param string $value Header value
	 * @return void
	 */
	public function header($key, $value = '') {
	    $key = str_replace(' ', '-', ucwords(str_replace('-', ' ', $key)));
	    header("$key: $value");
	}

	/**
	 * Sets a response cookie
	 *
	 * @param string $key Cookie name
	 * @param string $value Cookie contents
	 * @param string $expiry Defaults to 30 days.
	 * @param string $path Defaults to root of the domain
	 * @param string $domain Defaults to the called domain and all subdomains
	 * @param string $secure Tells the visiting browser to only transmit this cookie over SSL
	 * @param string $httponly Tells the visiting browser to only transmit this cookie on page requests and not AJAX.
	 * @return boolean
	 */
	public function cookie($key, $value = '', $expiry = null, $path = '/', $domain = null, $secure = false, $httponly = false) {
	    if ($expiry === null) {
	        $expiry = time() + (3600 * 24 * 30);
	    } elseif ($expiry instanceof \DateTime) {
			$expiry = $expiry->getTimestamp();
		} elseif (is_string($expiry)) {
			$expiry = strtotime($expiry);
		}
	    return setcookie($key, $value, $expiry, $path, $domain, $secure, $httponly);
	}
	
	/**
	 * Removes a response cookies
	 *
	 * @param string $key 
	 * @return void
	 */
	public function unsetCookie($key) {
		$this->cookie($key, '', 0);
	}

	/**
	 * Tell the browser not to cache the response
	 *
	 * @return void
	 */
    public function noCache() {
        header("Pragma: no-cache");
        header('Cache-Control: no-store, no-cache');
    }

	/**
	 * Gets/Sets the HTTP status header to match the passed code. Recommended using the HTTPStatus constants to define the code.
	 *
	 * @param integer
	 * @return void
	 **/
	function statusCode($code=null) {
		static $current_status;
		
		if ($code) {
            $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
			$current_status = $code;
			header($protocol. ' ' . static::$messages[$code]);
		}
		return $current_status?:200;
	}


	/**
	 * Sets content type to json data and sends the passed array as the body content.
	 *
	 * @param mixed The array or object to send.
	 * @return void
	 **/
	function json($object, $callback = null) {
		header('Content-type: application/json');
		$json = json_encode($object);
		if ($callback) echo ";$callback($json);";
		else echo $json;
		exit;
	}
	
	
	/**
	 * Sends a location header and terminates the controller, redirecting the browser to a new location.
	 *
	 * @param string URL to redirect to. Defaults to the current url if omitted.
	 * @return void
	 **/
	function redirect($url=".", $code = 302) {
		$this->statusCode($code);
		if ($url=='.') $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
		header("Location: {$url}");
		exit;
	}
	
}


