<?
uses('system.data.model');
uses('model.provider.base_provider');

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
class Provider extends BaseProvider
{
	/**
	* Describes the schema and validation rules for this object.  This is auto-generated.
	*/
	protected function describe()
	{
	    parent::describe();

	    // create validators for columns
	    $this->validators["driver"]=array(
		RequiredValidator::Create("driver is required.")
	    );
	    $this->validators["name"]=array(
		RequiredValidator::Create("name is required.")
	    );

	    // create relations for columns
	    $this->related['accounts']=new Relation($this,'accounts',Relation::RELATION_MANY,'provider.account','provider_id');
	}
}
