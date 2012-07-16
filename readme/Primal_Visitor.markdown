#Primal Visitor

Created and Copyright 2011 by Jarvis Badgley, chiper at chipersoft dot com.

This repository contains the visitor authentication and session management classes for the [Primal PHP Framework](http://www.primalphp.com).  Unlike other parts of the Primal collection, Primal Visitor is not usable as a standalone library and will only function when combined with the Primal Boilerplate and Primal Database.

Primal Visitor consists of three classes and two default actions:

##Primary Classes

###Visitor

This is a global static class for interacting with active user in the page session.  It provides a set of functions for handling user login and for retrieving the current user from the session.  See the `login` action file for examples of how to work with this class.


###DB\User

This is the Primal Database Record object for managing entries in the users table.  This is the model you will interact with to create, retrieve and update user accounts.


###Primal\SaltedHash

This is a data model for hashing user passwords under randomly generated encryption salt, and validating login passwords against existing hashes.  See the `setPassword` and `testPassword` functions on the `User` object for examples of how to use this class outside of the Visitor system.



##Default Actions

###/login/ - /actions/login.php

Basic login page example.

###/logout/ - /actions/logout.php

Clears the current user and redirects to the site index

###/current/ - /actions/current.php

Example of how to fetch the current user.