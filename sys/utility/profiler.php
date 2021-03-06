<?
/**
*
* Copyright (c) 2009, Jon Gilkison and Massify LLC.
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions are met:
*
* - Redistributions of source code must retain the above copyright notice,
*   this list of conditions and the following disclaimer.
* - Redistributions in binary form must reproduce the above copyright
*   notice, this list of conditions and the following disclaimer in the
*   documentation and/or other materials provided with the distribution.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
* AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
* IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
* ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
* LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
* CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
* SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
* INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
* CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
* ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
* POSSIBILITY OF SUCH DAMAGE.
*
* This is a modified BSD license (the third clause has been removed).
* The BSD license may be found here:
* 
* http://www.opensource.org/licenses/bsd-license.php
*
*/

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