<?
uses('vendor.pqp.PhpQuickProfiler');

class PQPProfiler extends Profiler
{
	private $instance=null;
	
	public function __construct()
	{
		$this->instance=new PhpQuickProfiler(PhpQuickProfiler::getMicroTime());
	}
	
	protected function _log($message)
	{
		Console::log($message);
	}
	
	protected function _error($exception,$message)
	{
		Console::logError($exception,$message);
	}
	
	protected function _memory($variable, $name)
	{
		Console::logMemory($variable,$name);
	}

	protected function _speed($name)
	{
		Console::logSpeed($name);	
	}
	
	protected function _logQuery($query,$time)
	{
		
	}
	
	protected function _display()
	{
		return $this->instance->display();
	}
	
}