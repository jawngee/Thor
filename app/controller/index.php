<?
uses('app.lib.slicehost.slicehost');
uses('app.lib.controllers.slicehost');

class IndexController extends SlicehostController
{
	public function index()
	{
		
		$data=PATH_APP.'data/inventory.data';
		
		$maxtime=(isset($_GET['refresh'])) ? -300 : 300;
		if ((file_exists($data)) && (fileatime($data)>(time()-$maxtime)))
			$slices=unserialize(file_get_contents($data));
		else
		{
			$slices=$this->slicehost->slices;
			file_put_contents($data,serialize($slices));
		}
		
		return array(
			'slices' => $slices
		);
	}
}