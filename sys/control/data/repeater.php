<?
/**
 * Repeater Control
 *
 * @author		user
 * @date		Jun 13, 2007
 * @time		12:45:16 AM
 * @file		repeater.php
 * @copyright  Copyright (c) 2007 massify.com, all rights reserved.
 */


uses('system.control.data.databound_control');

class RepeaterControl extends DataboundControl
{
	public $item_template=null; 			/** item template */
	public $container_template=null;		/** container template */
	public $editing=false;					/** Is in edit mode */
	
	/*
	 * Data returned from the datasource
	 * 
	 */
	public $rows=null;

    /*
     * store references to current items
     */
    public $current=null;
    public $current_index=0;

    /**
	 * Builds the control
	 *
	 * @return string
	 */
	public function build()
	{
		$result='';

		$this->rows=$this->get_data();		

		$rendered='';
		$this->count=0;		

		if (($this->rows!=null) && ($this->item_template!=null))
		{
			$template=new Template(PATH_APP.'view/'.$this->item_template.EXT);

			foreach($this->rows as $row)
			{
				$rendered.=$template->render(array('item' => $row, 'control' => $this, 'count' => $this->count, 'total_count'=>$this->total_count));
				
				$this->current=&$row;
				$this->current_index=$this->count++;
			}
		}

		if ($this->container_template!=null)
		{
			$view=new Template(PATH_APP.'view/'.$this->container_template.EXT);
			$result=$view->render(array('total_count'=>$this->total_count, 'count'=>$this->count, 'control' => $this, 'content' => $rendered));
		}
		else
			$result=$rendered;

		return $result;
	}
}