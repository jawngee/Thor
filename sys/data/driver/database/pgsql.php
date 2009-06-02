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

uses('system.data.database');
uses('system.data.driver.database.pgsql_result');
uses("system.app.dynamic_object");


/**
 * Driver for PGSQL
 */
class PGSQLDatabase extends Database
{
    private $config=null;
    private $connection=null;
    private $connstr='';

    /**
     * Constructor 
     * 
     * @param $dsn string DSN for the connection
     */
    public function __construct($dsn)
    {
		$this->config=parse_url($dsn);
		if (!$this->config)
		    throw new DatabaseException("Invalid dsn '$dsn'.");
		
		
		$this->connstr='dbname='.trim($this->config['path'],'/');
		
		if (isset($this->config['host']))
		    $this->connstr.=' host='.$this->config['host'];
		if (isset($this->config['user']))
		    $this->connstr.=' user='.$this->config['user'];
		if (isset($this->config['port']))
			$this->connstr.=' port='.$this->config['port'];
		if (isset($this->config['pass']))
			$this->connstr.=' password='.$this->config['pass'];
			
		$this->connection=pg_connect($this->connstr);
		if (!$this->connection)
			throw new DatabaseExeception("Invalid database settings.");
    }


    /**
     * Determines if the driver supports a specific feature.
     *
     * @param string $feature
     */
    public function supports($feature) { return true; }
    


    /**
     * Performs an insert
     *
     * @param string $table_name Name of the table to update
     * @param array $fields Name/value pair of fields to update/insert
     */
    public function insert($table_name,$key,$fields)
    {
    	// extract the keys and values so we can build a prepared statement.
    	$keys=array_keys($fields);
    	$vals=array_values($fields);
    	
    	$sql="insert into $table_name (".implode(',',$keys).") values (";
    	for($i=1; $i<=count($vals); $i++)
    		$sql.='$'.$i.',';
    	$sql=trim($sql,',');

   		$sql.=") returning $key";

    	$res=pg_query_params($this->connection,$sql,$vals);
    	$row=pg_fetch_array($res);
    	
    	// return the id
    	return $row[0];
    }

    /**
     * Performs an update
     *
     * @param string $table_name Name of the table to update
     * @param string $key The name of the primary key
     * @param mixed $id The value of the key
     * @param array $fields Name/value pair of fields to update/insert
     */
    public function update($table_name,$key,$id,$fields)
    {
    	// extract the keys and values so we can build a prepared statement.
    	$keys=array_keys($fields);
    	$vals=array_values($fields);
    	
    	$sql="update $table_name set ";
    	
    	for($i=1; $i<=count($keys); $i++)
    		$sql.=$keys[$i-1].'=$'.$i.',';
    	$sql=trim($sql,',');

   		$sql.=" where $key=$id";
    	$res=pg_query_params($this->connection,$sql,$vals);
    	if (pg_affected_rows($res)<=0)
    		throw new DatabaseException("Could not update $table_name for $key=$id");
    		
    	return true;
    }

    /**
     * Performs a delete
     *
     * @param string $table_name Name of the table to update
     * @param string $key The name of the primary key
     * @param mixed $id The value of the key
     */
    public function delete($table_name,$key,$id)
    {
		$res=pg_query($this->connection,"delete from $table_name where $key=$id");
    	return (pg_affected_rows($res)>0);
    }

    /**
     * Executes a query statement
     *
     * @param string $query Query to execute.
     * @param int $offset Offset into the records to fetch
     * @param int $limit The number of records to fetch
     */
    public function execute($query,$offset=null,$limit=null)
    {
    	if ($offset)
    		$query.=" OFFSET $offset";
    		
    	if ($limit)
    		$query.=" LIMIT $limit";
    		
    	return new PGSQLResult(pg_query($this->connection,$query));
    }

	/**
	 * Fetches the row count for a given query
	 *
	 * @param string $key
	 * @param string $table_name
	 * @param string $where
	 */
	public function count($table_name,$key,$where=null,$distinct=false)
	{
		$d=($distinct) ? 'distinct' : '';
		
		$sql="select $d count($table_name.$key) from $table_name";
		if ($where)
			$sql.=" $where;";
   		
		return $this->get_one($sql);
	}


    /**
     * Executes an sql statement and returns the first value
     *
     * @param string $sql SQL to execute.
     */
    public function get_one($query)
    {
    	$res=pg_query($this->connection,$query);
    	$row=pg_fetch_array($res);
    	return $row[0];
    }

    /**
     * Executes an sql statement and returns the first row
     *
     * @param string $query Query to execute.
     */
    public function get_row($query)
    {
    	$res=pg_query($this->connection,$query);
    	return pg_fetch_assoc($res);
    }

    /**
     * Fetches a single row by the table's primary key
     *
     * @param unknown_type $table_name
     * @param unknown_type $key
     * @param unknown_type $id
     */
    public function fetch_row($table_name,$key,$id)
    {
		$res=pg_query($this->connection,"SELECT * FROM $table_name WHERE $key=$id");
		return pg_fetch_assoc($res);
    }


    /**
     * Executes an sql statement and returns the results as an array
     *
     * @param string $query Query to execute.
     */
    public function get_rows($query)
    {
    	$res=pg_query($this->connection,$query);
    	return pg_fetch_all($res);
    }

    /**
     * starts a transaction
     */
    public function begin()
    {
		throw new Exception("Not implemented.");
    }

    /**
     * Ends a transaction.
     */
    public function commit()
    {
		throw new Exception("Not implemented.");
    }

	/**
	 * Escapes a value for a sql statement
	 * 
	 * @param mixed $value The value to escape
	 * @return string The escaped value
	 */
	public function escape_value($type,$value=null)
	{
		if ($value==null)
			$value=$this->value;
		
   		$value=str_replace("'","''",$value);
		
		switch($this->type)
		{
			case Field::STRING:
			case Field::TEXT:
				return "'$value'";
			case Field::BOOLEAN:
				return ($value) ? "true" : "false";
			case Field::OBJECT:
				return "'".(($value instanceof DynamicObject) ? $value->to_string() : serialize($value))."'";
			default:
				if (is_numeric($value))
					return $value;
				else
					return "'$value'";
		}
	}    

	/**
	 * Parses a database array type into a php array
	 * 
	 * @param string $value
	 * @return array
	 */
	public function parse_array($value)
	{
		if ($value==null)
			return array();
		
		if (is_array($value))
			return $value;
			
		$value=trim($value,'{}');
		$result=explode(',',$value);
		
		for($i=0; $i<count($result); $i++)
			if ($result[$i]=='NULL')
				$result[$i]=null;
				
		return $result;
	}

	/**
	 * Collapses a php array into a database array type
	 * 
	 * @param array $value
	 * @return string
	 */
	public function collapse_array($value)
	{
		if (count($value)==0)
			return null;
			
		$result='';
		foreach($value as $val)
			if ($val==null)
				$result.='NULL,';
			else
				$result.="$val,";
		
		return 'ARRAY['.trim($result,',').']';
	}
   
}