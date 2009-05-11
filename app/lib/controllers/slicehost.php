<?
uses('app.lib.slicehost.slicehost');

class SlicehostController extends Controller
{
	protected $slicehost=null;
	
	public function setup()
	{
		parent::setup();
		
		$conf=Config::Get('slicehost');
		$this->slicehost=new Slicehost($conf->key);
	}
}