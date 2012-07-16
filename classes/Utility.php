<?php 

class Utility {
	
	/**
	 * When passed an input string of comma separated values and an array of allowed values, will return a new comma separated string containing only the allowed values.
	 *
	 * @author Jarvis Badgley
	 */
	static function FilterSafeValues ($input, $keyed_values) {
		$input = preg_split('/,\s*/', $input);
		$input = array_intersect_key($keyed_values, array_combine($input, $input));
		return implode(',', $input);
	}
	
	/**
	 * Takes a named array of indexed arrays and returns an indexed array of named arrays.
	 * Useful for converting a collection of indexed form fields (ie: name="field[]") and getting individual records
	 *
	 * @param array $input The array to be flopped
	 * @return array
	 * @static
	 */
	static function ArrayFlop($input) {
		$results = array();
		foreach ($input as $key=>$collection) 
			foreach ($collection as $i=>$value) $results[$i][$key] = $value;
		return $results;
	}
	
	/**
	 * Recursively traverses a multi-dimensional named array and returns a single dimensioned array of the contents.
	 *
	 * @param array $input
	 * @return array
	 * @static
	 */
	static function ArrayFlatten($input) {
		$flat_array = array();
		foreach(new RecursiveIteratorIterator(new RecursiveArrayIterator($input)) as $k=>$v)
		$flat_array[$k] = $v;
		return $flat_array;
	}
	
		
	/**
	 * Limits the passed string to a max length, trimming and appending an ellipsis if the string goes over.
	 *
	 * @param string $text Text to be limited
	 * @param string $length
	 * @return array
	 * @static
	 */
	static function StrLimit($text, $length=26) { 
		if (strlen($text)>$length) {
		    $text = $text." "; 
		    $text = substr($text,0,$length); 
			$newlen = strrpos($text,' ');
		    if ($newlen > strlen($text)-10) $text = substr($text,0,$newlen); 
		    $text = trim($text)."..."; 
		}
	    return $text;
	}
	

	/**
	 * Takes an integer and attaches the correct ordinal suffix.
	 *
	 * @param integer $input The array to be flopped
	 * @return string
	 * @static
	 */
	static function Ordinalize($number) {
		if (in_array(($number % 100), array(11,12,13)))	return $number.'th';
		else switch (($number % 10)) {
			case 1:		return $number.'st';
			case 2:		return $number.'nd';
			case 3:		return $number.'rd';
			default:	return $number.'th';
		}
	}

	/**
	 * Returns a url safe base64 encoded version of the input
	 *
	 * @param string $input 
	 * @return string
	 * @static
	 */
	static function Base64URLEncode($input) {
		return strtr(base64_encode($input), '+/=', '-_,');
	}

	/**
	 * Returns the original value of the passed url safe base64 string
	 *
	 * @param string $input 
	 * @return string
	 * @static
	 */
	static function Base64URLDecode($input) {
		return base64_decode(strtr($input, '-_,', '+/='));
	}

	/**
	* Prefixes the passed value with the full absolute URL of the Primal site, with http or https as currently used.
	*
	* @param string $more File to be appended to the end of the result
	* @return string
	* @static
	*/
	public static function URLAbsolutize($path='') 	{
		return 
			( $_SERVER["HTTPS"]=='on' ? "https://{$_SERVER["HTTP_HOST"]}" : "http://{$_SERVER["HTTP_HOST"]}") .
			dirname($_SERVER["PHP_SELF"]) .
			( $more[0]=='/' ? substr($path, 1) : $path)
		;
	}
	
	
	/**
	* Strips the passed string down to only characters that are safe for using in a URL.  Most useful for adding article titles to links for SEO purposes.
	*
	* @param string $string The string to be sanitized
	* @return string The newly sanitized result
	* @static
	*/
	public static function URLSanitize($string) {
		$t = preg_replace('/\s/','_', $string);
		$t = preg_replace('/[^a-zA-Z0-9\-\_\.]/','', $t);
		return $t;
	}
	
	
	/**
	 * Returns the first non-null value
	 *
	 * @return mixed
	 * @static
	 */
	public static function Coalesce() {

		$args = func_get_args();
		$args = array_filter($args);
		
		return reset($args);
	}
	
	
	/**
	 * Date sanitization function, always results in either null or a string.
	 *
	 * @param string $input 
	 * @return void
	 * @author Jarvis Badgley
	 */
	const DATEPARSE_DATETIME = 0;
	const DATEPARSE_DATE = 1;
	const DATEPARSE_TIME = 2;
	public static function ParseDate($input, $return = 0) {
		if ($input) {
			if (!($input instanceof DateTime)) {
				if (is_string($input)) {
					if (strtolower($input) === 'now') {
						$input = new DateTime();
					} else {
						try {
							$input = new DateTime($input);
						} catch (Exception $e) {
							return null;
						}
					}
				} elseif (is_integer($input) && $input>0) {
					$input = new DateTime();
					$input->setTimestamp($input);
				} else {
					return null;
				}
			}
			switch ($return) {
			case 0: return $input->format('Y-m-d H:i:s'); 	//date and time
			case 1: return $input->format('Y-m-d');			//date only
			case 2: return $input->format('H:i:s');			//time only
			}
		}
		return null;
	}
	
}
