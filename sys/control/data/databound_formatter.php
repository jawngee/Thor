<?
/**
 * A formatter for values in a databound control.
 *
 */
abstract class DataboundFormatter
{
	/**
	 * List of formatter instances
	 *
	 * @var array
	 */
	private static $_formatters=array();
	
	/**
	 * Formats the value
	 *
	 * @param mixed $id The id of the grid. 
	 * @param mixed $pk The row's primary key
	 * @param mixed $item The item to get the value from, must be a keyed array or implement ArrayAccess
	 * @param string $field The name of the field.
	 * @param mixed $meta Any metadata
	 */
	abstract public function format_value($value,$field);
	
	/**
	 * Returns the editor for the value.  Override if your formatter is editable.
	 *
	 * @param mixed $id The id of the grid. 
	 * @param mixed $pk The row's primary key
	 * @param mixed $item The item to get the value from, must be a keyed array or implement ArrayAccess
	 * @param string $field The name of the field.
	 * @param mixed $meta Any metadata
	 * @return string
	 */
	public function edit_value($value,$field)
	{
		return $this->format_value($value,$field);
	}
		
	/**
	 * Builds attributes for xml or html tag based on the metadata
	 * in the column.  Pass in the names of the attributes as extra
	 * arguments to the function:
	 * 
	 * build_attributes($channel,'link_','class','style');
	 *
	 * @param mixed $meta Column metadata 
	 * @param string $prefix The column metadata prefix.
	 * @return string
	 */
	protected function build_attributes($field,$prefix)
	{
		$args=func_get_args();
		$args=array_slice($args,2);
		
		$attrs='';
		// checks to see if the $prefix.$attr is defined in the metadata
		// if so, it appends the attribute to the $attrs string.
		foreach($args as $attr)
			$attrs.=($meta->{$prefix.$attr}) ? "$attr='".$meta->{$prefix.$attr}."' " : null;
		return $attrs;
	}
	
	/**
	 * Formats a value using a given formatter class path.
	 *
	 * @param string $class_path Class path for the formatter to use.
	 * @param mixed $item The item to get the value from, must be a keyed array or implement ArrayAccess
	 * @param string $field The name of the field.
	 * @param mixed $meta Any associated metadata for conversion.
	 * @return string The formatted value.
	 */
	public static function Format($class_path,$value,$field,$editing=false)
	{
		if (!isset(self::$_formatters[$class_path]))
		{
			uses($class_path);
		
			$names=explode('.',$class_path);
			$class_name=str_replace('_','',array_pop($names).'Formatter');
			$formatter=new $class_name();
		
			self::$_formatters[$class_path]=$formatter;
		}
		
		$formatter=self::$_formatters[$class_path];

		if ($editing)
//		if (($editing) && ((isset($field['editable']) && ($field['editable']))))
			return $formatter->edit_value($value,$field);
		else
			return $formatter->format_value($value,$field);
	}
}