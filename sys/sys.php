<?
/**
*
* Copyright (c) 2009, Jon Gilkison and Massify LLC.
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions are met:
*
* - Redistributions of source code must retain the above copyright notice,
*   this list of conditions and the following disclaimer.
* - Redistributions in binary form must reproduce the above copyright
*   notice, this list of conditions and the following disclaimer in the
*   documentation and/or other materials provided with the distribution.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
* AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
* IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
* ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
* LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
* CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
* SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
* INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
* CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
* ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
* POSSIBILITY OF SUCH DAMAGE.
*
* This is a modified BSD license (the third clause has been removed).
* The BSD license may be found here:
* 
* http://www.opensource.org/licenses/bsd-license.php
*
*/

// Path and File Related Constants
define('PATH_ROOT', dirname(dirname(__FILE__)."..")."/"); 	/** Root path */
define('PATH_APP', PATH_ROOT."app/");						/** Root path to app files */
define('PATH_SYS', PATH_ROOT."sys/");						/** Root path to system files */
define('PATH_CONFIG', PATH_APP."conf/");					/** Root path to config files */
define('PATH_TEMP', PATH_ROOT."tmp/");						/** Root path to temp directory */
define('PATH_VENDOR', PATH_ROOT."vendor/");					/** Root path to vendor directory */
define('EXT', '.php');										/** Default file extension */

/**
 * Simpler, more elegant version of uses_*
 * 
 * Usage: uses('system.app.controller');

 * Can support wildcards:
 * 
 * uses('system.data.validators.*');
 *
 * @param string $what
 */
function uses($what)
{
	$parts=explode('.',$what);
	$type=array_shift($parts);
	$path='';
	
	switch($type)
	{
		case 'app':
		case 'application':
			$path=PATH_APP;
			break;
		case 'sys':
		case 'system':
			$path=PATH_SYS;
			break;
		case 'vendor':
			$path=PATH_VENDOR;
			break;
	}
	
	
	if ($parts[count($parts)-1]=="*")
	{
		array_pop($parts);
		$what=implode('/',$parts);
		
		$files=array();
		
		$files=files($path.$what);
		foreach($files as $file)
			require_once($file);
	}
	else
	{
		$what=implode('/',$parts);
		
		require_once($path.$what.EXT);
	}
}

/**
 * Print's an object using <pre> tags and escaping entities for display in an html page.
 * 
 * @param mixed $data The data to print.
 */
function dump($data)
{
	print '<pre>';
	if (is_object($data) || is_array($data))
	{
		ob_start();
		print_r($data);
		$data=ob_get_clean();
	}
	
	print htmlentities($data);
	print '</pre>';
}

/**
 * Exactly like dump but dies right after.  You can optionally include trace as well.
 *
 * @param mixed $data The data to dump
 * @param bool $show_trace Determines if trace should be shown.
 */
function vomit($data,$show_trace=false)
{
	if ($data instanceof Model)
		dump($data->to_array());
	else
		dump($data);
	
	die;
}

/**
 * Tries to find any matching method in a given class.
 * 
 * @param $class string Name of the class
 * @return string The name of the found method.
 */
function find_methods($class)
{
	$args=array_slice(func_get_args(),1);
	foreach($args as $arg)
	{
		if (method_exists($class,$arg))
			return $arg;
	}
			
	return FALSE;
}

/**
 * Sets the content type
 * 
 * @param string $type The content type.
 */
function content_type($type)
{
	header("Content-type:$type");
}


/**
 * Redirects a request to another resource
 * 
 * @param string $where Where to redirect to
 */
function redirect($where)
{
	header("Location:$where");
	die;
}

/**
 * Set cookie
 *
 * Accepts six parameter, or you can submit an associative
 * array in the first parameter containing all the values.
 *
 * @param	string	the value of the cookie
 * @param	string	the number of seconds until expiration
 * @param	string	the cookie domain.  Usually:  .yourdomain.com
 * @param	string	the cookie path
 * @param	string	the cookie prefix
 * @return	void
 */
