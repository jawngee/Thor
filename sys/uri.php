<?
/**
 * Allows manipulation of the request's URI and/or query string, for use by controls for such
 * things as pagination, etc.
 */

uses('sys.query');

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