Generated base model ... {{$base}}

<? if ($child): ?>
Generated child model ... {{$child}}
<? endif; ?>

<?
	foreach($tableschema->columns as $column)
	{
?>
	 * <?=$column->name?> - <?=($column->description=='') ? 'Undocumented column' : str_replace("'","\\'",$column->description)?>

<?
	}
?>