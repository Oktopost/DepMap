<?php
namespace DepMap\Filter;


use DepMap\DepMepException;


class FilterFileReader
{
	private $filePath;
	private $rootDir;
	
	/** @var FilterMap|null */
	private $filterMap = null;
	
	
	/**
	 * @param string $line
	 */
	private function readLine($line)
	{
		$isExclude = ($line[0] != '!');
		
		if (!$isExclude && !$this->filterMap)
		{
			return;
		}
		else if (!$this->filterMap)
		{
			$this->filterMap = new FilterMap($this->rootDir);
		}
		
		if (!$isExclude)
		{
			$line = substr($line, 1);
		}
		
		$this->filterMap->addFilter($line, $isExclude);
	}
	
	/**
	 * @param resource $handle
	 */
	private function readFile($handle)
	{
		while (!feof($handle))
		{
			$line = trim(fgets($handle));
			
			if ($line && !in_array($line[0], ['#', ';']))
			{
				$this->readLine($line);
			}
		}
	}
	
	
	/**
	 * @param string $filePath
	 */
	public function __construct($filePath)
	{
		if (!is_readable($filePath))
		{
			throw new DepMepException('Could not open file ' . $this->filePath);
		}
		
		$fileInfo = pathinfo($filePath);
		
		$this->filePath = $filePath;
		$this->rootDir = $fileInfo['dirname'];
	}
	
	
	/**
	 * @return FilterMap|null
	 */
	public function read()
	{
		$h = fopen($this->filePath, 'r');
		
		if (!$h)
			throw new DepMepException('Could not open file ' . $this->filePath);
		
		try
		{
			$this->readFile($h);
		}
		finally
		{
			fclose($h);
		}
		
		return $this->filterMap;
	}
}