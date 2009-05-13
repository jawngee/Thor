
List of available commands:

{{str_pad('Name',24,' ')}}{{str_pad('Command',24,' ')}}Usage
{{str_pad('',23,'=')}} {{str_pad('',23,'=')}} {{str_pad('',72,'=')}}

<? foreach($commands as $key=>$item): 
		$usage=SHELL_SCRIPT.' '.$key;
		if (array_key_exists('parameters',$item))
			foreach($item['parameters'] as $parameter)
				$usage.=' '.(($parameter['optional']) ? '{':'').$parameter['name'].(($parameter['optional']) ? '}':'');
		if (array_key_exists('switches',$item))
			foreach($item['switches'] as $switch)
				$usage.=' --'.$switch['name'].'='.$switch['type'];
?>
{{str_pad($item['title'],24,' ')}}{{str_pad($key,24,' ')}}{{$usage}}

<?  endforeach; ?>

Type '{{SHELL_SCRIPT}} help {command}' for help on individual commands.  For example, '{{SHELL_SCRIPT}} help model/push'.
