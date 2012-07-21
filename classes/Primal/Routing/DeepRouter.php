<?php 

namespace Primal\Routing;

class DeepRouter extends Router {
	
	protected $routes = false;
		
		
	/**
	 * Sets the search path for finding route files
	 *
	 * @param string $path 
	 * @return Karte Current instance, for chaining
	 * @author Jarvis Badgley
	 */
	function setRoutesPath($path) {
		parent::setRoutesPath($path);
		$this->loadRoutes();
		return $this;
	}
	
		
	protected function loadRoutes() {
		$this->routes = array();
		
		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->routes_path), \RecursiveIteratorIterator::SELF_FIRST);
		foreach ($iterator as $file) {
			if (in_array($file->getExtension(), array('php','html'))) {
				$path = str_replace($this->routes_path, '', $file->getPath());
				$path = str_replace('/','.',$path);
				$path = $path . '.' . $file->getBasename('.'.$file->getExtension());
				$path = substr($path,1);			
				
				$this->routes[ $path ] = (string)$file;
			}
			
		}
		
	}
	
	/**
	 * Internal function to test if a route exists.
	 *
	 * @param string $name Name of the route
	 * @return boolean
	 */
	protected function checkRoute($name) {
		return isset($this->routes[$name]) ? $this->routes[$name] : false;
	}
	
	/**
	 * Reroutes the request to the specified route.
	 *
	 * @param string $new_route Name of the new route
	 * @return Karte Current instance, for chaining
	 */
	public function reroute($new_route = null) {

		if ($new_route !== null) {
			if ($found = $this->checkRoute($new_route)) {
				$this->route_name = $new_route;
				$this->route_file = $found;
			} else {
				throw new Exception('Route could not be found: '.$new_route);
			}
		}

		return $this->run();
		
	}
	
	
}
