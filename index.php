<?php
namespace Primal;

/**
 * Primal Foundation Framework - Core
 * This is the base execution file for Primal.  The .htaccess file will take any request for a file
 * that does not actually exist and route it through this php file.  This file may also be included in any
 * cron or shell script that needs access to the autoloading, database or path libraries.
 *
 * @package Primal
 * @author Jarvis Badgley
 * @copyright 2008 - 2012 Jarvis Badgley
 */

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require(__DIR__.'/classes/Primal/Path.php');
Path::$Root = __DIR__;

require( Path::ForClass('Primal\\Autoloader') );
Autoloader::Init()
	->addPath(Path::Root(Path::classes))
	->addDirect('Primal\\Routing\\Router'       ,Path::ForClass('Primal\\Routing\\Router'))
	->addDirect('Primal\\Routing\\CachedRouter' ,Path::ForClass('Primal\\Routing\\CachedRouter'))
	->addDirect('Primal\\Launcher'              ,Path::ForClass('Primal\\Launcher'))
	->addDirect('Primal\\Response'              ,Path::ForClass('Primal\\Response'))
	->addDirect('Primal\\Request'               ,Path::ForClass('Primal\\Request'))
	->addDirect('Primal\\Session'               ,Path::ForClass('Primal\\Session'))
;

//make php shutup about date timezones.
date_default_timezone_set(defined('DATE_TIMEZONE')?DATE_TIMEZONE:'America/Los_Angeles');

if (file_exists(Path::Root('/config.php'))) include(Path::Root('/config.php'));

//REQUEST_URI will only be set if main.php was triggered by apache
//We only parse a controller if this value is set, so that cron jobs can load the framework as well
if ($_SERVER['REQUEST_URI']) {
	new Launcher();
}


