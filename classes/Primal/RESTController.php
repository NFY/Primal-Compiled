<?php 
namespace Primal;

use \DB\User;
use \DB\APIAuthToken;

abstract class RESTController {
	public $output = array();
	public $error = array();

	protected $success = false;
	protected $method;
	protected $user;
	protected $route;
	protected $request;
	protected $response;
	
	public $auth_realm = "Primal API";
	public $supported = array();
	
	protected static $METHODS = array("GET","POST","PUT","DELETE","HEAD","OPTIONS","TRACE","CONNECT");
	
	function __construct($route=null) {
		//called directly as an API resource
		$this->route = $route;
		$this->request = Request::Singleton();
		$this->response = Response::Singleton();
		
		$this->processAuthentication();
		
		//if a method was defined in the request body, use that, otherwise get the http request method
		$method = strtoupper($request['method'] ?: $this->request->method);
		
		//if the method is the OPTIONS request, output available methods.
		if ($method == 'OPTIONS') {
			$this->response->header('Allow', implode(', ',$this->implementedMethods()));
			exit;
			
		//otherwise, check if the method is acceptable and is available on the calling class.
		} elseif (in_array($method, static::$METHODS) && is_callable(array($this, $method))) {
			$this->method = $method;
			
			//call the request function first, no matter what the method is.  
			//if anything other than null is returned, use it as the response success status and finish,
			//otherwise, call the function for the specific http method.
			if (($glob = $this->request($this->method)) !== null) {
				$this->success = $glob;
			} else {
				$this->success = (bool)$this->{$this->method}();
			}
			
		} else {
			$this->methodNotAllowed();
		}
		
		$this->respond();

	}
	
	private function implementedMethods() {
		$r = new \ReflectionClass($this);
		$methods = $r->getMethods(\ReflectionMethod::IS_PUBLIC);
		$origin = get_called_class();

		$found = array_fill_keys($this->supported, true);
		$found['OPTIONS'] = true;
		foreach ($methods as $method) $found[strtoupper($method->name)] = ($method->class === $origin);
		$found = array_keys(array_filter($found));

		return array_intersect(static::$METHODS, $found);
	}
	
	
	
	private function processAuthentication() {
		$user = null;
		$validate = true;
		if (is_array($this->request['auth'])) {
			if ($this->request['auth']['email']) {
				$user = User::LoadWithEmail($this->request['auth']['email']);
				if ($user === null || !$user->testPassword($this->request['auth']['password'])) {
					if ($user !== null && $user['hint']) $this->output['auth']['hint'] = $user['hint'];
					if ($validate) {
						$this->error['auth']['AUTH_INVALID_EMAIL_PASSWORD'] = 'Login Email does not exist or Password is incorrect';
						$this->error['auth']['email'] = true;
						$this->error['auth']['password'] = true;
					}
					$user = null;
				}
			} elseif ($this->request['auth']['token']) {
				$token = new APIAuthToken($this->request['auth']['token']);
				if (!$token->found) {
					if ($validate) {
						$this->error['auth']['AUTH_TOKEN_NOT_FOUND'] = 'Login token does not exist.';
						$this->error['auth']['token'] = true;
					}
				} elseif (strtotime($token['last_used']) < time()-60*30) {
					if ($validate) {
						$this->error['auth']['AUTH_TOKEN_EXPIRED'] = 'Login token has expired.';
						$this->error['auth']['token'] = true;
					}
				} elseif ($token['source_ip'] != $_SERVER["REMOTE_ADDR"]) {
					if ($validate) {
						$this->error['auth']['AUTH_TOKEN_IP_MISMATCH'] = 'Login token was created from a different computer.';
						$this->error['auth']['token'] = true;
					}
				} else {
					$user = new User($token['user_id']);
					if (!$user->found) {
						$user = null;
						if ($validate) {
							$this->error['auth']['AUTH_TOKEN_USER_NOT_FOUND'] = 'The user account for that login token no longer exists.';
							$this->error['auth']['token'] = true;
						}
					} else {
						$token->set('last_used','now'); //keep the token alive
					}
				}
			}
		} elseif (Visitor::LoggedIn()) {
			$user = Visitor::Current();
		} elseif ($_SERVER['PHP_AUTH_USER'] && $_SERVER['PHP_AUTH_PW']) {
			$user = User::LoadWithEmail($_SERVER['PHP_AUTH_USER']);
			if ($user === null || !$user->testPassword($_SERVER['PHP_AUTH_PW'])) {
				if ($validate) {
					$this->error['auth']['AUTH_INVALID_EMAIL_PASSWORD'] = 'Login Email does not exist or Password is incorrect';
					$this->error['auth']['email'] = true;
					$this->error['auth']['password'] = true;
				}
				$user = null;
			}
		}
		
		if ($user && $user['disabled']) {
			$this->error['auth']['AUTH_ACCOUNT_DISABLED'] = 'This user\'s account has been disabled.';
			$user = null;
		}
		
		$this->user = $user;
		$this->output['auth']['ok'] = ($user !== null);
	}
	
	protected function validUser() {
		if ($this->user === null) {
			$this->error['auth']['AUTH_REQUIRED'] = 'This function requires a valid user account.';
			if ($this->response) {
				$this->response->statusCode(HTTPStatus::UNAUTHORIZED);
				header("WWW-Authenticate: Basic realm=\"{$this->auth_realm}\"");
			}
			return false;
		}
		return true;
	}
	
	protected function respond() {
		$reply = array();
		$reply['method'] = $this->method;
		$reply['success'] = $this->success;
		if (!empty($this->error)) {
			$reply['errors'] = $this->error;
		} else {
			$reply['errors'] = 0;
		}
		if (!empty($this->output)) {
			$reply['content'] = $this->output;
		} else {
			$reply['content'] = null;
		}
		
		if ($this->response) $this->response->json($reply);
		return $reply['success'];
	}
	
	public function post()          {return false;}
	public function get()            {return false;}
	public function put()          {return false;}
	public function delete()          {return false;}
	public function request($method)  {return null;}
	
	protected function setResponseCode() {
		$this->response->statusCode($code);
	}
	
	protected function methodNotAllowed() {
		$this->setResponseCode(HTTPStatus::METHOD_NOT_ALLOWED);
		$this->response->header('Allow', implode(', ',$this->implementedMethods()));
		
	}
}
