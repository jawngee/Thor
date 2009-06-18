<?
/**
 * The base class for all UI controls.
 * 
 * @package		HeavyMetal
 * @category 	Application
 * @author     	Jon Gilkison <jg@massifycorp.com>
 * @copyright  	2007 Massify LLC
 */

uses('system.app.controller');
uses('system.app.layout');
uses('system.app.session');

/**
 * Control is the base class for all ui controls.
 */
 abstract class Control
 {
  	/** ID of the control */
 	public $id='';

	/** Controls visibility of control */
 	public $visible=true;

 	/** The owning layout */
 	public $layout=null;
 	
 	/** The owning controller */
 	public $controller=null;
 	
 	/** Parsed sub content */
 	protected $content=null;
 	
 	/** Session **/
 	public $session=null;
 	
 	/** URI **/
 	protected $uri=null;
 	
 	/** Stores the attributes passed in from the view */
 	public $attributes=array();
 	
	/** 
	 * Constructor
	 * 
	 * @param Component Parent component, null if none.
	 */
	public function __construct(View $view=null, $content=null)
	{
		$this->view=$view;
		if ($view)
			$this->controller=$view->controller;
			
		$this->session=Session::Get();
		$this->content=$content;
	}
	
 	/**
 	 * Called after all controls have been loaded by the parent.
 	 */
	public function init()
	{
		$this->layout=Layout::MasterLayout();
	}

	/**
	 * Builds the control's content
	 * 
	 * @return string The built content
	 */
	abstract function build();
	
	
	/**
	 * Renders the control by building it
	 * 
	 * @return string The rendered control
	 */
 	public function render()
 	{
 		if ($this->visible==false)
 			return '';
 			
	 	$result=$this->build();

	 	return $result;
 	}
 	
 	/**
 	 * Renders and displays the control
 	 */
 	public function display()
 	{
 		echo $this->render();
 	}
  }