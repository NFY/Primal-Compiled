<?php 

namespace Cache;

/**
 * Primal\Cache\Cache - Base interface for a cache object.
 *
 * @package Primal.Cache
 * @author Jarvis Badgley
 * @copyright 2012 Jarvis Badgley
 */

interface Cache {
	
	function cacheIsValid();
	function cacheRetrieve();
	function cacheStore($value);
	
}

class CacheException extends \Exception {}
