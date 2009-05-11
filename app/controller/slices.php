<?
uses('app.lib.slicehost.slicehost');

class SlicesController extends Controller
{
	var $slicehost=null;
	
	public function setup()
	{
		parent::setup();
		
		$conf=Config::Get("slicehost");
		$this->slicehost=new Slicehost($conf->key);
	}
	
	public function put_index($id)
	{
		$slice=new SliceResource($this->slicehost,$id);
		$slice->reboot(($this->post->action=="hard_reboot"));
		redirect("/?refresh");
	}
	
	public function delete_index($id)
	{
		$slice=new SliceResource($this->slicehost,$id);
		$slice->delete();
		redirect("/?refresh");
	}
	
	public function index($id)
	{
		$slice=new SliceResource($this->slicehost,$id);		

		Profiler::Log($slice);
		
		return array(
			'slice' => $slice,
			'images' => $this->slicehost->images,
			'backups' => $this->slicehost->backups,
			'flavors' => $this->slicehost->flavors
		);
	}
}