<?
class SliceController extends Controller
{
	public function index($id=null)
	{
		Dispatcher::Dispatch("/slices/$id",PATH_APP.'controller/',PATH_APP.'view/','txt');
	}
	
	public function configs()
	{
		Dispatcher::Dispatch("/configs",PATH_APP.'controller/',PATH_APP.'view/','txt');
	}
	
	public function create($id)
	{
		Dispatcher::Dispatch("/configs/put_index/$id",PATH_APP.'controller/',PATH_APP.'view/','txt');
	}
}