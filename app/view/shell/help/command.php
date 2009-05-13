<?
	$usage=SHELL_SCRIPT.' '.$command;
	if (array_key_exists('parameters',$item))
		foreach($item['parameters'] as $parameter)
			$usage.=' '.(($parameter['optional']) ? '{':'').$parameter['name'].(($parameter['optional']) ? '}':'');
	if (array_key_exists('switches',$item))
		foreach($item['switches'] as $switch)
			$usage.=' --'.$switch['name'].'='.$switch['type'];
?>
{{$command}} - {{$item['title']}}


Usage: {{$usage}}


{{$item['description']}}


<? if(array_key_exists('parameters',$item)): ?>
Parameters
{{str_pad('',120,'=')}}

<? foreach($item['parameters'] as $param): ?>
{{str_pad($param['name'],14,' ')}}{{str_pad($param['type'],10,' ')}}{{($param['optional']) ? 'optional' : 'required'}}   {{$param['description']}} 
<? endforeach; ?>
<? endif; ?>


<? if(array_key_exists('switches',$item)): ?>
Switches
{{str_pad('',120,'=')}}

<? foreach($item['switches'] as $switch): ?>
{{str_pad($switch['name'],14,' ')}}{{str_pad($switch['type'],10,' ')}}{{$switch['description']}} 
<? endforeach; ?>
<? endif; ?>