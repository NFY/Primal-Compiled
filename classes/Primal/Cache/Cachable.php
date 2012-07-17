<?php 

namespace Primal\Cache;

/**
 * Primal\Cache\Cachable - Base implementation for a Cachable data store. 
 * This class intended to be Abstract, to be subclassed by cache implementations.
 * It is left implementable as a fallback class. See Primal\Cache\Query as an example.
 *
 * @package Primal.Cache
 * @author Jarvis Badgley
 * @copyright 2012 Jarvis Badgley
 */

class Cachable implements Cache {
	
	protected $key;
	protected $expires;
	
	function __construct($key, $expires = 300) {
		$this->key = $key;
		$this->expires = $expires;
	}
	
	static function Stored($key, $expires = 300) {
		return new static($key, $expires);
	}
	
	function result($subroutine) {

		if ($this->cacheIsValid()) {
			return $this->cacheRetrieve();	
		}
		
		$result = $subroutine();
		$this->cacheStore($result);

		return $result;
	}
	
/**

*/

	protected function cacheIsValid() {
		return false;
	}

	protected function cacheRetrieve() {
		return null;
	}

	protected function cacheStore($value) {
		//does nothing
	}

	
}

