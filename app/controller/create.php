<?
uses('app.lib.slicehost.slicehost');

class CreateController extends Controller
{
	public function index()
	{
		$conf=Config::Get("slicehost");
		$slicehost=new Slicehost($conf->key);
		return array(
		);
	}
}