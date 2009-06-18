<?	
uses('system.data.model');
	
/**
 * Keys Model
 *
 * Contains the following properties:
 *
 * account_id - Undocumented column
 * text_id - Undocumented column
 * name - Undocumented column
 * notes - Undocumented column
 * public_filename - Undocumented column
 * private_filename - Undocumented column
 * created - Undocumented column
		
 *
 * @copyright  Copyright (c) 2007 massify.com, all rights reserved.
 */
class BaseKeys extends Model
{
	public $table_name='provider.keys';
	
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
		
	    // text_id - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['text_id']=new Field('text_id',Field::STRING,0,'Undocumented column',true);
		
	    // name - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['name']=new Field('name',Field::STRING,0,'Undocumented column',true);
		
	    // notes - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['notes']=new Field('notes',Field::STRING,0,'Undocumented column',false);
		
	    // public_filename - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['public_filename']=new Field('public_filename',Field::STRING,0,'Undocumented column',false);
		
	    // private_filename - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['private_filename']=new Field('private_filename',Field::STRING,0,'Undocumented column',false);
		
	    // created - TODO: DOCUMENT YOUR DATABASE DUDE
	    $this->fields['created']=new Field('created',Field::TIMESTAMP,0,'Undocumented column',false);
		

	}
}
