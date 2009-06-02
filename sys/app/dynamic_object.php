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
 * An object that allows dynamic assignment of properties and can be treated as
 * a keyed array.
 */
class DynamicObject implements ArrayAccess, Iterator
{
	private $_props=array();

	/**
	 * Constructor.  Pass in a keyed array to prepopulate.
	 *
	 * @param array $props
	 */
	public function __construct($props=null)
	{
		if (is_array($props))
			foreach($props as $key=>$prop)
				$this->_props[$key]=(string)$prop;
		else if (is_string($props))
			$this->_props=unserialize($props);
	}
	
	/**
	 * Property getter
	 *
	 * @param unknown_type $name
	 * @return unknown
	 */
	public function __get($name)
	{
		if (!isset($this->_props[$name]))
			return null;
			
		return $this->_props[$name];
	}
	
	/**
	 * Property setter
	 *
	 * @param unknown_type $name
	 * @param unknown_type $value
	 */
	public function __set($name,$value)
	{
		$this->_props[$name]=$value;
	}
	
	/**
	 * Array access
	 *
	 * @param unknown_type $offset
	 * @return unknown
	 */
	function offsetExists($offset)
	{
		return isset($this->_props[$offset]);
	}
	
	/**
	 * Array access
	 *
	 * @param unknown_type $offset
	 * @return unknown
	 */
	function offsetGet($offset)
	{
		if (!isset($this->_props[$offset]))
			return null;
			
		return $this->_props[$offset];
	}
	
	/**
	 * Array access
	 *
	 * @param unknown_type $offset
	 * @return unknown
	 */
	function offsetUnset($offset)
	{
		unset($this->_props[$offset]);
	}
	
	/**
	 * Array access
	 *
	 * @param unknown_type $offset
	 * @return unknown
	 */
	function offsetSet($offset,$value)
	{
		$this->_props[$offset]=$value;	
	}
	
    /**
	 * Iterator implementation
     */
	public function key()
    {
        return key($this->_props);
    }

    /**
	 * Iterator implementation
     */
    public function current()
    {
        return current($this->_props);
    }

    /**
	 * Iterator implementation
     */
    public function next()
    {
        return next($this->_props);
    }

    /**
	 * Iterator implementation
     */
    public function rewind()
    {
        return reset($this->_props);
    }

    /**
	 * Iterator implementation
     */
    public function valid()
    {
        return (bool) $this->current();
    }
    
    /**
     * Serializes the object
     * 
     * @return string
     */
    public function to_string()
    {
		return serialize($this->_props);
    }
}