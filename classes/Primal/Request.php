<?php 
namespace Primal;
use \ArrayObject;

/**
 * Primal Request Object
 * Some functions in this class are based on or directly copied from https://github.com/chriso/klein.php
 *
 * @package Primal.Routing
 */

class Request extends ArrayObject {

	protected static $requestData = false;
		
	public function __construct() {
		if (self::$requestData === false) {
		
			//process incomming data based on the request content type
			switch ($this->contentType) {
				//process the incomming values 
		
				//JSON content directly sent
				case 'application/json':
				case 'text/json':
					$data = json_decode(file_get_contents('php://input'), true);
					if (!is_array($data)) $data = array();
					$data = array_merge($_GET, $data);
					break;
		
				case 'application/x-www-form-urlencoded':
				case 'multipart/form-data':
				default: //everything else
	
					$_PUT = array();
					if ($this->method == 'PUT') {  
					    parse_str(file_get_contents('php://input'), $_PUT);  
					}

					//COLLECT ALL TRANSMITTED DATA INTO A LOCAL VALUE
					$data = array_merge($_GET, $_POST, $_PUT);
					if ($data['json']) {
						$json = json_decode($data['json'], true);
						unset($data['json']);
						if ($json) $data = array_merge($data, $json);
					}
					break;
			}
			
			self::$requestData = $data;
		}
		
		$this->exchangeArray(self::$requestData);
	}	


	function __get($name) {
		switch (strtolower($name)) {
			case 'data':
				return self::$requestData;
			case 'secure':
				return isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS'])==='on';
			case 'domain':
				return $_SERVER['HTTP_HOST'];
			case 'url':
			case 'uri':
				return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
			case 'path':
				return parse_url($this->url, PHP_URL_PATH);
			case 'query':
				return parse_url($this->url, PHP_URL_QUERY);
			case 'referrer':
			case 'referer':
				return $_SERVER["HTTP_REFERER"];
			case 'ip':
				return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
			case 'agent':
			case 'useragent':
			case 'user_agent':
				return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
			case 'browser':
				$tests = array(
					'opera'		=> (stripos($_SERVER['HTTP_USER_AGENT'], 'Opera'	)!==FALSE),
					'chrome'	=> (stripos($_SERVER['HTTP_USER_AGENT'], 'Chrome'	)!==FALSE),
					'safari'	=> (stripos($_SERVER['HTTP_USER_AGENT'], 'Safari'	)!==FALSE),
					'webkit'	=> (stripos($_SERVER['HTTP_USER_AGENT'], 'WebKit'	)!==FALSE),
					'firefox'	=> (stripos($_SERVER['HTTP_USER_AGENT'], 'Firefox'	)!==FALSE),
					'ipad'		=> (stripos($_SERVER['HTTP_USER_AGENT'], 'iPad'		)!==FALSE),
					'ipodtouch'	=> (stripos($_SERVER['HTTP_USER_AGENT'], 'iPod'		)!==FALSE),
					'iphone'	=> (stripos($_SERVER['HTTP_USER_AGENT'], 'iPhone'	)!==FALSE),
				);

				//now match IE and set by major version
				$matches = array();
				preg_match('/MSIE ([^;]+);/i', $_SERVER['HTTP_USER_AGENT'], $matches );
				$ie_version = floor($matches[1]);
				if ($ie_version) $tests['ie'.$ie_version] = true;
				
				return array_shift(array_filter($tests));
			case 'mobile':
				$tests = array(
					(stripos($_SERVER['HTTP_USER_AGENT'], 'iPad'		)!==FALSE),
					(stripos($_SERVER['HTTP_USER_AGENT'], 'iPod'		)!==FALSE),
					(stripos($_SERVER['HTTP_USER_AGENT'], 'iPhone'		)!==FALSE),
					(stripos($_SERVER['HTTP_USER_AGENT'], 'Android'		)!==FALSE),
					(stripos($_SERVER['HTTP_USER_AGENT'], 'iOS'			)!==FALSE),
					(stripos($_SERVER['HTTP_USER_AGENT'], 'Blackberry'	)!==FALSE),
				);
				return max($tests);
			
			case 'content_type':
			case 'contenttype':
				if (!isset($_SERVER['CONTENT_TYPE'])) return false;
				$type = array_shift(explode(';',$_SERVER['CONTENT_TYPE']));
		        if (null !== $is) {
		            return strcasecmp($type, $is) === 0;
		        }
		        return $type;
			case 'method':
				$method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';

		        //For legacy servers, override the HTTP method with the X-HTTP-Method-Override
		        //header or _method parameter
		        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
		            $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
		        } else if (isset($_REQUEST['_method'])) {
		            $method = $_REQUEST['_method'];
		        }
		        
				return strtoupper($method);
			
		}
	}
	
    protected static $singleton;
	static function Singleton() {
		return (!static::$singleton) ? static::$singleton = new static() : static::$singleton;
	}
	

    /**
     * Gets a request header
     *
     * @param string $key Header name
     * @param string $default Optional value to use if header is absent
     * @return string
     */
    public function header($key, $default = null) {
        $key = 'HTTP_' . strtoupper(str_replace('-','_', $key));
        return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
    }


    /**
     * Gets a request cookie
     *
     * @param string $key Cookie name
     * @param string $default Optional value to use if the cookie is absent.
     * @return string
     */
    public function cookie($key, $default = null) {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
    }



}
