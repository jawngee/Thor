<?
class DefaultLayout extends Layout 
{
	public function __construct($title,$description,$view)
 	{
 		parent::__construct($title,$description,$view);

 		// shared amoung all pages on the site
 		$this->add_style("reset");	     
 		$this->add_style("default");	     
 	}
}