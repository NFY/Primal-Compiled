<?php 

namespace Primal;

class Launcher extends Routing\CachedRouter {
	
		
	function __construct() {
		$this->pairAllArguments()->indexPairedArguments();
		parent::__construct(Path::Root('/routes'), true);
	}
	
	
	/**
	 * Runs the current route
	 *
	 * @return Router Current instance, for chaining
	 */
	protected function run() {
		if (!file_exists($this->route_file)) throw new Exception('Route file does not exist: '.$this->route_file);
		
		$closure = function ($route, $arguments, $response, $session) {
			include $route->route_file;
		};
		
		$closure($this, $this->arguments, Response::Singleton(), Session::Singleton());		
		
		return $this;
	}
	
	
	
	public function startSession($session_id=null) {
		if (isset($_SESSION['Primal'])) return;
		
		if ($session_id !== null) session_id($session_id);
		elseif (isset($_REQUEST["PHPSESSID"])) session_id($_REQUEST["PHPSESSID"]);
		//if a session ID is passed in the request, use it.  This is to get around cookie issues with requests from inside Flash.
		
		session_start();
		if (!isset($_SESSION['Primal'])) $_SESSION['Primal'] = array(); //PHP offers no way of verifying that a session has been created, so we make the Primal array as a verify check.
		
	}
	
	
	public function __isset($param) {
        return isset($this->options[$param]);
    }
 
    public function __get($param) {
        return isset($this->options[$param]) ? $this->options[$param] : null;
    }
 
    public function __set($param, $value) {
        $this->options[$param] = $value;
    }
 
    public function __unset($param) {
        unset($this->options[$param]);
    }
	
}
