<?
/**
*
* Copyright (c) 2009, Jon Gilkison and Massify LLC.
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions are met:
*
* - Redistributions of source code must retain the above copyright notice,
*   this list of conditions and the following disclaimer.
* - Redistributions in binary form must reproduce the above copyright
*   notice, this list of conditions and the following disclaimer in the
*   documentation and/or other materials provided with the distribution.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
* AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
* IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
* ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
* LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
* CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
* SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
* INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
* CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
* ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
* POSSIBILITY OF SUCH DAMAGE.
*
* This is a modified BSD license (the third clause has been removed).
* The BSD license may be found here:
* 
* http://www.opensource.org/licenses/bsd-license.php
*/

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