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
 * Base Controller
 * Abstract controller class for all application controllers.
 */

uses('sys.request.input');
uses('sys.app.session');
uses('sys.request.uri');

/**
 * Abstract controller class inherited by all application controllers.
 */
 abstract class Controller
 {
   	/** Ignored methods **/
  	public $ignored=array('datasource','setup','reroute','slingback');
 	
 	/** Contains post-path uri segments and query values */
 	public $uri=null;
 	
 	/** Current session **/
 	public $session=null;
 	
 	/** Stores the input instance for post variables */
 	public $post=null;
 	
 	/** Stores the input instance for query string variables */
 	public $get=null;
 	
 	/** Stores the input instance for uploaded files */
 	public $files=null;
 	
 	/**
 	 * Request method: GET, PUT, POST, DELETE, etc.
 	 *
 	 * @var string
 	 */
 	public $method=null;
 	
 	
	/**
	 * Constructor
	 * 
	 * @param string $root The root uri path
	 * @param array $segments The uri segments following the root path
	 */
 	public function __construct($root,$segments)
 	{
 		$this->session=Session::Get();
 		
 		$this->uri=new URI($root,$segments);
 		$this->query=new Query();
 		
 		// assign the get and post vars
 		$this->post=Input::Post();
 		$this->get=Input::Get();
 		$this->files=Input::Files();
 		
 		$this->setup();
 	}
 	
 	/** Performs controller specific setup */
 	protected function setup()
 	{
 	}
 	
 	
 	/**
 	 * Returns to the referring URL
 	 */
 	public function slingback($or_else='/')
 	{
 		if ((isset($_SERVER['HTTP_REFERER'])) && ($_SERVER['HTTP_REFERER']!=null))
 			return redirect($_SERVER['HTTP_REFERER']);
 			
 		return redirect($or_else);
 	}

 	public function report_invalid_parameters()
	{
 		ob_end_clean();
 		header('HTTP/1.0 400 Bad Request');
 		header('Status: 400 Bad Request');
 		print('Bad Request');
 		die;
	}
 	
 	
 	public function report_not_found()
 	{
 		ob_end_clean();
 		header('HTTP/1.1 404 Not Found');
 		header('Status: 404 Not Found');
 		die;
 	}

  	public function report_bad_request()
 	{
 		ob_end_clean();
 		header('HTTP/1.0 400 Bad Request');
 		header('Status: 400 Bad Request');
 		print('Bad Request');
 		die;
  	}
 
  	public function report_gone()
 	{
 		ob_end_clean();
 		header('HTTP/1.0 410 Gone');
 		header('Status: 410 Gone');
 		print('Gone.');
 		die;
  	}

   	public function report_server_error()
 	{
 		ob_end_clean();
 		header('HTTP/1.0 500 Internal Server Error');
 		header('Status: 500 Internal Server Error');
 		print('Internal Server Error');
 		die;
  	}
}