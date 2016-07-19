<?php
namespace DepMap;


class DeploymentTest extends \PHPUnit_Framework_TestCase
{
	public function test_setRootDirectory_ReturnSelf() 
	{
		$d = new Deployment();
		$this->assertSame($d, $d->setRootDirectory('a'));
	}
	
	
	public function test_setTargetDirectory_ReturnSelf() 
	{
		$d = new Deployment();
		$this->assertSame($d, $d->setTargetDirectory('a'));
	}
	
	
	public function test_setMapFileFormat_ReturnSelf()
	{
		$d = new Deployment();
		$this->assertSame($d, $d->setMapFileFormat('a'));
		$this->assertSame($d, $d->setMapFileFormat('a', 'b'));
	}
}