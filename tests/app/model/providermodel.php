<?php
require_once 'PHPUnit/Framework.php';
require_once '../../../sys/sys.php';

uses('sys.app.config');
Config::LoadEnvironment('test');

uses('system.data.database');
uses('system.data.filter');

/**
 * Tests basic PGSQL Driver functionality
 */
class ProviderModel extends PHPUnit_Framework_TestCase
{
    protected $db;

    protected function setUp()
    {
    	$this->db=Database::Get('default');
		try
		{
		    $this->db->execute_file(PATH_ROOT.'sql/pgsql/blank.sql');
		}
		catch(Exception $ex)
		{
		}
	
    	$this->db->execute_file(PATH_ROOT.'sql/pgsql/schema.sql');
    }

    public function testFilterProvider()
    {
		$providers=filter('provider/provider')->find();
		$this->assertTrue(count($providers)==1);
    }

    public function testCreateAccount()
    {
		$account=Model::Instance('provider.account');
	
		$account->provider_id=1;
		$account->name="Test";
		$account->notes="";
		$account->key="junk";
		$account->secret="junk";
		$account->save();
	
		$accounts=filter('provider/account')->find();
		$this->assertTrue(count($accounts)==1);
    }

    public function testDeleteAccount()
    {
		$account=Model::Instance('provider.account');
	
		$account->provider_id=1;
		$account->name="Test";
		$account->notes="";
		$account->key="junk";
		$account->secret="junk";
		$account->save();
	
		$accounts=filter('provider/account')->find();
		$this->assertTrue(count($accounts)==1);
		
		$accounts[0]->delete();

		$accounts=filter('provider/account')->find();
		$this->assertTrue(count($accounts)==0);
    }
}
