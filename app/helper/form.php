<?
function select($name,$value_field,$title_field,$data=null,$value=null,$none_title=null,$none_value='none')
{
	$options='';
	
	if ($none_title)
		$options.="<option value='{$none_value}'>{$none_title}</option>";
		
	foreach($data as $row)
	{
		$selected=($row[$value_field]==$value) ? "selected" : "";
		$options.=<<<WHOO
	<option value="{$row[$value_field]}" $selected>{$row[$title_field]}</option>
WHOO;
	}
	return <<<WHOO
<select name="{$name}">
$options
</select>
WHOO;
}