 
<?
foreach($schemas as $schema)
	echo "{$schema['schema']}\n";

foreach($tables as $t)
	echo "{$t['tablename']}\n";
	
if ($table):
?>
{{$table->schema}}.{{$table->tablename}}

{{str_pad('',120,'-')}}

{{str_pad('Column',24,' ')}}{{str_pad('Type',12,' ')}}{{str_pad('Len',6,' ')}}{{str_pad("Req'd",6,' ')}}{{str_pad('Description',24,' ')}}

{{str_pad('',23,'-')}} {{str_pad('',11,'-')}} {{str_pad('',5,'-')}} {{str_pad('',5,'-')}} {{str_pad('',84,'-')}}

{{str_pad('*'.$table->primarykey->name,24,' ')}}{{str_pad($table->primarykey->db_type,12,' ')}}{{str_pad($table->primarykey->length,6,' ')}}{{str_pad('yes',6,' ')}}{{$table->primarykey->description}}

<? foreach($table->columns as $column):?>
{{str_pad($column->name,24,' ')}}{{str_pad($column->db_type,12,' ')}}{{str_pad($column->length,6,' ')}}{{str_pad($column->notnull ? 'yes' : 'no',6,' ')}}{{$column->description}}

<? endforeach; ?>

Related:

<? foreach($table->related as $rel):?>
{{$rel->schema}}.{{$rel->tablename}}

<? endforeach; ?>

<?
endif;

//vomit($table);