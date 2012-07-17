<?php 

namespace Primal\Cache;

/**
 * Primal\Cache\CachableInAPC - Subclass for storing Cachable data in the APC key/value store
 *
 * @package Primal.Cache
 * @author Jarvis Badgley
 * @copyright 2012 Jarvis Badgley
 */

class CachableInFile extends Cachable implements Cache {

	private $path;

	function __construct($key, $expires = 300) {
		parent::__construct($key, $expires);

		if (function_exists('apc_exists')) {
			throw new CacheException("APC does not appear to be installed and is not available for caching");
		}
		
		$this->path =  "{$_SERVER['HTTP_HOST']}:CachableInAPC:{$key}";
	}
	

/**

*/

	protected function cacheIsValid() {
		return apc_exists($this->path);
	}
	
	protected function cacheRetrieve() {
		return unserialize(apc_fetch($this->path));
	}
	
	protected function cacheStore($value) {
		apc_store($this->path, serialize($value), $this->expires);
	}
	
	
}