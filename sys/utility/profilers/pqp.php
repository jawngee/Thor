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
		PQPConsole::log($message);
	}
	
	protected function _error($exception,$message)
	{
		PQPConsole::logError($exception,$message);
	}
	
	protected function _memory($variable, $name)
	{
		PQPConsole::logMemory($variable,$name);
	}

	protected function _speed($name)
	{
		PQPConsole::logSpeed($name);	
	}
	
	protected function _logQuery($query,$time)
	{
		
	}
	
	protected function _display()
	{
		return $this->instance->display();
	}
	
}