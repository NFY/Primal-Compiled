<?php 

namespace Primal\Cache;

/**
 * Primal\Cache\Query - Caching extension to Primal\Database\Query
 * This class is dependent upon the Primal.Database library.  It is included in Primal.Cache as an example of how to use
 * a Cachable instance.
 *
 * @package Primal.Cache
 * @author Jarvis Badgley
 * @copyright 2012 Jarvis Badgley
 */

class Query extends \Primal\Database\Query {
	
	protected $_cache;
	protected $_cacheExpires;
	
	public function cache($cache = "\\Cache\\Cachable", $duration = 300) {
		
		$this->_cache = $cache;
		$this->_cacheExpires = (int)$duration;
		
		return $this;
	}
	
	
	public function select($format=self::RETURN_FULL) {
		
		$queryKey = $this->buildSelect(true);

		$request_format = $format;
		
		if (!is_integer($format)) {
			$request_format = self::RETURN_FULL;
		}
		
		$cache = new $this->cache($queryKey." -- {$request_format}", $this->_cacheExpires);

		if ($cache->cacheIsValid()) {
			$result = $cache->cacheRetrieve();
		} else {
			$result = parent::select($request_format);
			$cache->cacheStore($result);
		}
				
		if (!is_integer($format) && (is_object($format) || class_exists($format))) {		
			return array_map(function ($item) use ($format) {
				return new $format($item);
			}, $result);
		} else {
			return $result;
		}
	}
	
	
	public function count() {
		$queryKey = $this->buildCount(true);

		$cache = new $this->cache($queryKey." -- c", $this->_cacheExpires);

		if ($cache->cacheIsValid()) {
			$result = $cache->cacheRetrieve();
		} else {
			$cache->cacheStore(parent::count());
		}

		return $result;
	}
	

	
}
