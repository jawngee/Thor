<?
include '../sys/sys.php';

// start buffering
ob_start();

// dispatch the request
uses('sys.dispatcher');
Dispatcher::Dispatch();

// flush the buffer
ob_flush();
