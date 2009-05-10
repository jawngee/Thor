<?
uses('app.lib.slicehost.slicehost');

class IndexController extends Controller
{
	public function index()
	{
		$conf=Config::Get("slicehost");
		$slicehost=new Slicehost($conf->key);
		
		return array(
			'slices' => $slicehost->slices
		);
	}
}