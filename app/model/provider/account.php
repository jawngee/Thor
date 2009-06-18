<?
uses('system.data.model');
uses('model.provider.base_account');

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
class Account extends BaseAccount
{
	/**
	* Describes the schema and validation rules for this object.  This is auto-generated.
	*/
	protected function describe()
	{
	    parent::describe();

	    // create validators for columns
	    $this->validators["provider_id"]=array(
		RequiredValidator::Create("provider_id is required.")
	    );
	    $this->validators["name"]=array(
		RequiredValidator::Create("name is required.")
	    );

	    // create relations for columns
	    $this->related['inventory']=new Relation($this,'inventory',Relation::RELATION_MANY,'provider.inventory','account_id');
	    $this->related['keys']=new Relation($this,'keys',Relation::RELATION_MANY,'provider.keys','account_id');
	    $this->related['provider']=new Relation($this,'provider',Relation::RELATION_SINGLE,'provider.provider','id','provider_id');
	}
}
