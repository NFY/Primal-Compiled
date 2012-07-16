#Primal.Autoloader

Created and Copyright 2012 by Jarvis Badgley, chiper at chipersoft dot com.

Primal.Autoloader is a standalone micro-library designed to make setting up class autoloading in a PHP app as painless and conflict free as possible.

[Primal](http://www.primalphp.com) is a collection of independent micro-libraries that collectively form a [LAMP](http://en.wikipedia.org/wiki/LAMP_\(software_bundle\)) development framework, but is not required to use Primal.Autoloader in your own projects.

##Requirements
- PHP 5.3 or later

##Usage

Include Autoloader.php into the root of your PHP project; By default the class is namespaced as Primal\Autoloader and this git repo is structured as if the root of the repo were the root of the project.  Feel free to move and/or namespace the class as you desire.

The autoloader can be initialized either as a saved instance:

	$autoload = new Primal\Autoloader();
	$autoload->addPath('my/classes/folder');
	$autoload->addPath('another/classes/folder);

Or directly as a chained method:

	Primal\Autoloader::Init()
		->addPath('my/classes/folder')
		->addPath('another/classes/folder);
		
Note that the above method is not recommended for long-running scripts that may trigger garbage collection.

##Functions

###`new Autoload( [bool $use_includes] )` or `Autoload::Init( [bool $use_includes] )`

Both the class constructor and Autoload::Init() take a single optional parameter that defines if Autoloader should also search in the PHP Include Path.  If omitted the defaults to true.

###`addPath( string $path )`

Adds a directory to the search path.

###`addDirect( string $name, string $path )`

Directly defines the location of a named class, skipping the path searching algorithm.

###`setFormatter( function $callback )`

Replaces the function used for computing class paths.  By default classes are expected to be in `NamespaceA/ClassName.php` format, relative to the search paths.  This can be overridden like so:

	//Always searches for paths in format of class.classname.php, stripping namespacing.
	$autoload->setFormatter(function ($input) {return 'class.' . array_pop(strtolower(explode('\\', $input))) . '.php';});

Note that only one formatter may be used per autoloader, but multiple autoloaders may be initialized if your project supports more than one naming scheme.

##Legal

Primal is released under an MIT license.  No attribution is required and you are expected to make alterations.  For details see the enclosed LICENSE file.

