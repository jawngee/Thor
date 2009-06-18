<?	
uses('system.data.model');
	
/**
 * Provider Model
 *
 * Contains the following properties:
 *
 * driver - Undocumented column
 * name - Undocumented column
 * url - Undocumented column
 * manage_url - Undocumented column
 * description - Undocumented column
		
 *
 * @copyright  Copyright (c) 2007 massify.com, all rights reserved.
 */
class BaseProvider extends Model
{
	public $table_name='provider.provider';
	
	public $primary_key='id';

		public $database='default';

	/**
	* Describes the schema and validation rules for this object.  This is auto-generated.
	*/
	protected function describe()
	{
	    // describe the fields/columns
		
	    // driver - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['driver']=new Field('driver',Field::STRING,0,'Undocumented column',true);
		
	    // name - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['name']=new Field('name',Field::STRING,0,'Undocumented column',true);
		
	    // url - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['url']=new Field('url',Field::STRING,0,'Undocumented column',false);
		
	    // manage_url - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['manage_url']=new Field('manage_url',Field::STRING,0,'Undocumented column',false);
		
	    // description - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['description']=new Field('description',Field::STRING,0,'Undocumented column',false);
		

	}
}
