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
*
*/

/**
 * Stores session data in an encrypted cookie.
 * Session is bad, use sparingly.
 * 
 * @package		HeavyMetal
 * @category 	Application
 * @author     	Jon Gilkison <jg@massifycorp.com>
 * @copyright  	2007 Massify LLC
 */

uses('sys.utility.encrypt');
uses('sys.app.config');

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