<?php
namespace DepMap\Filter;


class FilterMap
{
	private $rootFolderLength;
	
	private $filters           = [];
	private $isExcludingFilter = [];
	
	
	/**
	 * @param string $rootFolder
	 */
	public function __construct($rootFolder) 
	{
		$this->rootFolderLength = strlen($rootFolder) + 1;
	}
	
	
	/**
	 * @param string $filter
	 * @param bool $isExcluding
	 */
	public function addFilter($filter, $isExcluding)
	{
		if (strpos($filter, './') === 0)
		{
			$filter = substr($filter, 2);
		}
		else if ($filter[0] == '/')
		{
			$filter = substr($filter, 1);
		}
		
		$this->filters[] = $filter;
		$this->isExcludingFilter[] = $isExcluding;
	}
	
	
	/**
	 * @param string $item
	 * @return bool
	 */
	public function isIncluded($item)
	{
		$item = substr($item, $this->rootFolderLength);
		$isExcluded = false;
		
		foreach ($this->filters as $index => $filter)
		{
			if (fnmatch($filter, $item))
			{
				$isExcluded = $this->isExcludingFilter[$index];
			}
		}
		
		return !$isExcluded;
	}
}