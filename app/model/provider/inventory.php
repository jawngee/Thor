<?
uses('system.data.model');
uses('model.provider.base_inventory');

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
class Inventory extends BaseInventory
{
	/**
	* Describes the schema and validation rules for this object.  This is auto-generated.
	*/
	protected function describe()
	{
	    parent::describe();

	    // create validators for columns
	    $this->validators["account_id"]=array(
		RequiredValidator::Create("account_id is required.")
	    );
	    $this->validators["server_type_id"]=array(
		RequiredValidator::Create("server_type_id is required.")
	    );
	    $this->validators["name"]=array(
		RequiredValidator::Create("name is required.")
	    );
	    $this->validators["status_id"]=array(
		RequiredValidator::Create("status_id is required.")
	    );

	    // create relations for columns
	    $this->related['ipaddresses']=new Relation($this,'ipaddresses',Relation::RELATION_MANY,'provider.ipaddress','inventory_id');
	    $this->related['account']=new Relation($this,'account',Relation::RELATION_SINGLE,'provider.account','id','account_id');
	}
}
