<?php
namespace DepMap;


use DepMap\Iterator\RecursiveFolderIterator;


class Deployment
{
	private $rootDir			= false;
	private $targetDir 			= false;
	private $deployMapFormat	= 'deploy.ignore.map';
	
	
	/**
	 * @throws DepMapException
	 */
	private function validate()
	{
		if (!$this->rootDir)
			throw new DepMapException('Root directory is not set');
		
		if (!$this->targetDir)
			throw new DepMapException('Target directory is not set');
		
		if (!is_dir($this->rootDir))
			throw new DepMapException('Root directory is not readable');
		
		if (!is_dir($this->targetDir))
			throw new DepMapException('Target directory is not readable');
		
		if (glob($this->targetDir . '/*'))
			throw new DepMapException("Target directory {$this->targetDir} must be empty");
	}
	
	/**
	 * @param $source
	 * @param $dryRun
	 * @param $verbose
	 */
	private function copy($source, $dryRun, $verbose)
	{
		$target = $this->targetDir . substr($source, strlen($this->rootDir));
		
		if ($verbose)
		{
			echo "$source => $target\n";
		}
		
		if (!$dryRun)
		{
			$dir = dirname($target);
			
			if (!is_dir($dir) && !mkdir($dir, 0777, true))
			{
				throw new DepMapException("Failed to create directory $dir");
			}
			
			if (!copy($source, $target))
			{
				throw new DepMapException("Failed to copy $source to $target");
			}
		}
	}
	
	
	/**
	 * @param string $dir
	 * @return static
	 */
	public function setRootDirectory($dir) 
	{
		$this->rootDir = $dir;
		return $this;
	}
	
	/**
	 * @param string $dir
	 * @return static
	 */
	public function setTargetDirectory($dir)
	{
		$this->targetDir = $dir;
		return $this;
	}
	
	/**
	 * @param string $deployMap
	 * @return static
	 */
	public function setMapFileFormat($deployMap)
	{
		$this->deployMap = $deployMap;
		return $this;
	}
	
	/**
	 * @param bool $dryRun
	 * @param bool $verbose
	 * @return int Number of cloned files
	 */
	public function deploy($dryRun = false, $verbose = false)
	{
		$this->validate();
		
		$totalItems = 0;
		
		$iterator = new RecursiveFolderIterator($this->rootDir);
		$iterator->setFilterMapFormat($this->deployMapFormat);
		
		foreach ($iterator->find() as $item)
		{
			$totalItems++;
			$this->copy($item, $dryRun, $verbose);
		}
		
		return $totalItems;
	}
}