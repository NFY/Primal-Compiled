<?php 
namespace Primal;

/**
 * Path - Centralized location for accessing all internal code elements.
 *
 * @package Primal
 * @author Jarvis Badgley
 * @copyright 2008 - 2011 Jarvis Badgley
 */

class Path {
	
	//CODE FOLDERS
	const classes			= '/classes/';
	
	static $Root;
	
	public static function Root($more='')			{return static::$Root .$more;}
	public static function ForClass($name)			{return static::$Root . Path::classes . str_replace('\\', '/', $name).".php";}
	
}
Path::$Root = dirname(dirname(__DIR__));
