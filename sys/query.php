<?
/**
 * Provides a wrapper around the query string, to allow controls to manipulate it.
 */
 class Query
 {
 	private $items=array();
 	
 	public function __construct()
 	{
 		$this->items=array();

 		// TODO: XSS Clean Input
 		foreach($_GET as $key => $item)
			$this->items[$key]=$item;
 	}
 	
	/**
	 * Gets the value of a query item
	 */
 	function get_value($name)
 	{
 		return (isset($this->items[$name])) ? $this->items[$name] : false;
 	}
 	
	/**
	 * Gets the first key in the query string that contains the substring $match
	 * (currently used to hang onto repeater id during pagination for ajax repeaters) 
	 */
	function get_key_like($match)
	{
		foreach($this->items as $key => $item)
			if (stristr($key, $match))
				return $key;
		
		return null;
	}
	
	/**
	 * Gets the value of a query item as a number
	 */
 	function get_number($name)
 	{
 		$value=$this->get_value($name);
 		
 		return (is_numeric($value)) ? $value : false;
 	}
 	
 	/**
 	 * Sets the value of a query item
 	 */
 	function set_value($name,$value)
 	{
 		$this->items[$name]=$value;
 	}
 	
 	/**
 	 * Removes a value from the query
 	 */
 	function remove_value($name)
 	{
 		unset($this->items[$name]);
 	}
 	
 	/**
 	 * Removes a value from the query
 	 */
 	function remove($name)
 	{
 		unset($this->items[$name]);
 	}
 	
 	/**
 	 * Returns the query string
 	 */
 	function build($newvalues=null,$removevalues=null)
 	{
 		$result='';
 		
 		$items=$this->items;
 		
 		if ($removevalues!=null)
			foreach($removevalues as $key)
				unset($items[$key]);
 		
		if ($newvalues!=null)
			foreach($newvalues as $key => $value)
				$items[$key]=$value;
 		
		$result='';
		
		foreach($items as $key=>$value)
		{
			if (is_array($value))
			{
				foreach($value as $item)
					$result.=$key.urlencode("[]")."=".urlencode($item)."&";
			}
			else
				$result.=$key.'='.urlencode($value)."&";
		}
				
 		$result=trim($result,'&');
		
		if ($result=='')
			return '';
		else
 			return '?'.$result;
 	}
}