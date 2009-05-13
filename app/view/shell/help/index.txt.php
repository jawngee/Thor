HeavyMetal 2.0
<? 	if ($command_info!=null): ?>
<render:view view="shell/help/command" />
<? 	elseif($commands!=null):?>
<? 		if ($command!=null):?>

Unknown Command

<?		endif; ?>
<render:view view="shell/help/list" />
<? endif; ?>