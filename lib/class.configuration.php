<?php
class Configuration {

	private $configData = array();
	private $configurationFilePath;
	private $controlChar = '=';

	public function __construct( $pathToConfigFile )
	{
		if ( !file_exists($pathToConfigFile) )
		{
			return false;
		}
		$this->configurationFilePath = $pathToConfigFile;
		$this->loadConfiguration();
	}

	private function loadConfiguration( )
	{
		$this->convertToConfigurationData();
	}

	public function getConfiguration( $property )
	{
		if ( count($this->configData) === 0 || $this->configData[$property] == null )
		{ 
			return false;
		} 
		return $this->configData[$property];
	}

	public function setConfiguration( $property, $value )
	{
		if ( strpos($property, $this->controlChar) !== false || strpos($value, $this->controlChar) !== false )
		{
			return false;
		} 
		$this->configData[$property] = $value;
	}

	public function saveConfiguration()
	{
		if ( count($this->configData) === 0 )
		{
			return false;
		}
		$this->convertToConfigurationFile();
	}

	private function convertToConfigurationFile( )
	{	
		$fp = fopen($this->configurationFilePath, 'w');
		foreach ( $this->configData as $property => $value )
		{
			fwrite($fp, "{$property}{$this->controlChar}{$value}\n");
		}
		fclose($fp);
	}

	private function convertToConfigurationData( )
	{
		$configFile = file($this->configurationFilePath); 
		foreach ( $configFile as $line )
		{
			$tmp = explode($this->controlChar, $line);
			$this->setConfiguration(trim($tmp[0]), trim($tmp[1]));
		}
	}
}