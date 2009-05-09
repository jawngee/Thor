<?
/**
 * Base Controller
 * Abstract controller class for all application controllers.
 */

uses('sys.input');
uses('sys.session');

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
 		$this->method=$_SERVER['REQUEST_METHOD'];
 		
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