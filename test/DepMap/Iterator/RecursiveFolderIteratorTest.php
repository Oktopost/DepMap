<?php
namespace DepMap\Iterator;


class RecursiveFolderIteratorTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @param string $item
	 * @return string
	 */
	private function getPathToTestItem($item)
	{
		return realpath(__DIR__ . '/RecursiveFolderIteratorTest') . "/$item";
	}
	
	/**
	 * @param string $dir
	 * @return string
	 */
	private function getTestDirectory($dir)
	{
		$dir = $this->getPathToTestItem($dir);
		
		if (!is_dir($dir))
		{
			mkdir($dir);
		}
		
		return $dir;
	}
	
	/**
	 * @param array $items
	 * @param string $dir
	 * @param string $filterFormat
	 */
	private function assertContainItems(array $items, $dir, $filterFormat = 'deploy.map')
	{
		$foundItems = $this->runForFolder($dir, $filterFormat);
		
		sort($items);
		sort($foundItems);
		array_walk($items, 
			function(&$item)
				use ($dir)
			{
				$item = $this->getPathToTestItem($dir . '/' . $item);
			});
		
		$this->assertEquals($items, $foundItems);
	}
	
	/**
	 * @param string $dir
	 * @param string $filterFormat
	 * @return array
	 */
	private function runForFolder($dir, $filterFormat = 'deploy.map')
	{
		$dir = $this->getTestDirectory($dir);
		$result = [];
		
		$it = new RecursiveFolderIterator($dir);
		$it->setFilterMapFormat($filterFormat);
		
		foreach ($it->find() as $item)
		{
			$result[] = $item;
		}
		
		return $result;
	}
	
	
	public function test_setFilterMapFormat_ReturnSelf()
	{
		$it = new RecursiveFolderIterator('a');
		$this->assertSame($it, $it->setFilterMapFormat('a'));
	}
	
	
	public function test_find_EmptyDirectory_NothingFound()
	{
		$this->assertEquals([], $this->runForFolder('Empty'));
	}
	
	public function test_find_DirectoryWithoutFilters_ItemsLoaded()
	{
		$this->assertContainItems(['a', 'b'], 'NoFilters');
	}
	
	public function test_find_FilesInSubDirectoriesLoaded()
	{
		$this->assertContainItems(['a/a'], 'SubFoldersLoaded');
	}
	
	public function test_find_FilterFileLoaded()
	{
		$this->assertContainItems(['a'], 'SimpleFilterFile');
	}
	
	public function test_find_NumberOfFilterFiles_AllLoaded()
	{
		$this->assertContainItems(['a', 'd'], 'NumberOfFilterFiles', 'deploy.map*');
	}
	
	public function test_find_FilterInSubDirectory_FilterLoaded()
	{
		$this->assertContainItems(['a/a'], 'SubDirectoryFilter');
	}
	
	public function test_find_FilterInAnotherSubDirectory_FilterDoesNotAffectOtherDirectories()
	{
		$this->assertContainItems(['a/a', 'b/b'], 'FilterInAnother');
	}
	
	public function test_find_ParentFilterMapsHasHigherPriority()
	{
		$this->assertContainItems(['a/a'], 'HigherMap');
	}
	
	public function test_find_HiddenFileFound()
	{
		$this->assertContainItems(['.hidden'], 'HiddenFile');
	}
}