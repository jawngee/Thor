<?
uses('system.app.control');
uses('system.data.channel');
uses('system.app.view');
uses('system.app.template');
uses('system.request.uri');

/**
 * Base class for databound controls, such as the datagrid and the repeater.
 */
abstract class DataboundControl extends Control
{
	/**
	 * Determines if pagination should be turned on or not.
	 *
	 * @var bool
	 */
	public $paging=true;				

	/**
	 * The number of items per page.
	 *
	 * @var int
	 */
	public $page_size=0;
	
	/**
	 * The current page number.
	 *
	 * @var int
	 */
	public $current_page=0;

	/**
	 * This can either be the uri query or a variable bound in the view.
	 * 
	 * For uri queries, the following schemes are supported: controller, channel, model.
	 * 
	 * The queries will look something like:
	 * 
	 * controller://path/path#results?arg1=val&q=asdads asd ad ad&arg=[123,232,123]
	 * channel://channel/datasource?arg1=val&q=asdads asd ad ad&arg=[123,232,123]
	 * model://profiles/profile_view?arg1!=val&q=asdads asd ad ad&arg=[123,232,123]
	 *
	 * @var mixed
	 */
	public $datasource=null;
	
	/**
	 * Total number of items in the datasource
	 *
	 * @var int
	 */
	public $total_count=null;
	
	/**
	 * Total number of items rendered
	 *
	 * @var int
	 */
 	public $count=0;
 	
 	
	public $lowerBound=0;
	public $upperBound=0;
	public $pageno=0;
	public $lastpage=0;

	/**
	 * The template to use for pagination.
	 *
	 * @var unknown_type
	 */
	public $pagination_template='global/search/pagination';
	
	/**
	 * Initializes the control
	 */
 	public function init()
	{
		parent::init();
		
		$this->uri=$this->controller->uri;

		if ($this->paging)
		{		
			if ($this->uri->query->get_number($this->id.'_pg'))
				$this->current_page=$this->uri->query->get_number($this->id.'_pg');
	
			if ($this->current_page==0)
				$this->uri->query->remove_value($this->id.'_pg');
	    }
	}
	

	/**
	 * Generates the next page link
	 *
	 * @return string
	 */
	function next_page_link()
 	{
 		return $this->uri->build(null,array($this->id.'_pg' => $this->current_page+1));
 	}

 	/**
 	 * Generates the previous page link
 	 *
 	 * @return string
 	 */
 	function prev_page_link()
 	{
	 		return $this->uri->build(null,array($this->id.'_pg' => $this->current_page-1));
 	}
	
 	/**
 	 * Generates the page link.
 	 *
 	 * @param int $page
 	 * @return string
 	 */
 	function page_link($page)
 	{
		return $this->uri->build(null,array($this->id.'_pg' => $page));
 	}


 	 	
	/**
	 * Fetches the data
	 *
	 * @return mixed The data from the datasource
	 */
	protected function get_data($order_by=null, $dir='asc')
	{
		$rows=null;
		if (gettype($this->datasource)=='string')
		{
			if (strpos($this->datasource,'://')>1)
			{			
				$ds=$this->datasource;
				if ($order_by)
				{
					if (!strpos($ds,'?'))
						$ds.='?';
					else
						$ds.='&';
					$ds.='order by '.$order_by.' '.$dir;
				}
				
				$rows=Channel::GetDatasource($ds,$this->current_page*$this->page_size,$this->page_size,$this->total_count);
			}
			else
			{
				user_error('Using datasources on controllers is deprecated',E_USER_WARNING);
				$rows=$this->controller->datasource($this->datasource,$this->current_page*$this->page_size,$this->page_size,$this->total_count);
			}
		}
		else
		{
			$rows=$this->datasource;
		}
			
		return $rows;
	}
}