function set_cookie($name = '', $value = '', $expire = '', $domain = '', $path = '/', $prefix = '')
{
	if (!is_numeric($expire))
		$expire = time() - 86500;
	else
	{
		if ($expire > 0)
			$expire = time() + $expire;
		else
			$expire = 0;
	}
	
	if (!defined('SESSION_DISABLED'))
		setcookie($prefix.$name, $value, $expire, $path, $domain, 0);
}
	
/**
 * Fetch an item from the COOKIE array
 *
 * @param	string
 * @param	bool
 * @return	mixed
 */
function get_cookie($index = '', $xss_clean = FALSE)
{
	if (!isset($_COOKIE[$index]))
		return FALSE;

	if (is_array($_COOKIE[$index]))
	{
		$cookie = array();
		foreach($_COOKIE[$index] as $key => $val)
			$cookie[$key] = $val;
	
		return $cookie;
	}
	else
		return $_COOKIE[$index];
}

/**
 * Renders a view
 * 
 * @param string $view Name of view to render, must be in the views subfolder
 * @param array $data The data to pass into the view
 * @return string The rendered view
 */
function render_view($view,$data=null)
{
  	if ($data!=null)
		extract($data);

	$contents=preg_replace("|{{([^}]*)}}|m",'<?=$1?>',file_get_contents($view.EXT));

	ob_start();		
	eval("?>".$contents);
	$result=ob_get_contents();
	ob_end_clean();
	return $result;
}

/**
 * Serialize php arrays to XML.  
 *
 * @param unknown_type $data
 * @param unknown_type $level
 * @param unknown_type $prior_key
 * @return unknown
 */
function &XML_serialize(&$data, $level = 0, $prior_key = NULL)
{ 
    if ($level == 0)
    { 
    	ob_start(); 
    	echo '<?xml version="1.0" ?>',"\n"; 
    } 
    
    while (list($key, $value) = each($data)) 
        if (!strpos($key, ' attr')) 
        {
        	if (is_array($value) and array_key_exists(0, $value))
                XML_serialize($value, $level, $key); 
            else
            { 
                $tag = $prior_key ? $prior_key : $key; 
                echo str_repeat("\t", $level),'<',$tag; 
                if (array_key_exists("$key attr", $data))
                { 
                	while (list($attr_name, $attr_value) = each($data["$key attr"])) 
                        echo ' ',$attr_name,'="',htmlspecialchars($attr_value),'"'; 
                    
                   reset($data["$key attr"]); 
                }

                if (is_null($value)) 
                	echo " />\n"; 
                else if (!is_array($value)) 
                	echo '>',htmlspecialchars($value),"</$tag>\n"; 
                else 
                	echo ">\n",XML_serialize($value, $level+1),str_repeat("\t", $level),"</$tag>\n"; 
            }
    
            reset($data); 
    		if($level == 0)
    		{ 
    			$str = &ob_get_contents(); 
    			ob_end_clean(); 
    			return $str; 
        	} 
        }
}

/**
 * Generates a unique id based on RFC 4122 sans dashes
 */
function uuid() {
   
    // The field names refer to RFC 4122 section 4.1.2

    return sprintf('%04x%04x%04x%03x4%04x%04x%04x%04x',
        mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
        mt_rand(0, 65535), // 16 bits for "time_mid"
        mt_rand(0, 4095),  // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
        bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
            // 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
            // (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
            // 8 bits for "clk_seq_low"
        mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535) // 48 bits for "node" 
    ); 
}

/**
 * Generates a unique path for files to help with cache/cdn/spreading across filesystem
 *
 * @return unknown
 */
function uuid_path($segments=3)
{
	$uid=uuid();
	$result='';
	
	if ($segments>strlen($uid)/2)
		$segments=strlen($uid)/2;

	for($i=0; $i<$segments; $i++)
		$result.=substr($uid,$i*2,2).'/';
		
	return $result;
}
