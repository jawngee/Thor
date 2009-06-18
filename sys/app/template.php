<?
/**
 * A "buffered" view.  Useful for rendering a view repeatedly as it only reads the file from disk once.
 * 
 * @package		HeavyMetal
 * @category 	Application
 * @author     	Jon Gilkison <jg@massifycorp.com>
 * @copyright  	2007 Massify LLC
 */
class Template
{
	private $view_contents=null;
	public $data=null;
	
	/**
	 * Constructor
	 * 
	 * @param string $view The name of the view to use as the template.
	 */
	public function __construct($view)
	{
		if (!file_exists($view))
			throw new Exception("View file '$view' does not exist.");

		$contents=preg_replace("|{{([^}]*)}}|m",'<?=$1?>',file_get_contents($view));
	
		$this->view_contents=$contents;
	}
	
	/**
	 * Renders the template.
	 */
	public function render($data)
	{
		extract($data);
		ob_start();
		eval("?>".$this->view_contents);
		$result=ob_get_contents();
		@ob_end_clean();
		
		return $result;
	}
}