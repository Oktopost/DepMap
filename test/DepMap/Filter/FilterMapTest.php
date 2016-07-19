<?php
namespace DepMap\Filter;


class FilterMapTest extends \PHPUnit_Framework_TestCase
{
	public function test_isIncluded_NoFilters_ReturnTrue()
	{
		$f = new FilterMap('');
		$this->assertTrue($f->isIncluded('/a'));
	}
	
	public function test_isIncluded_IncludeFilterNotMatching_ReturnTrue()
	{
		$f = new FilterMap('');
		$f->addFilter('b', false);
		$this->assertTrue($f->isIncluded('/a'));
	}
	
	public function test_isIncluded_ExcludeFilterNotMatching_ReturnTrue()
	{
		$f = new FilterMap('');
		$f->addFilter('b', true);
		$this->assertTrue($f->isIncluded('/a'));
	}
	
	public function test_isIncluded_IncludeFilterMatching_ReturnTrue()
	{
		$f = new FilterMap('');
		$f->addFilter('a', false);
		$this->assertTrue($f->isIncluded('/a'));
	}
	
	public function test_isIncluded_ExcludeFilterMatching_ReturnFalse()
	{
		$f = new FilterMap('');
		$f->addFilter('a', true);
		$this->assertFalse($f->isIncluded('/a'));
	}
	
	public function test_isIncluded_ExcludeFilterAfterInclude_ReturnFalse()
	{
		$f = new FilterMap('');
		$f->addFilter('a', false);
		$f->addFilter('a', true);
		$this->assertFalse($f->isIncluded('/a'));
	}
	
	public function test_isIncluded_IncludeFilterAfterExclude_ReturnFalse()
	{
		$f = new FilterMap('');
		$f->addFilter('a', true);
		$f->addFilter('a', false);
		$this->assertTrue($f->isIncluded('/a'));
	}
	
	public function test_isIncluded_RootDirectoryNotEmpty_FilterIsRelativeToDirectory()
	{
		$f = new FilterMap('/some/root');
		$f->addFilter('a', true);
		
		$this->assertFalse($f->isIncluded('/some/root/a'));
		$this->assertTrue($f->isIncluded('/some/root/b'));
	}
	
	public function test_isIncluded_PatternIsRelativeToDirectory()
	{
		$f = new FilterMap('/some/root');
		$f->addFilter('./a', true);
		
		$this->assertFalse($f->isIncluded('/some/root/a'));
		$this->assertTrue($f->isIncluded('/some/root/b'));
	}
	
	public function test_isIncluded_PatternIsRelativeToRootDirectory_TreatedAsCurrentDirectory()
	{
		$f = new FilterMap('/some/root');
		$f->addFilter('/a', true);
		
		$this->assertFalse($f->isIncluded('/some/root/a'));
		$this->assertTrue($f->isIncluded('/some/root/b'));
	}
	
	public function test_isIncluded_ComplexPatternMatching()
	{
		$f = new FilterMap('/some/root');
		$f->addFilter('*a?/*asd', true);
		
		$this->assertFalse($f->isIncluded('/some/root/ssan/nasd'));
		$this->assertTrue($f->isIncluded('/some/root/ssan/nasd_not'));
	}
}