<?
uses('app.lib.slicehost.slicehost');

class IndexController extends Controller
{
	public function index()
	{
		$conf=Config::Get("slicehost");
		$slicehost=new Slicehost($conf->key);
		
		
		$data=PATH_APP.'data/inventory.data';
		
		$maxtime=(isset($_GET['refresh'])) ? -300 : 300;
		if ((file_exists($data)) && (fileatime($data)>(time()-$maxtime)))
			$slices=unserialize(file_get_contents($data));
		else
		{
			$slices=$slicehost->slices;
			file_put_contents($data,serialize($slices));
		}
		
		return array(
			'slices' => $slices
		);
	}
}