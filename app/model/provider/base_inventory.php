<?	
uses('system.data.model');
	
/**
 * Inventory Model
 *
 * Contains the following properties:
 *
 * account_id - Undocumented column
 * server_type_id - Undocumented column
 * name - Undocumented column
 * notes - Undocumented column
 * root_password - Undocumented column
 * status_id - Undocumented column
 * progress - Undocumented column
 * server_size - Undocumented column
 * base_image - Undocumented column
 * backup_name - Undocumented column
 * driver_data - Undocumented column
 * created - Undocumented column
		
 *
 * @copyright  Copyright (c) 2007 massify.com, all rights reserved.
 */
class BaseInventory extends Model
{
	public $table_name='provider.inventory';
	
	public $primary_key='id';

		public $database='default';

	/**
	* Describes the schema and validation rules for this object.  This is auto-generated.
	*/
	protected function describe()
	{
	    // describe the fields/columns
		
	    // account_id - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['account_id']=new Field('account_id',Field::NUMBER,0,'Undocumented column',true);
		
	    // server_type_id - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['server_type_id']=new Field('server_type_id',Field::NUMBER,0,'Undocumented column',true);
		
	    // name - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['name']=new Field('name',Field::STRING,0,'Undocumented column',true);
		
	    // notes - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['notes']=new Field('notes',Field::STRING,0,'Undocumented column',false);
		
	    // root_password - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['root_password']=new Field('root_password',Field::STRING,0,'Undocumented column',false);
		
	    // status_id - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['status_id']=new Field('status_id',Field::NUMBER,0,'Undocumented column',true);
		
	    // progress - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['progress']=new Field('progress',Field::NUMBER,0,'Undocumented column',false);
		
	    // server_size - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['server_size']=new Field('server_size',Field::STRING,0,'Undocumented column',false);
		
	    // base_image - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['base_image']=new Field('base_image',Field::STRING,0,'Undocumented column',false);
		
	    // backup_name - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['backup_name']=new Field('backup_name',Field::STRING,0,'Undocumented column',false);
		
	    // driver_data - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['driver_data']=new Field('driver_data',Field::STRING,0,'Undocumented column',false);
		
	    // created - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['created']=new Field('created',Field::TIMESTAMP,0,'Undocumented column',false);
		

	}
}
