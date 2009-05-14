#!/usr/bin/php
<?php

// include heavy metal's shell system
require_once('sys/sys.php');
require_once('sys/sys_shell.php');

uses('sys.app.config');
uses('sys.app.dispatcher');
uses('sys.utility.profiler');

define('SHELL_SCRIPT',array_pop(explode('/',__FILE__)));
ini_set('error_reporting',E_ERROR);
		


Config::LoadEnvironment();

$args=parse_args();

if (count($args)>0)
	$path='/'.implode('/',$args);
else
	$path="/help";
	
$switches=parse_switches();
foreach($switches as $key=>$value)
	$_POST[$key]=$value;
			
// start buffering
ob_start();

// dispatch the request
Dispatcher::Dispatch($path,PATH_APP.'shell/', PATH_APP.'view/shell/','txt');

// flush the buffer
ob_flush();
