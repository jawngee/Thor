<?='<?'?>	
uses('system.data.model');
	
/**
 * <?=$classname?> Model
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
class Base<?=$classname?> extends Model
{
	public $table_name='<?=$tableschema->schema?>.<?=$tableschema->tablename?>';
	<? if ($tableschema->view): ?>

	public $primary_key='id';
	public $readonly=true;

	<? else: ?>

	public $primary_key='<?=$tableschema->primarykey->name?>';

	<? endif; ?>
	public $database='<?=$database?>';

	/**
	* Describes the schema and validation rules for this object.  This is auto-generated.
	*/
	protected function describe()
	{
	    // describe the fields/columns
<? 
foreach($tableschema->columns as $column):
	$fieldtype='Field::STRING';
	switch($column->type)
	{
		case Field::NUMBER:
			$fieldtype='Field::NUMBER';
			break;
		case Field::TEXT:
			$fieldtype='Field::TEXT';
			break;
		
		case Field::TIMESTAMP:
			$fieldtype='Field::TIMESTAMP';
			break;
		
		case Field::BOOLEAN:
			$fieldtype='Field::BOOLEAN';
			break;
		
		case Field::BLOB:
			$fieldtype='Field::BLOB';
			break;
	}
?>		
	    // <?=$column->name?> - <?=($column->description=='') ? 'TODO: DOCUMENT YOUR DATABASE DUDE' : str_replace("'","\\'",$column->description)?>

	    $this->fields['<?=$column->name?>']=new Field('<?=$column->name?>',<?=$fieldtype?>,<?=$column->length?>,'<?=($column->description=='') ? 'Undocumented column' : str_replace("'","\\'",$column->description)?>',<?=(($column->notnull) ? "true" : "false")?>);
<? endforeach; ?>		

	}
}
