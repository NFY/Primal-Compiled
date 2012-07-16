<?php 

use Primal\Visitor;

if (Visitor::LoggedIn()) {
	//Visitor::Current returns a DB\User object. Calling export to get the raw array for output.
	$user = Visitor::Current()->export();
} else {
	$user = "No user logged in.";
}

?><!DOCTYPE HTML>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Current User</title>
</head>
<body>

<pre><?php print_r($user); ?></pre>

</body>
</html>