<?php
namespace DepMap\Iterator;


use DepMap\Filter\FilterMap;
use DepMap\Filter\FilterFileReader;


class RecursiveFolderIterator
{
	private $rootDir;
	private $filterMapFormat = '';
	
	/** @var FilterMap[] */
	private $filters = [];
	
	
	/**
	 * @param string $dir
	 * @return int Number of loaded FilterMaps
	 */
	private function loadMapFileInDirectory($dir)
	{		
		if (!$this->filterMapFormat) return 0;
		
		$loaded = 0;
		
		foreach (glob("$dir/{$this->filterMapFormat}", GLOB_ERR) as $file)
		{
			$mapLoader = new FilterFileReader($file);
			$map = $mapLoader->read();
			
			if ($map)
			{
				$this->filters[] = $map;
				$loaded++;
			}
		}
		
		return $loaded;
	}
	
	/**
	 * @param string $file
	 * @return bool
	 */
	private function isIncluded($file)
	{
		if (in_array(basename($file), ['.', '..'])) return false;
		
		foreach ($this->filters as $filter)
		{
			if (!$filter->isIncluded($file)) 
			{
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * @param string $dir
	 * @return \Generator
	 */
	private function filterDirectory($dir)
	{
		foreach (glob("$dir/{,.}*", GLOB_BRACE | GLOB_ERR) as $item)
		{
			if (!$this->isIncluded($item)) continue;
			
			if (is_file($item))
			{
				if (fnmatch($this->filterMapFormat, basename($item)))
				{
					continue;
				}
				
				yield $item;
			}
			else
			{
				foreach ($this->iterateDirectory($item) as $subItem)
				{
					yield $subItem;
				}
			}
		}
	}
	
	/**
	 * @param string $dir
	 * @return \Generator
	 */
	private function iterateDirectory($dir)
	{
		$loadedFilters = $this->loadMapFileInDirectory($dir);
		
		foreach ($this->filterDirectory($dir) as $item)
		{
			yield $item;
		}
		
		if ($loadedFilters)
		{
			$this->filters = array_slice($this->filters, 0, count($this->filters) - $loadedFilters);
		}
	}
	
	
	/**
	 * @param string $rootDir
	 */
	public function __construct($rootDir)
	{
		$this->rootDir = $rootDir;
	}
	
	/**
	 * @param string $format
	 * @return static
	 */
	public function setFilterMapFormat($format)
	{
		$this->filterMapFormat = $format;
		return $this;
	}
	
	/**
	 * @return \Generator
	 */
	public function find()
	{
		return $this->iterateDirectory($this->rootDir);
	}
}