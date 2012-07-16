<?php 

$pass = Environment::Singleton()->getTestConsolePassword();

if (!is_string($pass)) die();

?><!DOCTYPE HTML>
<html lang="en">
<body>
	<form method="post" accept-charset="utf-8">
		<textarea name="exec" style="width:100%;height:100px;font-family:monospace;font-size:14px"><?php echo $_POST['exec'] ?></textarea>

		<div align="right"><input type="text" name="key" value="<?php echo $_POST['key'] ?>"><input type="submit" value="Execute"></div>
	</form>
	
<?php if ($_POST['exec'] && $_POST['key']===$pass) { ?>
	<div>Result:</div>
	<div style="padding:10px;border:1px solid gray;font-size:10px;">
		<pre><?php eval($_POST['exec'].';') ?></pre>
	</div>
<?php } ?>
</body>
</html>
