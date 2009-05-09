<?
class Upload
{
	public $name='';
	public $type='';
	public $tmp_name='';
	public $error='';
	public $error_message='';
	public $size='';
	
	public function __construct($name,$type,$tmp_name,$error,$size)
	{
		$this->name=$name;
		$this->type=$type;
		$this->tmp_name=$tmp_name;
		$this->error=$error;
		
		switch($error)
		{
			case 1:
			case 2:
				$this->error_message='File too large.';
				break;
			case 3:
				$this->error_message='Partial upload.';
				break;
			case 4:
				$this->error_message='No file uploaded.';
				break;
			case 6:
				$this->error_message='Missing a temporary folder.';
				break;
			case 7:
				$this->error_message='Cannot write to disk.';
				break;
			case 8:
				$this->error_message='File upload stopped.';
				break;
		}
				
		$this->size=$size;
	}
	
	public static function GetFiles()
	{
		$files=array();
		
		foreach ($_FILES as $key => $file)
		{
			if (is_array($file['name']))
			{
				$count=count($file['name']);
				for($i=0; $i<$count; $i++)
					$files[$key][]=new Upload($file['name'][$i],$file['type'][$i],$file['tmp_name'][$i],$file['error'][$i],$file['size'][$i]);
			}
			else
				$files[$key][]=new Upload($file['name'],$file['type'],$file['tmp_name'],$file['error'],$file['size']);
		}
		
			
		return $files;
	}

	public function move()
	{
		$fname=PATH_TEMP.$this->name;
		if (!move_uploaded_file($this->tmp_name,$fname))
			throw new Exception("Could not move temporary file to '$fname'.");
			
		if (!chmod($fname,0666))
			throw new Exception("Could not set permissions on '$fname'.");

		return $fname;
	}
	
	public function extract()
	{
		$files=array();
		
	 	if (get_extension($this->name)=='zip')
 		{
			$conf=Config::Get('zip');
			
 			$orig_file=$this->move();
 			$zip=zip_open($orig_file);
			if ($zip) 
			{
    			while ($zip_entry = zip_read($zip)) 
	    			if (zip_entry_filesize($zip_entry)>0)
    				{
			        	$name=zip_entry_name($zip_entry);
			        	$extension=get_extension($name);
			        	
						if ((in_array($extension,$conf->valid_extensions->items)) && (zip_entry_open($zip, $zip_entry, "r"))) 
						{
							$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
							$fname=PATH_TEMP.md5($name.time()).'.'.$extension;
							file_put_contents($fname,$buf);
							chmod($fname,0666);
							zip_entry_close($zip_entry);
							
							$files[]=$fname;
						}
		    		}
			}
 		
 			zip_close($zip);
 		}
 		
 		return array_reverse($files);
	}
}