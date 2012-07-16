<?php 
namespace Primal;

class Autoloader {
	
	/**
	 * Directories to search in for classes
	 *
	 * @var array
	 **/
	protected $paths = array();
	
	/**
	 * Named array containing direct paths to specific class files.  Makes for slightly faster lookups.
	 *
	 * @var array
	 **/
	protected $quicklook = array();
	
	/**
	 * Function used to find format the path of the class
	 *
	 * @var function
	 */
	protected $formatter;
	
	/**
	 * Include the PHP include path when searching for classes
	 *
	 * @var boolean
	 */
	protected $search_include_path = true;
	
	/**
	 * Adds a local file path to the search list
	 *
	 * @param string
	 * @return $this
	 **/
	public function addPath($path) {
		$path = realpath($path).'/';
		if ($path) {
			$this->paths[] = $path;
		}
		return $this;
	}
	
	/**
	 * Stores the path of a specific class for faster recall
	 *
	 * @param string $name Full class name, including namespace (remember to escape backslashes)
	 * @param string $path Path to the class file.
	 * @return $this
	 * @author Jarvis Badgley
	 */
	public function addDirect($name, $path) {
		$path = realpath($path);
		$this->quicklook[$name] = $path;
		return $this;
	}
	
	/**
	 * Replaces the default class formatting function with a custom callback, allowing for an alternate naming scheme.
	 *
	 * @param function $callback 
	 * @return $this
	 * @author Jarvis Badgley
	 */
	public function setFormatter($callback) {
		if (is_callable($callback)) $this->formatter = $callback;
		else throw new \Exception('Passed callback is not a callable function.');
		
		return $this;
	}
	
	/**
	 * Attempts to load the named class.  First checks the quicklook cache, then scans the defined directories, and finally searches the include paths
	 *
	 * @param string
	 * @return void
	 * @static
	 **/
	public function load($class) {
		$formatter = $this->formatter;
		
		//check quickloader cache
		if (isset($this->quicklook[$class])) {
			$path = $this->quicklook[$class];
			if (is_file($path)) {
				require_once $path;
				return;
			}
		}
		
		//check assigned paths
		$classPath = $formatter($class); // Do whatever logic here
		foreach ($this->paths as $path) {
			if (is_file($path . $classPath)) {
				require_once $path . $classPath;
				return;
			}
		}
		
		if ($this->search_include_path) {
			//check in the php include directories
			foreach (explode(':',ini_get('include_path')) as $path) if ($path && $path[0]!='.') {
				$path = realpath("$path/$classPath");
				if (is_file($path)) {
					require_once $path;
					return;
				}			
			}
		}
	}
	
	/**
	 * Class construct, initializes the autoload hook.
	 *
	 * @author Jarvis Badgley
	 */
	function __construct($use_include = true) {
		$this->search_include_path = $use_include;
		
		$this->formatter = function ($name) {
			return str_replace('\\', DIRECTORY_SEPARATOR, ltrim($name, '\\')).".php";
		};
		
		spl_autoload_register(array($this,'load'));
	}
	
	/**
	 * Convenience function to get a new autoloader for chaining without assignment
	 *
	 * @return void
	 * @author Jarvis Badgley
	 */
	static function Init($use_include = true) {
		return new static($use_include);
	}
}



