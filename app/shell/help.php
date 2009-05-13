<?
class HelpController extends Controller
{
	function index()
	{
		$args=func_get_args();
		
		$command=null;
		$command_info=null;

		// load the conf file if it exists
		$conffile=PATH_APP.'conf/commands.js';
		$commands=(file_exists($conffile)) ? json_decode(file_get_contents($conffile),true) : array();
				
		if (count($args>0))
		{
			$command=implode('/',$args);
			if (isset($commands[$command]))
				$command_info=$commands[$command];
		}
		
		return array(
			'command'=>$command,
			'command_info'=>$command_info,
			'commands'=>$commands
		);
	}
	
	/**
	 * Registers the command in the help system.
	 * 
	 * @param $command
	 * @param $class
	 * @param $method
	 * @return unknown_type
	 */
	private function _register($command, $class, $method)
	{
		$mref=new ReflectionMethod($class,$method);
		$refp=$mref->getParameters();
		$refparams=array();
		foreach($refp as $param)
			$refparams[$param->name]=$param;
		
		// load the conf file if it exists
		$conffile=PATH_APP.'conf/commands.js';
		if ((!$this->post->clean) && (file_exists($conffile)))
			$conf=json_decode(file_get_contents($conffile),true);
		else
			$conf=array();
		
		// break up the comment block into seperate lines
		$comments=explode("\n",$mref->getDocComment());
		
		// top and bottom elements are useless
		$comments=array_slice($comments,1,count($comments)-2);
		
		$info=array();
		$description=array();
		$params=array();
		
		foreach($comments as $comment)
		{
			$comment=substr(trim($comment),2);
						
			if (preg_match('#@param\s([^\s]*)\s([^\s]*)\s(.*)#',$comment,$matches))
			{
				$pname=ltrim($matches[2],'$');
				
				$p=array(
					'name'=>ltrim($matches[2],'$'),
					'type'=>$matches[1],
					'optional'=>false,
					'description'=>$matches[3]
				);
				
				if (array_key_exists($pname,$refparams))
					$p['optional']=$refparams[$pname]->isOptional();

				$info['parameters'][]=$p;
			}
			if (preg_match('#@switch\s([^\s]*)\s([^\s]*)\s(.*)#',$comment,$matches))
				$info['switches'][]=array(
					'name'=>ltrim($matches[2],'$'),
					'type'=>$matches[1],
					'description'=>$matches[3]
				);
			else if (preg_match('#@title\s(.*)#',$comment,$matches))
				$info['title']=$matches[1];
			else if (($comment{0}!='@')&&($comment!=''))
				$description[]=$comment;
		}
		
		if (!isset($info['title']))
			return false;
		
		$info['description']=implode("\n",$description);
		
		$conf[$command]=$info;
		file_put_contents($conffile,json_encode($conf));
		
		return true;
	}
	
	/**
	 * Registers a command with the help system
	 * 
	 * @title Register Command
	 * @param string $command The name of the command.
	 * @switch clean Resets the configuration file.
	 */
	function register()
	{
		$args=func_get_args();
		$path=$command=implode('/',$args);
		
		$parsed_uri=Dispatcher::ParseURI($path, PATH_APP.'shell/');
		
		require_once($parsed_uri['root'].$parsed_uri['controller'].EXT);

		$classname=$parsed_uri['controller'].'Controller';
		$method=find_methods($classname, $parsed_uri['method'], 'index');

		if (!$method)
			throw new Exception("Could not find '$command'.");

		$this->_register($command,$classname,$method);
	}
	
	/**
	 * Unregisters a command from the help system
	 * 
	 * @title Unregister Command
	 * @param string $command The name of the command
	 */
	function unregister()
	{
		$args=func_get_args();
		$path=$command=implode('/',$args);
		
		// load the conf file if it exists
		$conffile=PATH_APP.'conf/commands.js';
		if (file_exists($conffile))
			$conf=json_decode(file_get_contents($conffile),true);
print_r($conf);die;
		if (isset($conf[$command]))
		{
			unset($conf[$command]);
			file_put_contents($conffile,json_encode($conf));
		}
	}
}