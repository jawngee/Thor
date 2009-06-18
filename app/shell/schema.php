<?
uses('system.data.database');

class SchemaController extends Controller
{
	public function index($database,$schema=null,$table=null)
	{
		$db=Database::Get($database);
		
		if (strpos($schema,'.')>0)
		{
			$name=explode('.',$schema);
			$schema=$name[0];
			$table=$name[1];
		}
		
		if ($schema==null)
			return array('schemas'=>$db->schemas()->to_array());
		else if ($table==null)
			return array('tables'=>$db->tables($schema));
		else 
			return array('table'=>$db->table($schema,$table,true,false));
	}
}