<?
// Path and File Related Constants
define('PATH_ROOT', dirname(dirname(__FILE__)."..")."/"); 	/** Root path */
define('PATH_APP', PATH_ROOT."app/");						/** Root path to app files */
define('PATH_SYS', PATH_ROOT."sys/");						/** Root path to system files */
define('PATH_LIB', PATH_APP."lib/");						/** Root path to library files */
define('PATH_CONTROLLER', PATH_APP."controller/");			/** Root path to controller files */
define('PATH_VIEW', PATH_APP."view/");						/** Root path to view files */
define('PATH_CONFIG', PATH_ROOT."conf/");					/** Root path to config files */
define('PATH_TEMP', PATH_ROOT."tmp/");						/** Root path to temp directory */
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
		case 'lib':
		case 'library':
			$path=PATH_LIB;
			break;
		case 'system':
			$path=PATH_SYS;
			break;
		case 'controller':
			$path=PATH_CONTROLLER;
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
	$args=array_slice(func_get_args(),2);
	
	foreach($args as $arg)
		if (method_exists($class,$arg))
			return $arg;
			
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
