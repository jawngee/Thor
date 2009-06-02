<?php
require_once 'PHPUnit/Framework.php';
require_once '../../../sys/sys.php';

uses('sys.app.config');
Config::LoadEnvironment('test');

uses('system.data.database');

/**
 * Tests basic PGSQL Driver functionality
 */
class PGSQL extends PHPUnit_Framework_TestCase
{
    protected $db;

    protected function setUp()
    {
    	$this->db=Database::Get('default');
    	$this->db->execute_file(PATH_ROOT.'sql/pgsql/test.sql');
    }

    public function testGetDatabase()
    {
    	$this->assertNotNull($this->db);
    }

    public function testInsert()
    {
		
		$id=$this->db->insert('test.test','id',array('intv'=>12,'floatv'=>1234.34,'stringv'=>'neat','textv'=>'cat', 'boolv'=>'false','datev'=>'12/12/2009'));
		$this->assertEquals($id,1);
    }

    public function testUpdate()
    {
		$id=$this->db->insert('test.test','id',array('intv'=>12,'floatv'=>1234.34,'stringv'=>'neat','textv'=>'cat', 'boolv'=>'false','datev'=>'12/12/2009'));
		$this->assertEquals($id,1);
		
		$this->db->update('test.test','id',1,array('intv'=>12,'floatv'=>1234.34,'stringv'=>'neat','textv'=>'cat', 'boolv'=>'false','datev'=>'12/12/2009'));
    }

    public function testUpdateException()
    {
		try
		{
			$this->db->update('test.test','id',2,array('intv'=>12,'floatv'=>1234.34,'stringv'=>'neat','textv'=>'cat', 'boolv'=>'false','datev'=>'12/12/2009'));
		} 
		catch (DatabaseException $ex)
		{
			$this->assertTrue(true);
		}

		$this->assertTrue(false); 
    }

    public function testCount()
    {
		$id=$this->db->insert('test.test','id',array('intv'=>12,'floatv'=>1234.34,'stringv'=>'neat','textv'=>'cat', 'boolv'=>'false','datev'=>'12/12/2009'));
		$this->assertEquals($id,1);

    	$id=$this->db->insert('test.test','id',array('intv'=>12,'floatv'=>1234.34,'stringv'=>'neat','textv'=>'cat', 'boolv'=>'false','datev'=>'12/12/2009'));
		$this->assertEquals($id,2);
    
		$id=$this->db->insert('test.test','id',array('intv'=>12,'floatv'=>1234.34,'stringv'=>'neat','textv'=>'cat', 'boolv'=>'false','datev'=>'12/12/2009'));
		$this->assertEquals($id,3);
		
		$this->assertTrue($this->db->count('test.test','id')==3);
    }

    public function testDelete()
    {
		$id=$this->db->insert('test.test','id',array('intv'=>12,'floatv'=>1234.34,'stringv'=>'neat','textv'=>'cat', 'boolv'=>'false','datev'=>'12/12/2009'));
		$this->assertEquals($id,1);

    	$id=$this->db->insert('test.test','id',array('intv'=>12,'floatv'=>1234.34,'stringv'=>'neat','textv'=>'cat', 'boolv'=>'false','datev'=>'12/12/2009'));
		$this->assertEquals($id,2);
    
		$id=$this->db->insert('test.test','id',array('intv'=>12,'floatv'=>1234.34,'stringv'=>'neat','textv'=>'cat', 'boolv'=>'false','datev'=>'12/12/2009'));
		$this->assertEquals($id,3);
		
		$this->assertTrue($this->db->count('test.test','id')==3);
		
		$this->assertTrue($this->db->delete('test.test','id',1));
		$this->assertTrue($this->db->count('test.test','id')==2);

    	$this->assertTrue($this->db->delete('test.test','id',2));
		$this->assertTrue($this->db->count('test.test','id')==1);

    	$this->assertTrue($this->db->delete('test.test','id',3));
		$this->assertTrue($this->db->count('test.test','id')==0);
    }
}
