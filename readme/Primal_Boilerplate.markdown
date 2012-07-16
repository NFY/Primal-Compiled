#Primal Boilerplate

Created and Copyright 2012 by Jarvis Badgley, chiper at chipersoft dot com.

Primal Boilerplate is a default site structure designed to combine the Primal libraries under a single set of conventions.  This repo is the starting point for a Primal based site and must be mixed with Primal.Autoloader and Primal.Routing to be of any use.

[Primal](http://www.primalphp.com) is a collection of independent micro-libraries that collectively form a [LAMP](http://en.wikipedia.org/wiki/LAMP_\(software_bundle\)) development framework.

##Requirements
- PHP 5.3 or later
  - No extra PEAR modules are required.  Primal is designed to work in the easiest of environments
  - Primal does expect magic_quotes and register_globals to be disabled. The .htaccess file attempts to override these values to ensure this.
- Apache 2.x with mod_rewrite
  - Apache must be configured to allow mod_rewrite directives in .htaccess files.

##Folder Structure
From the base of the Primal installation:

- *.htaccess* - This is the Apache configuration file that includes the mod_rewrite directives for handling page requests.  This file *MUST* be installed for Primal to work.  Linux and the Mac OS may hide this file from view, so it may be necessary to check using the terminal to see if this file is present.

- *classes* - This is the root location of all namespacing for the purposes of autoloading. Every class used on your site goes into this folder.

	- *classes/Primal* - The only namespace you can't delete.  Contains all the classes critical for the operation of Primal.

- *LICENSE* - The distribution license for the Primal framework.

- *main.php* - This is the core execution file for Primal.  All web requests pass through main.php before executing the related controller file.  For cron jobs, including this file at the beginning of the script will allow the script to execute just as a controller would.

- *README.markdown* - This file.
		
##Built in Classes

- `Primal\Path` - All file requests in PHP are done using absolute paths to avoid security issues. The Path class provides the helper functions for producing those paths.

##Legal

All Primal libraries are released under an MIT license.  No attribution is required and you are expected to make alterations.  For details see the enclosed LICENSE file.

