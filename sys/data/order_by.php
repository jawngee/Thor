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

uses('system.data.order');

/**
 * Contains a list of sort orders for a filter
 */
class OrderBy
{
	private $orders=array();	/** List of sort orders */	
	private $model=null;		/** Reference to the filter's model */
	private $filter=null;		/** Reference to filter object */
	/**
	 * Constructor
	 * 
	 * @param Model $model A reference to the model being filtered/sorted
	 */
	public function __construct($filter,$model)
	{
		$this->filter=$filter;
		$this->model=$model;
	}
	
	/**
	 * Allows us to declare ordering by requesting "properties" of the object by field name.
	 * When a property is requested, a new Order class is created for the requested
	 * field, or a pre-existing one is returned if it already exists.
	 */
	function __get($prop_name)
   	{
   		if (isset($this->orders[$prop_name]))
   			$result=&$this->orders[$prop_name];
   		else if (isset($this->model->fields[$prop_name]))
	   		$result=new Order($this->filter,$this->model->fields[$prop_name]->name);
   		else if ($this->model->primary_key==$prop_name)
	   		$result=new Order($this->filter,$this->model->primary_key);
   		else
	   		$result=new Order($this->filter,$prop_name,true);
   		
   		$this->orders[$prop_name]=&$result;
   		return $result;
   	}
   	
   	/**
   	 * Returns rows randomly
   	 */
   	function random()
   	{
   		$this->orders['random']=new Order($this->filter,'random()',true);
   	}
   	
   	/**
   	 * Clears out the order bys
   	 */
   	function clear()
   	{
   		$this->orders=array();
   	}
   	
   	/**
   	 * Returns the list of sort orders as SQL
   	 */
   	function to_sql()
   	{
   		if (count($this->orders)==0)
   			return '';
   			
   		$result=' ORDER BY ';
   		
   		foreach($this->orders as $order)
   			if ($order->computed)
 				$result.=$order->field.(($order->is_not_null)?' is not null ':'')." $order->direction,";
   			else
 				$result.=(($this->model->db->supports(Database::FEATURE_PREFIX_COLUMNS)) ? $this->model->table_name.'.' : '')."$order->field ".(($order->is_not_null)?' is not null ':'')." $order->direction,";
   			
   		return rtrim($result,',');	
   	}
	
}
 