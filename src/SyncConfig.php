<?php

namespace App;


use Symfony\Component\Yaml\Yaml;

class SyncConfig
{

    const LAGOON_YAML_KEY = "lagoon-sync";

    protected $configuration;

    /**
     * This will load a configuration from a file
     *
     * The big idea here is to have multiple ways of loading sync configs,
     * Files being just one of them ...
     *
     * @param string $syncConfig
     */
    public static function loadSyncConfigFromFile($syncConfig = ".lagoon.yml")
    {
        $parsedData = Yaml::parseFile($syncConfig);
        if(!key_exists(self::LAGOON_YAML_KEY, $parsedData)) {
            throw new \Exception(sprintf("Could not find %s in file %s", self::LAGOON_YAML_KEY, $syncConfig));
        }
        return new static($parsedData[self::LAGOON_YAML_KEY]);
    }

    public static function loadSyncConfigFromJson($jsonWithConfigInIt)
    {
        //Thoughts ...
        //
        // This would then load up a SyncConfig from the json file,
        // this could be passed as an argument or whatever ...
        return new static();
    }


    protected function __construct($configurationArray = [])
    {
        $this->configuration = $configurationArray;
    }

    /**
     * Simply returns a list of all configuration details we have
     *
     * @return array
     */
    public function getSyncTypes()
    {
        return array_keys($this->configuration);
    }

    public function getSyncConfigurationDetails($syncName)
    {
        if(!in_array($syncName, array_keys($this->configuration))) {
            throw new \Exception("Invalid sync type name $syncName -- not found");
        }
        return $this->configuration[$syncName];
    }

}