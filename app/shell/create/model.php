<?
uses('system.data.database');

class ModelController extends Controller
{
    public function index($database,$table)
    {
	$db=Database::Get($database);

	$name=explode('.',$table);
	$schema=$name[0];
	$table=$name[1];

	$tableschema=$db->table($schema,$table,true,false);

	$classname='';
	$names=explode('_',$table);
	$filename='';
	foreach($names as $name)
	{
	    $filename.=strtolower($name).'_';
	    $classname.=ucfirst($name);
	}

	$filename=trim($name,'_');

	$data=array(
	    'classname' => $classname,
	    'schema' => $schema,
	    'filename' => $filename,
	    'table' => $table,
	    'database' => $database,
	    'tableschema' => $tableschema
	);


	$view=new View(PATH_APP.'view/shell/', 'create/model/base.txt');
	$base=$view->render($data);

	$view=new View(PATH_APP.'view/shell/', 'create/model/child.txt');
	$child=$view->render($data);

	$path=PATH_APP."model/$schema/";
	if (!file_exists($path))
	    mkdir($path,0777,true);

	$data['base']=$path.'base_'.$filename.EXT;
	file_put_contents($path.'base_'.$filename.EXT, $base);
	
	if ((!file_exists($path.$filename.EXT)) || ((file_exists($path.$filename.EXT)) && ($this->post->overwrite)))
	{
	    file_put_contents($path.$filename.EXT, $child);
	    $data['child']=$path.$filename.EXT;
	}

	return $data;
    }
}