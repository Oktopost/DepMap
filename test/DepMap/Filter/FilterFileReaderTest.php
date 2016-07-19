<?php
namespace DepMap\Filter;


class FilterFileReaderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @param string $fileName
	 * @return string
	 */
	private function getFilePath($fileName)
	{
		return realpath(__DIR__ . "/FilterFileReaderTest") . "/$fileName";
	}
	
	
	/**
	 * @expectedException \DepMap\DepMepException
	 */
	public function test_constructor_InvalidFile_ThrowException()
	{
		new FilterFileReader($this->getFilePath('InvalidFilePath'));
	}
	
	
	public function test_read_EmptyFile_ReturnNull()
	{
		$f = new FilterFileReader($this->getFilePath('Empty'));
		$this->assertNull($f->read());
	}
	
	public function test_read_CommentsOnly_ReturnNull()
	{
		$f = new FilterFileReader($this->getFilePath('CommentsOnly'));
		$this->assertNull($f->read());
	}
	
	public function test_read_noExcludingRules_ReturnNull()
	{
		$f = new FilterFileReader($this->getFilePath('NoExcluding'));
		$this->assertNull($f->read());
	}
	
	public function test_read_HasExcludeRules_ReturnMap()
	{
		$f = new FilterFileReader($this->getFilePath('Loaded'));
		$this->assertInstanceOf(FilterMap::class, $f->read());
	}
	
	public function test_read_FiltersLoaded()
	{
		$f = new FilterFileReader($this->getFilePath('FilterLoaded'));
		$map = $f->read();
		
		$this->assertFalse($map->isIncluded($this->getFilePath('a/a')));
		$this->assertTrue($map->isIncluded($this->getFilePath('a/b')));
	}
}