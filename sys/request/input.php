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

/**
 * Wraps input (get, post) from the incoming request and acts as a filter.
 */
 
uses('sys.request.upload');

/**
 * Input filter class
 */
final class Input implements Iterator
{
	protected static $_post=null;
	protected static $_get=null;
	protected static $_request=null;
	protected static $_files=null;
	
	private $_data=null;
	
	/**
	 * Constructor
	 * 
	 * @param array $data Input data array.
	 */
	protected function __construct(&$data)
	{
		$this->_data=&$data;
	}
	
	/**
	 * Fetches a filtered Request input
	 * 
	 * @return Input A filtered Input object based on $_REQUEST
	 */
	public static function Request()
	{
		if (self::$_request==null)
			self::$_request=new Input($_REQUEST);
		
		return self::$_request;
	}
	
	/**
	 * Fetches a filtered Post input
	 * 
	 * @return Input A filtered Input object based on $_POST
	 */
	public static function Post()
	{
		if (self::$_post==null)
			self::$_post=new Input($_POST);
		
		return self::$_post;
	}
	

	/**
	 * Fetches a filtered Get input
	 * 
	 * @return Input A filtered Input object based on $_GET
	 */
	public static function Get()
	{
		if (self::$_get==null)
			self::$_get=new Input($_GET);
		
		return self::$_get;
	}
	
	public static function Files()
	{
		if (self::$_files==null)
			self::$_files=new Input(Upload::GetFiles());
		
		return self::$_files;
	}
	
	/**
	 * Allows setting $_POST, $_GET, $_REQUEST.  Useful for testing and calling controllers from the command line.
	 *
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public static function SetValue($key,$value)
	{
		global $_POST;
		global $_GET;
		global $_REQUEST;
		
		$_POST[$key]=$_GET[$key]=$_REQUEST[$key]=$value;
	}
	
	/**
	 * Determines if input has a value with the given name
	 *
	 * @return unknown
	 */
	function exists()
	{
		$args=func_get_args();
		
		if (count($args)==1)
			return isset($this->_data[$args[0]]);
			
		foreach($args as $arg)
			if (!isset($this->_data[$arg]))
				return false;
				
		return true;
	}
	
	/**
	 * Determines if input has any of the supplied names
	 *
	 * @return unknown
	 */
	function has()
	{
		$args=func_get_args();
		
		foreach($args as $arg)
			if (isset($this->_data[$arg]))
				return true;
				
		return false;
	}
	
	function count()
	{
		return count($this->_data);
	}
	
	/**
	 * Determines if the input has an item with a name and that the value matches
	 *
	 * @param unknown_type $what
	 * @param unknown_type $value
	 * @return unknown
	 */
	function has_value($what,$value)
	{
		if (!$this->exists($what))
			return false;
			
		$val=$this->_data[$what];
		
		if (is_array($val))
			return in_array($value,$val);
		else
			return ($val==$value);
	}
	
	/**
	 * Fetches the value, insuring it's a number
	 *
	 * @param unknown_type $prop_name
	 * @param unknown_type $default_value
	 * @return unknown
	 */
	function get_num($prop_name,$default_value=null)
	{
		if ((!isset($this->_data[$prop_name]))||(trim($this->_data[$prop_name])=='')||(!is_numeric($this->_data[$prop_name])))
			return $default_value;
			
		return ($this->_data[$prop_name]) ? $this->_data[$prop_name] : '0';
	}
	
	/**
	 * Returns the value as a boolean
	 *
	 * @param unknown_type $prop_name
	 * @return unknown
	 */
	function get_boolean($prop_name, $default=false)
	{
		if (!isset($this->_data[$prop_name]))
			return $default;
			
		return (($this->_data[$prop_name] == 'on') || ($this->_data[$prop_name]=='true'));
	}
	
	/**
	 * Returns the value as a valid date.
	 *
	 * @param unknown_type $prop_name
	 * @return unknown
	 */
	function get_date($prop_name)
	{
		if (!isset($this->_data[$prop_name]))
			return FALSE;
		
		$value=str_replace('/','-',trim($this->get_string($prop_name)));
		if (!preg_match('/^\d{1,2}-\d{1,2}-(\d{2}|\d{4})$/', $value))
            return FALSE;

		return $value;
	}
	
	/**
	 * Returns the value as a valid zipcode
	 *
	 * @param unknown_type $prop_name
	 * @return unknown
	 */
	function get_zipcode($prop_name)
	{
		if (!isset($this->_data[$prop_name]))
			return FALSE;
		
		$value=str_replace('/','-',$this->get_string($prop_name));
		if (!preg_match('/^[0-9]{5,5}([- ]?[0-9]{4,4})?$/', $value))
            return FALSE;

		return $value;
	}
	
	/**
	 * Determines if any data has been passed in.
	 *
	 * @return unknown
	 */
	function is_postback()
	{
		return (count($this->_data)>0);
	}
	
	/**
	 * Determines if any files have been posted.
	 *
	 * @param unknown_type $prop_name
	 * @return unknown
	 */
	function has_file($prop_name)
	{
		if (!isset($_FILES[$prop_name]))
			return false;
			
		if ($_FILES[$prop_name]['error']>0)
			return false;

		if ($_FILES[$prop_name]['size']==0)
			return false;
			
		return true;
	}
	
	/**
	 * Returns the filename for an upload file
	 *
	 * @param unknown_type $prop_name
	 * @return unknown
	 */
	function file_name($prop_name)
	{
		if (!$this->has_file($prop_name))
			return false;

		return $_FILES[$prop_name]['name'];
	}
	
	/**
	 * Moves the file to a temporary directory.
	 *
	 * @param unknown_type $prop_name
	 * @return unknown
	 */
	function move_file($prop_name)
	{
		if ($this->has_file($prop_name))
		{
			$fname=PATH_TEMP.$_FILES[$prop_name]["name"];
			move_uploaded_file($_FILES[$prop_name]["tmp_name"],$fname);
			chmod($fname,0666);
			return $fname;
		}
		else
		 return false;
	}

	
	/**
	 * Returns the input as a serialized string.
	 *
	 * @return unknown
	 */
	function to_string()
	{
		return serialize($this->_data);
	}
	
	/**
	 * Reconstitutes the input from a string.
	 *
	 * @param unknown_type $str
	 */
	function from_string($str)
	{
		$name_value_pairs=unserialize($str);
		foreach($name_value_pairs as $key => $value)
			$this->_data[$key]=$value;
	}
	
	/**
	 * Overload for getting at input variables as member variables.
	 */
	function __get($prop_name)
    {
        if (isset($this->_data[$prop_name])) 
        {
        	$val=$this->_data[$prop_name];
        	
       		return $val;
        }
        else
        	return false;
    }

	/**
	 * Overload for setting input variables as member variables.
	 */
    function __set($prop_name, $prop_value)
    {
        $this->_data[$prop_name]=$prop_value;
     }

    public function key()
    {
        return key($this->_data);
    }

    public function current()
    {
        return current($this->_data);
    }

    public function next()
    {
        return next($this->_data);
    }

    public function rewind()
    {
        return reset($this->_data);
    }

    public function valid()
    {
        return (bool) $this->current();
    }
}