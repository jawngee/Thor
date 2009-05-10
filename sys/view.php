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
 * View
 */
 class View
 {
 	public $layout=null;
 	
 	private $view=null;
 	
 	public $data=array();
 	
 	protected $base_path='';
 	
	// holds a list of the control classes
	private $control_classes=array();
 	
 	/** regex that extracts the attributes */
 	const REGEX_ATTRIBUTE = '#([a-zA-Z_0-9]+)[=]+(?:\'|")([^\'"]*)(?:\'|")+#';		
 	
 	/**
 	 * Constructor
 	 * 
 	 * @param string $view The view to use for this layout
 	 */
 	public function __construct($view)
 	{
		$this->view=$view;
		$this->base_path=PATH_APP.'view/';
 	}
 	
 	/**
 	 * Parses out controls and uses tags from the view's rendered output, replacing the tags with rendered controls
 	 * 
 	 * @param string $rendered The view's rendered output that may contain control or uses tags
 	 */
 	protected function parse_layout(&$rendered)
 	{
		// extract php control includes
		$regex = '#<[\s]*uses[\s]*:[\s]*layout([^>]*?)[\s]*/[\s]*>#is';					// extracts the tag
		
		// parse the rendered view
 		$matched=array();
 		while(preg_match($regex,$rendered,$matched,PREG_OFFSET_CAPTURE)==1)
 		{
			$parsed_attr=array();
			preg_match_all(View::REGEX_ATTRIBUTE,$matched[1][0],$parsed_attr,PREG_SET_ORDER);
			
			$this->target='main';
			$title='';
			$layout=null;
			
			foreach($parsed_attr as $attr)
				switch($attr[1])
				{
					case 'target':
						$this->target=$attr[2];
						break;

					case 'layout':
						$layout=$attr[2];
						break;

					case 'title':
						$title=$attr[2];
						break;
						
					case 'description':
						$description=$attr[2];
						break;
				}

 			// we matched, so remove it from the rendered view
 			$rendered=str_replace($matched[0][0],'',$rendered);

			if ($layout!=null)
			{
 				uses("app.layout.$layout");
	 			if (class_exists(str_replace('_','',$layout).'Layout'))
	 			{
	 				$view_type=array_pop(explode('.',$this->view));
	 				
	 				$layoutclass= str_replace('_','',$layout).'Layout';
	 				$this->layout=new $layoutclass($title,$description,"$layout.$view_type");
	 			}
	 		}
 		}
 	}
 	
 	/**
 	 * Parses out controls and uses tags from the view's rendered output, replacing the tags with rendered controls
 	 * 
 	 * @param string $rendered The view's rendered output that may contain control or uses tags
 	 */
 	protected function parse_subviews(&$rendered)
 	{
		// extract php control includes
		$regex = '#<[\s]*render[\s]*:[\s]*view([^>]*?)[\s]*/[\s]*>#is';					// extracts the tag
		
		// parse the rendered view
 		$matches=array();
 		while(preg_match($regex,$rendered,$matches,PREG_OFFSET_CAPTURE)==1)
 		{
			$parsed_attr=array();
			preg_match_all(View::REGEX_ATTRIBUTE,$matches[1][0],$parsed_attr,PREG_SET_ORDER);
			
			$view=null;
			$target=null;
			foreach($parsed_attr as $attr)
			{
				switch($attr[1])
				{
					case 'view':
						$view=$attr[2];
						break;
					case 'target':
						$target=$attr[2];
						break;
					default:
						if (preg_match('#{[^}]*}#is',$attr[2]))
						{
							$key=trim(trim($attr[2],'{'),'}');
							if (isset($this->data[$key]))
								$this->data[$attr[1]]=$this->data[$key];
							else
								user_error("Cannot bind to variable '$key'.",E_USER_WARNING);
						}
						else
							$this->data[$attr[1]]=$attr[2];
						break;
				}
			}

			if ($view!=null)
			{
				$v=new View($this->parent,$view);
				$result=$v->render($this->data,true);
				
				if (($this->layout!=null) && ($target!=null))
				{
					$this->layout->add_content($target,$result);
					$rendered=str_replace($matches[0][0],'',$rendered);
				}
				else
					$rendered=str_replace($matches[0][0],$result,$rendered);
			}
			else
				$rendered=str_replace($matches[0][0],'',$rendered);
 		}
 	}
 	
 	/**
 	 * Parses out controls and uses tags from the view's rendered output, replacing the tags with rendered controls
 	 * 
 	 * @param string $rendered The view's rendered output that may contain control or uses tags
 	 */
 	protected function parse_targets(&$rendered)
 	{
		// extract php tags
		$matches=array();
		$regex="@<[\s]*render[\s]*:[\s]*target[\s]*target=['\"]([^\"']*)['\"]>(.*?)<[\s]*/[\s]*render[\s]*:[\s]*target[\s]*>@is";
 		while(preg_match($regex,$rendered,$matches,PREG_OFFSET_CAPTURE)==1)
 		{
 			$content=$matches[2][0];
 			$target=$matches[1][0];
 			
 			if ($this->layout!=null)
 				$this->layout->add_content($target,$content);

			$rendered=str_replace($matches[0][0],'',$rendered);
 		}
  	}
 	
 	/**
 	 * Parses out controls and uses tags from the view's rendered output, replacing the tags with rendered controls
 	 * 
 	 * @param string $rendered The view's rendered output that may contain control or uses tags
 	 */
 	protected function parse_includes(&$rendered)
 	{
		// extract helper code
		$matches=array();
		$regex="@<[\s]*uses[\s]*:[\s]*helper[\s]*src[\s]*=[\s]*['\"]([^\"']*)['\"][\s]*/[\s]*>@is";
 		while(preg_match($regex,$rendered,$matches,PREG_OFFSET_CAPTURE)==1)
 		{
 			uses("app.helper.".$matches[1][0]);
			$rendered=str_replace($matches[0][0],'',$rendered);
 		}
 			
 		// extract javascript includes
		$matches=array();
		$regex="@<[\s]*uses[\s]*:[\s]*include[\s]*include[\s]*=[\s]*['\"]([^\"']*)['\"][\s]*/[\s]*>@is";
 		while(preg_match($regex,$rendered,$matches,PREG_OFFSET_CAPTURE)==1)
 		{
 			$include=$matches[1][0];
 			
 			if ($this->layout!=null)
 				$this->layout->add_include($include);
 			else
 				Layout::$MasterLayout->add_include($include);

			$rendered=str_replace($matches[0][0],'',$rendered);

 		}

		// extract css style include
		$matches=array();
		$regex="@<[\s]*uses[\s]*:[\s]*style[\s]*style[\s]*=[\s]*['\"]([^\"']*)['\"][\s]*(compile\s*=\s*['\"](true|false)['\"])*\s*/[\s]*>@is";
 		while(preg_match($regex,$rendered,$matches,PREG_OFFSET_CAPTURE)==1)
 		{
 			$include=$matches[1][0];
 			$dynamic=$matches[3][0];
 			
 			if ($this->layout!=null)
 				$this->layout->add_style($include,$dynamic);

			$rendered=str_replace($matches[0][0],'',$rendered);
 		}
  	}
 	
 	/**
 	 * Parses out other tags from the view's rendered output, replacing them with the rendered tag's contents.
 	 * This should be overridden in child classes.
 	 * 
 	 * @param string $rendered The view's rendered output that may contain layout tags
 	 */
 	protected function parse_other_tags(&$rendered)	{}
 	
 	/**
 	 * Parses out ajax tags
 	 * 
 	 * TODO: This is prototype specific, need to generify.
 	 */
 	protected function parse_ajax_tags(&$rendered)
 	{
 		// extract layout tags from rendered source
 		$tags=array();
		$ajax_tags='@<[\s]*ajax[\s]*:[\s]*(\w+)([^>]*?)>(.*?)<[\s]*/[\s]*ajax:(?:\w+)[\s]*>@is';
		
 		while(preg_match($ajax_tags,$rendered,$tags,PREG_OFFSET_CAPTURE)==1)
 		{
 			$tag=$tags[1][0];
 			$full_tag=$tags[0][0];
 			$attributes=$tags[2][0];
 			$content=trim(str_replace("\r",' ',str_replace("\n",' ',str_replace('"','\"',$tags[3][0]))));
 			
			$rawattrs=array();
			$attrs=array();
			preg_match_all(View::REGEX_ATTRIBUTE,trim($attributes),$rawattrs,PREG_SET_ORDER);
			foreach($rawattrs as $attr)
			{
				if (preg_match('#{[^}]*}#is',$attr[2]))
				{
					$key=trim(trim($attr[2],'{'),'}');
					if (isset($this->data[$key]))
						$attrs[$attr[1]]=$this->data[$key];
					else
						user_error("Cannot bind to variable '$key'.",E_USER_WARNING);
				}
				else
					$attrs[$attr[1]]=$attr[2];
			}
							
			$result='';
			
			switch($tag)
 			{
 				case 'update':
					$result="Element.update(\"".$attrs['id']."\",\"".$content."\");";
 	 				break;
 				case 'replace':
					$result="Element.replace(\"".$attrs['id']."\",\"".$content."\").remove();";
 	 				break;
 	 			case 'insert':
 					$where=(isset($attrs['where'])) ? $attrs['where'] : 'before';
					$result="new Insertion.".ucfirst(strtolower($where))."(\"".$attrs['id']."\",\"".$content."\");";
	 				break;
 				case 'remove':
 					$fade=(isset($attrs['fade'])) ? $attrs['fade'] : false;
					if ($fade)
						$result='Effect.Fade("'.$attrs['id'].'",{ afterFinish:function(effect) { $(effect.element).remove(); }});';
					else
						$result='$("'.$attrs['id'].'").remove();'; 
 	 				break;
 				case 'hide':
					$result='$("'.$attrs['id'].'").hide();'; 
 	 				break;
 				case 'show':
					$result='$("'.$attrs['id'].'").show();'; 
	 				break;
 			}
 			
 			$rendered=str_replace($full_tag,$result,$rendered)."\n";
 		}
  	}

 	/**
 	 * Renders and displays the multiview.
 	 */	
 	public function render($data=null, $subview=false)
 	{
 		if ($data!=null)
 			$this->data=$data;
 		else
 			$this->data=array();
 		
 		$this->data['layout']=$this;
 		$result=render_view($this->base_path.$this->view,$this->data);

 		if ($subview==false)
 			$this->parse_layout($result);
 		
 			
 		$this->parse_includes($result);
 		$this->parse_subviews($result);
 		$this->parse_other_tags($result);
 		$this->parse_targets($result);
 		$this->parse_ajax_tags($result);

  		if ($this->layout!=null)
 		{
 			$this->layout->add_content($this->target,$result);
 			$result=$this->layout->render($data);
 			
 		}
	
 		return $result;
 	}
 }