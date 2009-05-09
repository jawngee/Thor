<?
/**
 * Configuration Reader.  Config files are stored as json or yaml
 * 
 * Usage is:
 * 
 * $config=Config::Get('nameofconfig');
 */
 class Config
 {
 	/** Array of configuration items */
 	public $items=null;
 	
 	/** Static array of preloaded configurations */
 	private static $_configs=array();
 	
 	/**
 	 * Constructor
 	 */
 	public function __construct($config)
 	{
 		$this->items=$config;
 		
		foreach($config as $key => $item)
			if (is_array($item))
				$this->items[$key]=new Config($item);
 	}
 	
 	/**
	*  Callback method for getting a property
	*/
   	function __get($prop_name)
   	{
       if (isset($this->items[$prop_name]))
           return $this->items[$prop_name];
   	}

	/**
	 * Loads a configuration, or fetches a pre-loaded one from the cache.
	 * 
	 * @param string $what The name of the configuration to load.
	 */
 	public static function Get($what)
 	{	
 		if (isset(self::$_configs[$what]))
 			return self::$_configs[$what];
 		else
 		{
 			$filename=PATH_CONFIG.$what;
 			
 			if (file_exists($filename.'.js'))
				$data=json_decode(file_get_contents($filename.'.js'),true);
			else if (file_exists($filename.'.conf'))
				$data=syck_load(file_get_contents($filename.'.conf'));
			else if (file_exists($filename.'.php'))
			{
				ob_start();
				$data = include($filename.$ext);
				ob_get_clean();
			}

			if (!is_array($data))
				throw new Exception("Invalid Config File '$what'.");
 			
			$conf=new Config($data);
 			self::$_configs[$what]=$conf;
 			
 			return $conf;
 		}
 	}
 }