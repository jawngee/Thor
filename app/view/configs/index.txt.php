<?	if (count($configs)==0): ?>
You don't have any configurations.
<?	else: ?>
{{str_pad('ID',32,' ')}}
 {{str_pad('Name',18,' ')}}
 {{str_pad('Image',32,' ')}}
 {{str_pad('Backup',32,' ')}}
 {{str_pad('Flavor',12,' ')}}
 
{{str_pad('',32,'=')}}
 {{str_pad('',18,'=')}}
 {{str_pad('',32,'=')}}
 {{str_pad('',32,'=')}}
 {{str_pad('',12,'=')}}
 
<? 		foreach($configs as $config): ?>
{{str_pad($config->id,32,' ')}}
 {{str_pad($config->name,18,' ')}}
 {{str_pad($config->image_desc,32,' ')}}
 {{str_pad($config->backup_desc,32,' ')}}
 {{str_pad($config->flavor_desc,12,' ')}}
 {{$config->notes}}
 
<? 		endforeach; ?>
<? 	endif; ?>