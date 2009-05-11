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
	
	public function delete_index($id)
	{
		$slice=new SliceResource($this->slicehost,$id);
		return array(
			'slice' => $slice
		);
	}
	
	public function index($id)
	{
		$slice=new SliceResource($this->slicehost,$id);		

		return array(
			'slice' => $slice
		);
	}
}