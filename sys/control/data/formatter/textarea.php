<?
uses('system.control.data.formatter.text');

/**
 * Formats any value into a string.  Specify the format in the metadata.
 */
class TextAreaFormatter extends TextFormatter 
{
	
	public function edit_value($value,$field)
	{
		return "<textarea name='{$field['id']}'>".$value."</textarea>";
	}
}