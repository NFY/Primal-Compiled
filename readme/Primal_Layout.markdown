#Primal Layout

Created and Copyright 2011 by Jarvis Badgley, chiper at chipersoft dot com.

This repository contains the page layout classes for the [Primal PHP Framework](http://www.primalphp.com).  The contents of this repo are to be merged into the root of Primal Core.  Primal Layout is built on the philosophy that [PHP is already a template engine itself](http://codeangel.org/articles/simple-php-template-engine.html), and that adding an additional template engine on top of PHP is unnecessary.  This is not to say that Primal Layout encourages the mixing of presentation logic with business logic, but it leaves that separation up to you the developer.  If this does not flow with your own goals, we recommend not including Primal Layout with your Primal compilation.

Primal Layout consists of three primary classes, four derived classes, two replacement actions, and a few default content folders.

##Primary Classes

###Primal\Layout\Page

Primal layouts are built from the outside-in, beginning with the <html>, <head> and <body> tags.  The Page class provides that root foundation, letting you assemble it without ever having to write any HTML.  Code output begins when you call the `Page->start()` function and ends when you call `Page->stop()`.  Any tags you wish to add to the page head are done using a set of chainable functions.

```php	
$page = new Primal\Layout\Page();
$page->setTitle("This is my example page")
     ->addScript('prototype.js')
     ->addStyle('example.css')
     ->addClass('ExamplePage')
     ->start();

//Page Content Goes Here

$page->stop();
```

Page is designed to be subclassable, so that additional markup may be added within the body framework.  This is the purpose of the Primal\Layout\Page namespaced classes as described below.  By default, the Page constructor sets the page content type to UTF-8, defines a favicon (included in the module) and adds an HTML5 element shim for IE8 and below.

###Primal\Layout\WebPath

WebPath defines a set of static utility functions for working with local urls.  If you need to change the default location of scripts and stylesheets, this is the file you will need to alter.

###Primal\Layout\View

View is an ArrayAccess subclass built to simplify the process of rendering PHP templates.  Place your templates into the `/views/` folder and then reference them through this class.  The templates will be sandboxed within their own scope and only have access to the data that you pass to them as indexes on the view object.

```php
$view = new Primal\Layout\View('my.template'); //loads the my.template.php file from /views
$view['value1'] = 'foo';
$view['value2'] = 'bar';
$view->output();
```

View also allows rendering of the template to a string variable, for inclusion in emails for example.

##Derived Page Classes

###Primal\Layout\Page\Basic (extends Page)

The intent of Basic is to contain any site chrome that wraps your main page content.  This includes page headers and site navigation.  You would want to edit this class to contain your own page markup, as well as any scripts or css that you want to use site wide.  All internal pages would be extended from this class.

###Primal\Layout\Page\Confirmation (extends Basic)

Displays a basic page indicating that an action completed successfully.  This class takes your message title and body in its constructor and outputs the entire page immediately.  Pass false in the third constructor argument if you do not want this class to end page execution.

###Primal\Layout\Page\Error (extends Confirmation)

Same as Confirmation, but defaults the message title to "An Error Has Occurred" and adds the Error body class.

###Primal\Layout\Page\NotFound (extends Error)

Same as error, except responds with a default "Resource Not Found" message and sets the response type to 404 Not Found.

##Folders

The `Primal\Layout\Page` class expects javascript and stylesheet files to be stored in `/js` and `/css` respectively, so these folders are included in the module.  If you wish to use different default locations for these file types, the paths can be altered inside `Primal\Layout\WebPath`.

The `Primal\Layout\View` class expects the view templates it displays to be stored in the `/views` folder.  By default this folder contains the view for the revised index action.

##Actions

Primal Layout includes new placeholder files for the 404 and index actions.  These are to serve as examples for how the `Primal\Layout\Page` classes are used.