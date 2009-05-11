<?
uses('app.lib.slicehost.slicehost');

class SliceConfiguration
{
	public $id;
	public $name='';
	public $image_desc='';
	public $image_id=null;
	public $backup_id=null;
	public $backup_desc='';
	public $flavor_id=null;
	public $flavor_desc='';
	public $notes='';
	
	public function __construct()
	{
		$this->id=uuid();
	}
	
	public static function LoadConfigurations()
	{
		$result=array();
		$path=PATH_APP.'data/configurations.data';
		if (file_exists($path))
			$confs=unserialize(file_get_contents($path));
			
		return $confs;
	}
	
	public static function SaveConfigurations($confs=null,$conf=null)
	{
		if (($confs==null) && (!is_array($confs)))
		{
			$confs=self::LoadConfigurations();
			if ($conf!=null)
				$confs[$conf->id]=$conf;
		}
		
		$path=PATH_APP.'data/configurations.data';
		file_put_contents($path,serialize($confs));
			
		return $confs;
	}
	
	public function create($name=null)
	{
		$conf=Config::Get('slicehost');
		$s=new Slicehost($conf->key);
		
		$slice=new SliceResource($s);
		
		$slice->name=($name!=null) ? $name : $this->name.' '.microtime(true);
		if ($this->image_id)
			$slice->image_id=$this->image_id;
		else if ($this->backup_id)
			$slice->backup_id=$this->backup_id;
		else
			throw new Exception("No valid image_id or backup_id has been supplied.");
		
		$slice->flavor_id=$this->flavor_id;

		$slice->save();
		
		return $slice;
	}
	
	public function save()
	{
		self::SaveConfigurations(null,$this);
	}
}