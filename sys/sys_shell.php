<?
/**
 * Safely gets arguments from the environment trying a couple of different methods.
 * Taken from Pear:GetOpt by Andrei Zmievski <andrei@php.net>
 */
function get_args()
{
	global $argv;
	if (!is_array($argv))
	{
		if (!@is_array($_SERVER['argv']))
		{
			if (!@is_array($GLOBALS['HTTP_SERVER_VARS']['argv']))
				throw new Exception("Could not read cmd args (register_argc_argv=Off?)");

			return $GLOBALS['HTTP_SERVER_VARS']['argv'];
		}
		
		return $_SERVER['argv'];
	}
    
    return $argv;
}

/**
 * Parses arguments into commands/options and switches
 */
function parse_args()
{
	$result=array();
	$args=array_slice(get_args(),1);
	$vals=array();
	for($i=0; $i<count($args);$i++)
		if(!preg_match('#--([^=]*)(?:[=]*)(.*)#',$args[$i],$vals))
			$result[]=$args[$i];

	return $result;
}

/**
 * Parses arguments into commands/options and switches
 */
function parse_switches()
{
	$result=array();
	$args=array_slice(get_args(),1);
	$vals=array();
	for($i=0; $i<count($args);$i++)
		if(preg_match('#--([^=]*)(?:[=]*)(.*)#',$args[$i],$vals)==1)
			$result[$vals[1]]=($vals[2]=='') ? true : $vals[2];			

	return $result;
}

/**
 * Handles SIG handlers
 */
$sigterm=false;
$sighup=false;

/**
 * SIG handler
 */
function sig_handler($signo) 
{
	global $sigterm, $sigup;
	
	if($signo == SIGTERM)
		$sigterm = true;
 	else if($signo == SIGHUP)
		$sighup = true;
}

/**
 * Forks the current process.
 */
function fork()
{
	ini_set("max_execution_time", "0");
	ini_set("max_input_time", "0");
	set_time_limit(0);
	
	pcntl_signal(SIGTERM, "sig_handler");
	pcntl_signal(SIGHUP, "sig_handler");
	pcntl_signal(SIGINT, "sig_handler");
	
	$pid = pcntl_fork();
	file_put_contents('php://stdout',$pid);
	if($pid == -1)
	    die("There is no fork()!");
	
	if($pid)
	{
	    echo($pid);
	    exit(0);
	}
}