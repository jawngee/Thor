<?='<?'?>

uses('system.data.model');
uses('model.{{$tableschema->schema}}.base_{{$filename}}');

/**
 * {{$classname}} Model
 *
 * Contains the following properties:
 *
<?
	foreach($tableschema->columns as $column)
	{
?>
 * <?=$column->name?> - <?=($column->description=='') ? 'Undocumented column' : str_replace("'","\\'",$column->description)?>

<?
	}
?>
 *
 * @copyright  Copyright (c) 2007 massify.com, all rights reserved.
 */
class <?=$classname?> extends Base<?=$classname?>

{
	/**
	* Describes the schema and validation rules for this object.  This is auto-generated.
	*/
	protected function describe()
	{
	    parent::describe();

	    // create validators for columns
<?
	foreach($tableschema->columns as $column)
	if ($column->notnull)
	{
?>
	    $this->validators["<?=$column->name?>"]=array(
		RequiredValidator::Create("<?=$column->name?> is required.")
	    );
<?
	}
?>

	    // create relations for columns
<?
	foreach($tableschema->related as $key => $related)
	{
		$name=str_replace($id_suffix,'',(($related->related_field==null) ? str_replace($tableschema->tablename.'_','',$related->tablename) : $related->related_field));
//		if ($name==$tableschema->primarykey->name)
//			$name=$related->tablename;

		$reltype='Relation::RELATION_SINGLE';

		if ($related->type==Relation::RELATION_MANY)
		{
			$reltype='Relation::RELATION_MANY';
		}

?>	    $this->related['<?=$name?>']=new Relation($this,'<?=$name?>',<?=$reltype?>,'<?=$related->schema?>/<?=$related->tablename?>','<?=$related->related_column?>'<?=(($related->related_field==null) ? '' : ",'$related->related_field'")?>);
<?
	}
?>
	}
}
