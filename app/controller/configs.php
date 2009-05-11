<?
uses('app.model.slicehost.slice_configuration');
uses('app.lib.controllers.slicehost');

class ConfigsController extends SlicehostController
{
	
	public function index()
	{
		$configs=SliceConfiguration::LoadConfigurations();
			
		return array(
			"configs" => $configs
		);
	}
	
	public function delete_index($id)
	{
		$configs=SliceConfiguration::LoadConfigurations();
		
		if (isset($configs[$id]))
		{
			unset($configs[$id]);
			SliceConfiguration::SaveConfigurations($configs);
		}
		
		redirect('/configs');
	}
	
	public function put_index($id)
	{
		$configs=SliceConfiguration::LoadConfigurations();
		
		if (isset($configs[$id]))
		{
			$configs[$id]->create();
			redirect('/?refresh');
		}
	
		redirect('/configs');
	}
	
	public function create()
	{
		return array(
			'images' => $this->slicehost->images,
			'flavors' => $this->slicehost->flavors,
			'backups' => $this->slicehost->backups
		);
	}
	
	public function put_create()
	{
		$errors=array();
		
		$images = $this->slicehost->images;
		$flavors = $this->slicehost->flavors;
		$backups = $this->slicehost->backups;
		
		
		if (!$this->post->name)
			$errors['name']='Name is required.';
			
		if (($this->post->image_id==$this->post->backup_id) || (($this->post->image_id!='none')&&($this->post->backup_id!='none')))
		{ 
			$errors['image_id']='Please select an image or a backup.';
			$errors['backup_id']='Please select an image or a backup.';
		}
		
		if (count($errors)==0)
		{
			$c=new SliceConfiguration();
			$c->name=$this->post->name;
			
			$c->image_desc='None';
			if ($this->post->image_id!='none')
			{
				$c->image_id=$this->post->image_id;
				foreach($images as $image)
					if ($image->id==$this->post->image_id)
					{
						$c->image_desc=$image->name;
						break;
					}
			}
			
			$c->backup_desc='None';
			
			if ($this->post->backup_id)
			{
				$c->backup_id=$this->post->backup_id;
				foreach($backups as $backup)
					if ($backup->id==$this->post->backup_id)
					{
						$c->backup_desc=$backup->name;
						break;
					}
			}
			
			$c->flavor_id=$this->post->flavor_id;
			foreach($flavors as $flavor)
				if ($flavor->id==$this->post->flavor_id)
				{
					$c->flavor_desc=$flavor->name;
					break;
				}
			
			$c->notes=$this->post->notes;
			
			$c->save();
			
			redirect('/configs');
		}
		
		return array(
			'errors' => $errors,
			'images' => $this->slicehost->images,
			'flavors' => $this->slicehost->flavors,
			'backups' => $this->slicehost->backups
		);
	}

}