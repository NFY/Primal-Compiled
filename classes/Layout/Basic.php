<?php
namespace Layout;

use Primal\Visitor;

class Basic extends AssetPage {

	public $notifications;

	function __construct() {
		parent::__construct();
		$this->addClass('Basic');
	}

	function start() {
		parent::start();?>
	<div id="center">
		<header>

		</header>
<?php 	if (!empty($this->notifications)) foreach ($this->notifications as $notice) { ?>
		<div class="notification <?php echo $notice[1] ?>"><span><?php echo htmlentities($notice[0], ENT_QUOTES, 'utf-8') ?></span></div>
<?php 	} ?>
		<div id="content">
<?php
 	}

	function stop($exit=true) {?>
		</div>
	</div>
	<script type="text/javascript">
		$(function () {
			$('.notification.autohide').delay(2500).slideUp(1000);
		});
	</script>
<?php
		parent::stop($exit);
	}

}
