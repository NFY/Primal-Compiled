<?php
namespace Primal\Layout;

class Page {
	var $title = '';
	private $bodyattributes = array();
	private $bodyclasses = array();
	private $metatags = array();
	private $metaequiv = array();
	private $headcontent = array();
	private $footcontent = array();
	
	protected $scriptroot = '/js/';
	protected $styleroot = '/css/';

	function __construct() {
		$this->addMetaEquiv('Content-Type', 'text/html; charset=utf-8');
				
		//Favicon
		$this->addRawHeadHTML('<link rel="shortcut icon" href="/favicon.png">');
		
		//HTML5 polyfill
		$this->addRawHeadHTML('<!--[if lt IE 9]><script>var e = ("abbr,article,aside,audio,canvas,datalist,details,figure,footer,header,hgroup,mark,menu,meter,nav,output,progress,section,time,video").split(\',\');for (var i = 0; i < e.length; i++) document.createElement(e[i]);</script><![endif]-->');
	}

	/**
	 * Defines the page title.
	 *
	 * @param string $title 
	 * @return Page
	 */
	function setTitle($title) {
		static $title_index = -1;
		
		$titlehtml =  '<title>'.htmlentities($title).'</title>';
		
		//if no title has been set yet, append the title tag to the head and remember the position
		//if it has been set (and thus we know the position) then replace that position.
		if ($title_index === -1) {
			$this->headcontent[] = $titlehtml;
			$title_index = count($this->head_content)-1;
		} else {
			$this->headcontent[$title_index] = $titlehtml;
		}
		
		$this->title = $title;
		return $this;
	}
	

	/**
	 * Adds a stylesheet link to the document head.
	 *
	 * @param string $filename 
	 * @param string $media 
	 * @return Page
	 */
	function addStyle($filename, $media='') {
		$path = $this->stylesheet($filename);
		if ($media) $media = " media=\"{$media}\"";
		
		$this->headcontent[] = "<link rel=\"stylesheet\" href=\"{$path}\" type=\"text/css\"{$media}>";
		return $this;
	}
	
	
	/**
	 * Adds a javascript include to the document head
	 *
	 * @param string $filename 
	 * @return Page
	 */
	function addScript($filename, $position='top') {
		$path = $this->script($filename);
		switch ($position){
		case 'top':
			$this->headcontent[] = "<script src=\"{$path}\" type=\"text/javascript\" charset=\"utf-8\"></script>";
			break;
		case 'bottom':
			$this->footcontent[] = "<script src=\"{$path}\" type=\"text/javascript\" charset=\"utf-8\"></script>";
			break;
		}
		return $this;
	}
	
	
	/**
	 * Add named meta tag to the document head
	 *
	 * @param string $name 
	 * @param string $content 
	 * @return Page
	 */
	function addMeta($name, $content) {
		$this->headcontent[] = "<meta name=\"{$name}\" content=\"{$content}\">";
		return $this;
	}


	/**
	 * Add named meta HTTP-EQUIV tag to the document head
	 *
	 * @param string $name 
	 * @param string $content 
	 * @return Page
	 */
	function addMetaEquiv($name, $content) {
		$this->headcontent[] = "<meta http-equiv=\"{$name}\" content=\"{$content}\">";
		return $this;
	}
	
	
	/**
	 * undocumented function
	 *
	 * @param string $content 
	 * @return Page
	 */
	function addRawHeadHTML($content) {
		$this->headcontent[]=$content;
		return $this;
	}

	
	/**
	 * Sets an attribute on the <body> tag
	 *
	 * @param string $attribute 
	 * @param string $value 
	 * @return Page
	 */
	function addBodyAttribute($attribute, $value) {
		$this->bodyattributes[$attribute] = $value;
		return $this;
	}
	
	/**
	 * Adds a css class name to the <body> tag.
	 *
	 * @param string $class The css class to add.
	 * @return Page
	 */
	function addClass($class) {
		$this->bodyclasses[] = $class;
		return $this;
	}
	

	/**	
		PAGE START
	*/	
	function start() {
?><!DOCTYPE HTML>
<html lang="en">
<head>
<?php foreach ($this->headcontent as $content) echo $content,"\n"; ?>
</head>
<body class="<?php echo implode(' ', $this->bodyclasses); ?>" <?php foreach ($this->bodyattributes as $a=>$v) echo "{$a}=\"{$v}\" "?>>
<?php		
	}
	
	
/**	
	PAGE STOP
*/	
	function stop($exit=true) {?>
<?php foreach ($this->footcontent as $content) echo $content,"\n"; ?>
</body>
</html><?php
		if ($exit) exit;
	}
	
	
	
	/**
	 * Returns the path to the CSS folder
	 *
	 * @param string $more File to be appended to the end of the result
	 * @return string
	 * @static
	 */
	public function stylesheet($path) 	{
		return ($path[0]=='/' || strpos($path,'http://')===0) ? 
			$path : 
			$this->styleroot.$path
		;
	}

	/**
	 * Returns the path to the JavaScript folder
	 *
	 * @param string $more File to be appended to the end of the result
	 * @return string
	 * @static
	 */
	public function script($path) 	{
		return ($path[0]=='/' || strpos($path,'http://')===0) ? 
			$path : 
			$this->scriptroot.$path
		;
	}
	
	
}


