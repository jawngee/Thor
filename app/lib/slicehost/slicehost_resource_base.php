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
* "Slicehost" and "slice" are trademarks of Slicehost, LLC.
*/

/**
 * Base object for slicehost resources.
 */
class SlicehostResourceBase implements ArrayAccess, Iterator
{
	/**
	 * Slicehost instance
	 * @var Slicehost
	 */
	public $slicehost=null;

	/**
	 * Array of properties
	 * @var array
	 */
	protected $_props=array();
	
	/**
	 * Constructor
	 * 
	 * @param $slicehost Slicehost Instance of a slicehost.
	 * @param $item SimpleXMLElement The element to parse properties from.
	 */
	public function __construct($slicehost,$item=null)
	{
		$this->slicehost=$slicehost;
		
		// loop through each item of the node
		if ($item)
			foreach($item as $node)
			{
				$nodeName=$node->getName();
				
				// determine if this is an array
				$type=(String)$node['type'];
				if ($type=='array')
				{
					// assign an array to the property
					// and parse out the children using xpath
					$this->_props[$nodeName]=array();
					$children=$node->xpath('child::*');
					foreach($children as $child)
						$this->_props[str_replace('-','_',$nodeName)][]=(String)$child;
				}
				else
					$this->_props[str_replace('-','_',$nodeName)]=(String)$node;
			}
	}

	/**
	 * Property getter
	 *
	 * @param string $name Name of the property.
	 * @return mixed
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
	 * @param string $name Name of the property
	 * @param mixed $value
	 */
	public function __set($name,$value)
	{
		$this->_props[$name]=$value;
	}
	
	/**
	 * Array access
	 *
	 * @param mixed $offset
	 * @return mixed
	 */
	function offsetExists($offset)
	{
		return isset($this->_props[$offset]);
	}
	
	/**
	 * Array access
	 *
	 * @param mixed $offset
	 * @return mixed
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
	 * @param mixed $offset
	 * @return mixed
	 */
	function offsetUnset($offset)
	{
		unset($this->_props[$offset]);
	}
	
	/**
	 * Array access
	 *
	 * @param mixed $offset
	 * @return mixed
	 */
	function offsetSet($offset,$value)
	{
		$this->_props[$offset]=$value;	
	}
	
	/**
	 * Iterator impl.
	 */
    public function key()
    {
        return key($this->_props);
    }

	/**
	 * Iterator impl.
	 */
    public function current()
    {
        return current($this->_props);
    }

	/**
	 * Iterator impl.
	 */
    public function next()
    {
        return next($this->_props);
    }

	/**
	 * Iterator impl.
	 */
    public function rewind()
    {
        return reset($this->_props);
    }

	/**
	 * Iterator impl.
	 */
    public function valid()
    {
        return (bool) $this->current();
    }
}


