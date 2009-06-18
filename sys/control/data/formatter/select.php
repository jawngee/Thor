<?
uses('system.data.channel');
uses('system.control.data.databound_formatter');

/**
 * Formats any value into a string.  Specify the format in the metadata.
 */
class SelectFormatter extends DataboundFormatter 
{
	public function format_value($value,$field)
	{
		
		if ($value)
		{
			$key=$field['key'];
			
			$ds=$field['datasource'];
			$data=Channel::GetDatasource("$ds?$key=$value");
			
			$format=($field['format']) ? $field['format'] : '%s';
	
			if (count($data)==1)
				return sprintf($format,$data[0]->{$field['field']});
		}
		
		return '';
	}
	
	public function edit_value($value,$field)
	{
		$ds=$field['datasource'];
		$key=$field['key'];
		
		$data=Channel::GetDatasource($ds);

		$result="\n<select name='{$field['id']}'>\n";
		
		foreach($data as $row)
		{
			$sel=($row[$key]==$value);
			
			if ($sel)
				$result.="\t<option selected='true' value='{$row[$key]}'>{$row[$field['field']]}</option>\n";
			else
				$result.="\t<option value='{$row[$key]}'>{$row[$field['field']]}</option>\n";
		}
		
		$result.="</select>\n";
		
		return $result;
	}
}