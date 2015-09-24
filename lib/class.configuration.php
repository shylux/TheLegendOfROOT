<?php

class Configuration {
	
	private $configData = array();
	private $configurationFilePath;

	public function __construct( $pathToConfigFile )
	{
		if ( !file_exists($pathToConfigFile) )
		{
			return false;
		}

		$configFile = file($pathToConfigFile);
		$this->configurationFilePath = $pathToConfigFile;
		$this->loadConfiguration();
	}

	private function loadConfiguration( )
	{
		$this->convertToConfigurationData();
	}

	public function getConfiguration( $property )
	{
		if ( count($this->configData === 0 || $this->configData[$property] == null )
		{
			return false;	
		}
		return $this->configData[$property];
	}

	public function setConfiguration( $property, $value )
	{
		if ( count($this->configData === 0 )
		{
			return false;	
		}
		$this->configData[$property] = $value;
	}

	public function saveConfiguration() 
	{
		if ( count($this->configData === 0 )
		{
			return false;	
		}
		$this->convertToConfigurationFile();
	}

	private function convertToConfigurationFile( )
	{
		// TO DO: put the configuration of the file to the $configData-array (which format?) 
		// file_put_contents ($this->configurationFilePath, $data) etc..
	}


	private function convertToConfigurationData( )
	{
		// TO DO: put the configuration of the file to the $configData-array (which format?) 
	}

}

	
