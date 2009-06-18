<?
uses('system.control.data.databound_formatter');

/**
 * Formats any value into a string.  Specify the format in the metadata.
 */
class PasswordFormatter extends DataboundFormatter 
{
	public function format_value($value,$field)
	{
		return str_pad('',strlen($value),'*');
	}
	
	public function edit_value($value,$field)
	{
		return "<input type='password' name='{$field['id']}' value='".$value."' />";
	}
}