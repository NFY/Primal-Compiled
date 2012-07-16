<?php 

namespace Primal\Routing;

class CachedRouter extends DeepRouter {
	
	protected $apc_key = false;
	protected $cache_duration = 60;
	
	/**
	 * Sets the search path for finding route files
	 *
	 * @param string $path 
	 * @return Karte Current instance, for chaining
	 * @author Jarvis Badgley
	 */
	public function setRoutesPath($path) {
		Router::setRoutesPath($path);
		if (function_exists('apc_exists')) {
			$this->apc_key = $_SERVER['HTTP_HOST'].':PrimalRoutesCache:'.$path;
			$this->routes = apc_fetch($this->apc_key);
		}
		if ($this->routes === false) {
			$this->loadRoutes();
		}
		return $this;
	}
	
	
	protected function loadRoutes() {
		parent::loadRoutes();
		
		if ($this->apc_key) {
			apc_store($this->apc_key, $this->routes, $this->cache_duration);
		}
	}
	
	
}
