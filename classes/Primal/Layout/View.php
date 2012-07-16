<?php
namespace Primal\Layout;

/**
 * View - Basic class for rendering the contents of a view file with a set of predefined variables
 * The view class is referenced like so:
 * 		$v = new View('name');
 *		$v['variable'] = 'some value';
 *		$contents = $v->render();
 * Within the render() function, $v['variable'] becomes $variable to the view file.
 * @package Primal
 * @author Jarvis Badgley
 * @copyright 2008 - 2011 Jarvis Badgley
 */


class View extends \ArrayObject {
	static $VIEWS_PATH = null;		//absolute path (server side) to the views folder
	static $VIEWS_EXTENSION = null; //file extension of templates
	
	private $vars = array();
	private $file;

	function __construct($name, $views_folder = null) {
		// if no views_folder is provided, we need to find one.  If a path is defined in static::$VIEWS_PATH we use that, 
		// otherwise assume the /views/ folder at the same level as the classes folder given our released folder structure
		if ($views_folder === null) {
			if (static::$VIEWS_PATH === null) {
				$views_folder = dirname(dirname(dirname(__DIR__))) . '/views/';
			} else {
				$views_folder = static::$VIEWS_PATH;
			}
		}
		
		if (static::$VIEWS_EXTENSION === null) {
			if (file_exists("{$views_folder}{$name}.php")) 
				$this->file = "{$views_folder}{$name}.php";
			elseif (file_exists("{$views_folder}{$name}.html"))
				$this->file = "{$views_folder}{$name}.html";
			else throw new \Exception("The defined View does not exist: $name");
		} else {
			if (file_exists("{$views_folder}{$name}.{static::$VIEWS_EXTENSION}"))
				$this->file = "{$views_folder}{$name}.{static::$VIEWS_EXTENSION}";
			else throw new \Exception("The defined View does not exist: $name");
		}
	}

	/**
	 * Exports the view variables as a named array.
	 *
	 * @return array
	 */	
	public function export() {return $this->getArrayCopy();}

	/**
	 * Imports a named array into the view variables
	 *
	 * @param array $in
	 */	
	public function import($in) {
		$this->exchangeArray( array_merge($this->export(), $in) );
	}

 
	/**
	 * Renders the view in the output buffer and returns the result as a string
	 *
	 * @return string
	 */	
	public function render() {
		extract($this->export());
		ob_start();
		include($this->file);
		return ob_get_clean();
	}
	
	/**
	 * Renders the view directly to the viewer
	 *
	 * @return string
	 */	
	public function output() {
		extract($this->export());
		include($this->file);
	}
	
	static function QuickDraw($name, $vars=null) {
		$o = new self($name);
		if ($vars) $o->import($vars);
		$o->output();
	}
}
