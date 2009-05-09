<?
/**
 * Stores session data in an encrypted cookie.
 * Session is bad, use sparingly.
 * 
 * @package		HeavyMetal
 * @category 	Application
 * @author     	Jon Gilkison <jg@massifycorp.com>
 * @copyright  	2007 Massify LLC
 */

uses_system('sys.encrypt');
uses_system('sys.config');

/**
 * Stores session data in encrypted cookie.
 */
class Session
{
	// static instance
	private static $_instance=null;
	private static $_config=null;
	
	// session data
	public $data=array();
	
	/**
	 * Constructor
	 */
	private function __construct()
	{
		$this->load_ticket();
	}
	
	/**
	 * Fetches the current session.
	 * 
	 * @return Session The current session instance.
	 */
	public static function Get()
	{
		if (self::$_config==null)
			self::$_config=Config::Get('session');
			
		if (self::$_instance==null)
			self::$_instance=new Session();
		
		return self::$_instance;
	}

   
	/**
	*  Callback method for getting a property
	*/
   	function __get($prop_name)
   	{
		if (isset($this->data[$prop_name]))
           return $this->data[$prop_name];
		else
			return null;
   	}

   	/**
   	*  Callback method for setting a property
   	*/
   	function __set($prop_name, $prop_value)
   	{
   		if (($prop_value==null) && (isset($this->data[$prop_name])))
   			unset($this->data[$prop_name]);
   		else
			$this->data[$prop_name] = $prop_value;
			
		return true;
   	}
	
	/**
	 * Generates the auth ticket and auto login cookies for a validated user
	 * 
	 * @param bool $remember_login Should the user's login info be remembered for auto-login?
	 */
	protected function build_ticket()
	{
		$ticket=serialize($this->data)."|@@!@@|".(time()+20);

		$encrypter=new Encrypt();
		
		// encrypt the auth ticket and store it
		$ticket=$encrypter->encode($ticket);
		
		// auth ticket is good for 20 minutes to prevent spoofing.
		set_cookie(self::$_config->auth_ticket_cookie,$ticket,self::$_config->auth_ticket_duration,self::$_config->auth_cookie_domain);
	}

	/**
	 * Generates the auth ticket and auto login cookies for a validated user
	 * 
	 * @param bool $remember_login Should the user's login info be remembered for auto-login?
	 */
	protected function load_ticket($ticket=null)
	{
		// get the ticket cookie
		if ($ticket==null)
			$ticket=get_cookie(self::$_config->auth_ticket_cookie);
		
		if ($ticket==false)
			return;

		$encrypter=new Encrypt();
		// decrypt the auth ticket
		
		$ass=$encrypter->decode($ticket);

		$content=explode("|@@!@@|",$ass);
		if (count($content)>1)
			$this->data=unserialize($content[0]);
	}
	
	/**
	 * Saves the current session
	 */
	public function save()
	{
		$this->build_ticket();
	}
	
	/**
	 * Deletes the current session
	 */
	public function delete()
	{
		$this->data=array();
		delete_cookie(self::$_config->auth_ticket_cookie);
	}

	/**
	 * Returns the current session as an encrypted string.
	 *
	 * @return unknown
	 */
	public function to_string()
	{
		$ticket=serialize($this->data)."|@@!@@|".(time()+20);

		$encrypter=new Encrypt();
		
		// encrypt the auth ticket and store it
		return $encrypter->encode($ticket);
	}
	
	/**
	 * Loads session from an encrypted string
	 *
	 * @param unknown_type $ticket
	 */
	public function from_string($ticket)
	{
		$this->load_ticket($ticket);
	}
}