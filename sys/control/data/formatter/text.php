<?
uses('system.control.data.databound_formatter');

/**
 * Formats any value into a string.  Specify the format in the metadata.
 */
class TextFormatter extends DataboundFormatter 
{
	public function format_value($value,$field)
	{
		$format=(isset($field['format'])) ? $field['format'] : '%s';
		return sprintf($format,$value);
	}
	
	public function edit_value($value,$field)
	{
		return "<input type='text' name='{$field['id']}' value='".$value."' />";
	}
}