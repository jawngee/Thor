<?
include '../sys/sys.php';

uses('sys.app.config');
uses('sys.app.dispatcher');
uses('sys.utility.profiler');


Config::LoadEnvironment();

Profiler::Init();

// start buffering
ob_start();

// dispatch the request
Dispatcher::Dispatch(null,PATH_APP.'controller/', PATH_APP.'view/');

// flush the buffer
ob_flush();
