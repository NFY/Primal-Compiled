<?php 

namespace Primal\Cache;

/**
 * Primal\Cache\CachableInFile - Subclass for storing Cachable data in files on the local system.
 *
 * @package Primal.Cache
 * @author Jarvis Badgley
 * @copyright 2012 Jarvis Badgley
 */

class CachableInFile extends Cachable implements Cache {

	public static $CacheFolder;

	private $path;

	function __construct($key, $expires = 300) {
		parent::__construct($key, $expires);

		if (!static::$CacheFolder) {
			if (class_exists('\\Primal\\Path')) {
				static::$CacheFolder = Primal\Path::Root("/cache/");
			} else {
				throw new CacheException("No cache folder path has been defined for CachableInFile.");
			}
		}
		
		$this->path = realpath($path.'/'.sha1($this->key).".cachable");
	}
	

/**

*/

	protected function cacheIsValid() {
		return file_exists($this->path) && time() - filemtime($this->path) < $this->expires;
	}
	
	protected function cacheRetrieve() {
		return unserialize(file_get_contents($this->path));
	}
	
	protected function cacheStore($value) {
		file_put_contents($this->path, serialize($value));
	}
	
	
}