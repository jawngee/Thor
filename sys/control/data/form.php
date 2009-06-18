<?
uses('system.app.control');
uses('system.data.model');
uses('system.app.view');
uses('system.app.template');
uses('system.request.uri');
uses('system.control.data.databound_formatter');

/**
 * Base class for databound controls, such as the datagrid and the repeater.
 */
class FormControl extends Control
{
	/**
	 * The mode of the form control editor.
	 * 
	 * @var string
	 */
	 public $mode;
	 
	 /**
	  * The form action, or URL endpoint.
	  * 
	  * @var string
	  */
	 public $action;
	 
	 /**
	  * The name of the model to create OR an instance of the model to edit.
	  * 
	  * @var mixed
	  */
	 public $model;
	 
	 public $form_template=null;
	 
 	 public $redirect=null;
 	 
 	 public $allow_create=false;
 	 
 	 public $allow_update=false;
	 
 	 public $errors=array();
 	 
 	 public $fields=array();
 	 
	/**
	 * Initializes the control
	 */
 	public function init()
	{
		parent::init();
		
		if (gettype($this->model)=='string')
			$this->model=Model::Instance($this->model);

		if (!$this->content->fields)
			$this->fields=$this->build_default_fields();
		else
		{
			foreach($this->content->fields->field as $f)
			{
				$flat=array();
				foreach($f->attributes() as $k => $v)
					$flat[(string)$k]=(string)$v;
				$flat['value']=$this->model->{$flat['id']};
				$this->fields[]=$flat;
			}
		}
		
		// auto magic	
		if ( (($this->allow_create) && ($this->controller->method=='PUT')) || (($this->allow_update) && ($this->controller->method=='POST')) )
			$this->save();			
	}
	
	private function save()
	{
		$bind=array();
		$input=Input::Post();
		
		if ($this->controller->method=='POST')
		{
			if (!$input->has($this->model->primary_key))
				return;
				
			$this->model->{$this->model->primary_key}=$input->{$this->model->primary_key};
			$this->model->reload();
		}

		foreach($this->fields as $f)
			$bind[$f['id']]=$f['id'];
			
		$this->model->bind_input($input,$bind);
		
		$errors=$this->model->save();
		
		if (is_array($errors))
			$this->errors=$errors;
		else if($this->redirect)
			redirect(sprintf($this->redirect,$this->model->id));
	}
	
	private function build_default_fields()
	{
		$related=array();
		foreach($this->model->related as $r)
			$related[$r->field]=$r;
		
		$result=array();
		foreach($this->model->fields as $field)
		{
			$f=array();

			$f['id']=$field->name;
			$f['label']=ucfirst($field->name);
			
			if (isset($related[$field->name]))
			{
				$f['type']='select';
				$f['datasource']="model://".$related[$field->name]->model;
				$f['key']=$related[$field->name]->foreign_field;
				
				// we have to guess what the display name is ...
				// if you need more control, specify the fields manually.
				$model=Model::Instance($related[$field->name]->model);
				$names=explode('.',$related[$field->name]->model);
				$f['label']=ucfirst(array_pop($names));
				
				if (isset($model->fields['name']))
					$f['field']='name';
				else if (isset($model->fields['title']))
					$f['field']='title';
				else foreach($model->fields as $field)
				{
					// grab the first string field
					if ($field->type==Field::STRING)
						$f['field']=$field->name;
				}
			}
			else
				$f['type']='text';
				
			$f['value']=$field->value;
			
			$result[]=$f;
		}
//		vomit($result);
		return $result;
	}
	
	public function build_form($edit)
	{
		
		$result='';
		
		foreach($this->fields as $field)
		{
			$label=(isset($field['label'])) ? $field['label'] : '';
			
			if ($this->mode=='view')
				$result.="<tr><td>$label</td>";
			else
				$result.="<label for='{$field['id']}'>$label</label>\n";
				
			$val=DataboundFormatter::Format('control.data.formatter.'.$field['type'],$field['value'],$field,$edit);
			
			if ($this->mode=='view')
				$result.="<td>$val</td></tr>\n";
			else
				$result.="$val<br />\n";
		}
			
		return $result;
	}
	
	public function build()
	{
		$method=($this->mode=='create') ? 'PUT' : 'POST';

		$contents=$this->build_form($this->mode!='view');

		if ($this->mode=='view')
			return "<table>$contents</table>";
			
		if ($this->mode=='edit')
			$edittag="<input type='hidden' name='{$this->model->primary_key}' value='{$this->model->primary_key_value}' >";
		else
			$edittag='';
			
		if ($this->form_template)
		{
			$f=new Template(PATH_APP.'view/'.$this->form_template.EXT);
			$form=$f->render(array('control'=>$this,'method'=>$method,'contents'=>$contents));	
		}
		else
			$form=<<<EOD
<form method='post' action='{$this->action}'>
	$edittag
	<input type="hidden" name='real_method' value='$method' />
	$contents
	<input type="submit" value="Create" />
</form>
EOD;
		return $form;
	}
}
