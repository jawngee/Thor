<?
abstract class Profiler
{
	private static $_profiler=null;
	
	public static function Init()
	{
		$profiler=Config::$environment_config->profiler;
		if ((self::$_profiler==null) && ($profiler!=null) && ($profiler!="none")) 
		{
			uses('sys.utility.profilers.'.$profiler);
			
			$class=$profiler."Profiler";
			self::$_profiler=new $class();
		}
	}
	
	public static function Log($message)
	{
		if (self::$_profiler)
			self::$_profiler->_log($message);
	}
	
	public static function Error($exception,$message)
	{
		if (self::$_profiler)
			self::$_profiler->_error($exception,$message);
	}
	
	public static function Memory($variable, $name)
	{
		if (self::$_profiler)
			self::$_profiler->_memory($variable,$name);
	}

	public static function Speed($name)
	{
		if (self::$_profiler)
			self::$_profiler->_speed($name);
	}
	
	public static function LogQuery($query,$time)
	{
		
	}
	
	public static function Display()
	{
		if (self::$_profiler)
			return self::$_profiler->_display();
	}

	protected abstract function _log($message);
	
	protected abstract function _error($exception, $message);

	protected abstract function _memory($variable, $name);

	protected abstract function _speed($name);
	
	protected abstract function _logQuery($query,$time);
	
	protected abstract function _display();
}