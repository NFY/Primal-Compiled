<?php 
namespace Layout;

class Confirmation extends Basic {
	function __construct($title, $message, $exit=true) {
		$this->addClass('Confirmation');
		parent::__construct();
		parent::start();?>
			<h1><?php echo $title ?></h1>
			<p><?php echo $message ?></p>
<?php 	parent::stop($exit);
	}
}