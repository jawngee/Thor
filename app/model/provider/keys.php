<?
uses('system.data.model');
uses('model.provider.base_keys');

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
class Keys extends BaseKeys
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
	    $this->validators["text_id"]=array(
		RequiredValidator::Create("text_id is required.")
	    );
	    $this->validators["name"]=array(
		RequiredValidator::Create("name is required.")
	    );

	    // create relations for columns
	    $this->related['account']=new Relation($this,'account',Relation::RELATION_SINGLE,'provider.account','id','account_id');
	}
}
