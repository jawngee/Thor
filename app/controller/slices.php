<?
uses('app.lib.slicehost.slicehost');
uses('app.lib.controllers.slicehost');

class SlicesController extends SlicehostController
{
	public function put_index($id)
	{
		$slice=new SliceResource($this->slicehost,$id);
		switch($this->post->action)
		{
			case "reboot":
			case "hard_reboot":
				$slice->reboot(($this->post->action=="hard_reboot"));
				redirect("/slices/$id");
				break;
			case "image_rebuild":
				$slice->rebuild($this->post->image_id);
				break;
			case "backup_rebuild":
				$slice->rebuild(null,$this->post->backup_id);
				break;
		}
	
		return array(
			'slice' => $slice,
			'slices' => null,
			'images' => $this->slicehost->images,
			'backups' => $this->slicehost->backups,
			'flavors' => $this->slicehost->flavors
		);
	}
	
	public function delete_index($id)
	{
		$slice=new SliceResource($this->slicehost,$id);
		$slice->delete();
		redirect("/slices/?refresh");
	}
	
	public function index($id=null)
	{
		if ($id==null)
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
				'slice' => null,
				'slices' => $slices
			);
		}
		
		
		$slice=new SliceResource($this->slicehost,$id);		

		return array(
			'slice' => $slice,
			'slices' => null,
			'images' => $this->slicehost->images,
			'backups' => $this->slicehost->backups,
			'flavors' => $this->slicehost->flavors
		);
	}
}