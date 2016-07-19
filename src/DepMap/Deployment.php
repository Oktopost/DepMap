<?php
namespace DepMap;


class Deployment
{
	private $rootDir	= false;
	private $targetDir 	= false;
	private $deployMap	= 'deploy.map';
	
	
	/**
	 * @throws DepMepException
	 */
	private function validate()
	{
		
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
	 * @return int Number of cloned files
	 */
	public function deploy($dryRun = false)
	{
		$this->validate();
	}
}