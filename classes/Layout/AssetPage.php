<?php 

namespace Layout;
use \RecursiveIteratorIterator, \RecursiveArrayIterator, \Exception;

class AssetPage extends \Primal\Layout\Page {

	function __construct() {
		parent::__construct();

		$this->import('jquery');

		$this->addStyle('global.css');
	}


	static $importables = array(
		'jquery'				=>'script:lib/jquery-1.7.2.min.js',
		'jquery effects'		=>'script:lib/jquery-ui-1.8.20.effects.min.js',
		'jquery ui core'		=>array('script:lib/jquery-ui-1.8.20.core.min.js', 'style:lib/jquery-ui-1.8.20.css'),
		'jquery interactions'	=>array('jquery ui core', 'script:lib/jquery-ui-1.8.20.interactions.min.js'),
		'jquery widgets'		=>array('jquery ui core', 'script:lib/jquery-ui-1.8.20.widgets.min.js'),
	);
	
	var $imported = array();
	
	function import() {
		$items = array();
		foreach(new RecursiveIteratorIterator(new RecursiveArrayIterator(func_get_args())) as $k=>$v) $items[] = $v;
		foreach ($items as $item) {
			$split = explode(':', $item);
			switch ($split[0]) {
			case 'script':
				$this->addScript($split[1]);
				break;
			case 'style':
				$this->addStyle($split[1]);
				break;
			default:
				$item = $split[0];
				if (!isset(self::$importables[$item])) throw new Exception("Import item does not exist: {$item}");
				elseif (!isset($this->imported[$item])) {
					$this->imported[$item] = true;
					$this->import(self::$importables[$item]);
				}
			}
		}
	}
}

