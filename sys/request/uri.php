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
 * Allows manipulation of the request's URI and/or query string, for use by controls for such
 * things as pagination, etc.
 */

uses('sys.request.query');

/**
 * URI
 */
 class URI
 {
 	
 	public $root='';
 	public $segments=array();
 	public $query=null;
 	
 	public function __construct($root, $segments)
 	{
 		if (($root==null) && ($segments==null))
 		{
 		 	$path=(isset ($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @ getenv('PATH_INFO');
			$segments=explode('/',$path);
			array_shift($segments);
			$root=array_shift($segments);
 		}
 		
 		if ((strlen($root)==0) || ($root[0]!='/'))
 			$root='/'.$root;
 		
 		$this->root=$root;
 		$this->segments=$segments;
 		$this->query=new Query();
 	}
 	
	/**
	 * Gets the value of a segment-value in the URI
	 */
 	public function get_value($name)
 	{
 		for($i=0; $i<count($this->segments)-1; $i++)
 			if ($this->segments[$i]==$name)
 				return $this->segments[$i+1];
 		
 		return false;
 	}
 	
 	/**
 	 * Makes sure that the value of the segment-value in the URI is a number
 	 */
 	public function get_number($name)
 	{
 		$value=$this->get_value($name);
 		
 		return (is_numeric($value) ? $value : false);
 	}
 	
 	/**
 	 * Sets the value of a segment in the URI
 	 */
 	function set_value($name,$value)
 	{
 		if (!$name)
 		{
 			$this->segments[]=$value;
 			return;
 		}
 			
 		for($i=0; $i<count($this->segments); $i++)
 			if ($this->segments[$i]==$name)
 			{
 				array_splice($this->segments,$i+1,1,$value);
 				return;
 			}
 			
 		array_splice($this->segments,count($this->segments),0,array($name,$value));
 	}
 	
 	/**
 	 * Removes a segment-value pair from the URI
 	 */
 	function remove_value($name)
 	{
 		for($i=0; $i<count($this->segments)-1; $i++)
 			if ($this->segments[$i]==$name)
 			{
 				array_splice($this->segments,$i,2);
 				return;
 			}
 			
 		$this->remove($name);
 	}
 	
 	/**
 	 * Removes a segment from the URI
 	 */
 	function remove($name)
 	{
 		for($i=0; $i<count($this->segments); $i++)
 			if ($this->segments[$i]==$name)
 			{
 				array_splice($this->segments,$i,1);
 				return;
 			}
 	}
 	
 	/**
 	 * Replace a single segment in the URI
 	 */
 	function replace($name,$what)
 	{
 		for($i=0; $i<count($this->segments); $i++)
 			if ($this->segments[$i]==$name)
 			{
 				$this->segments[$i]=$what;
 				return;
 			}
 	}

 	/**
 	 * Returns the values + original path as a complete URI
 	 */
 	function build($newvalues=null, $queryvalues=null, $removevalues=null)
 	{
 		$segs=$this->segments;
 		
 		
  		if ($newvalues!=null)
	 		foreach($newvalues as $key=>$value)
	 		{
		 		$added=false;
 			
				if (!$key)
 				{
 					$segs[]=$value;
 					$added=true;
 				}
 				else for($i=0; $i<count($segs); $i++)
	 				if ($segs[$i]==$key)
		 			{
	 					array_splice($segs,$i+1,1,$value);
	 					
	 					$added=true;
	 					break;
	 				}
 			
	 			if (!$added)
		 			array_splice($segs,count($segs),0,array($key,$value));
		 	}
 			
 		return $this->root."/".implode('/',$segs).$this->query->build($queryvalues,$removevalues);
 	}
}