<?	
uses('system.data.model');
	
/**
 * Account Model
 *
 * Contains the following properties:
 *
 * provider_id - Undocumented column
 * name - Undocumented column
 * notes - Undocumented column
 * key - Undocumented column
 * secret - Undocumented column
 * created - Undocumented column
		
 *
 * @copyright  Copyright (c) 2007 massify.com, all rights reserved.
 */
class BaseAccount extends Model
{
	public $table_name='provider.account';
	
	public $primary_key='id';

		public $database='default';

	/**
	* Describes the schema and validation rules for this object.  This is auto-generated.
	*/
	protected function describe()
	{
	    // describe the fields/columns
		
	    // provider_id - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['provider_id']=new Field('provider_id',Field::NUMBER,0,'Undocumented column',true);
		
	    // name - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['name']=new Field('name',Field::STRING,0,'Undocumented column',true);
		
	    // notes - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['notes']=new Field('notes',Field::STRING,0,'Undocumented column',false);
		
	    // key - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['key']=new Field('key',Field::STRING,0,'Undocumented column',false);
		
	    // secret - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['secret']=new Field('secret',Field::STRING,0,'Undocumented column',false);
		
	    // created - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['created']=new Field('created',Field::TIMESTAMP,0,'Undocumented column',false);
		

	}
}
