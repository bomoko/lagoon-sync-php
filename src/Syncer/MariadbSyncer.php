<?php

namespace App\Syncer;


class MariadbSyncer extends SyncerBase implements SyncerInterface
{

    const SYNCTYPE_NAME = 'mariadb';


    protected $uniqueFilenameForOutput;

    public static function synctype()
    {
        return self::SYNCTYPE_NAME;
    }

    public function __construct(\App\SyncConfig $config)
    {
        //here we can do some checks to make sure that the configuration
        //details look reasonable
        $this->configuration = $config->getSyncConfigurationDetails(self::SYNCTYPE_NAME);
        if (!array_key_exists('database', $this->configuration)) {
            throw new \Exception(sprintf("Invalid configuration data for type: %s",
              self::SYNCTYPE_NAME));
        }

        $this->uniqueFilenameForOutput = uniqid(self::SYNCTYPE_NAME . "-sync-") . date('y-m-d') . '-' . uniqid() . '.sql';
    }

    protected function getOutputDirectory()
    {
        return '/tmp/'; //TODO: this could be filled in from env vars or whatever
        //TODO: could also have local/remote overrides ...
    }

    /**
     * This will return the command that will be run on the target machine
     * The idea is that this command _should_ generate a unique dump
     * that can then be rsynced locally
     *
     * @return string
     */
    public function getRemoteCommand()
    {
        //let's build our command
        $remoteCommand = sprintf("mysqldump -h%s -u%s -p%s -P%s %s > %s",
          $this->configuration['hostname'],
          $this->configuration['username'],
          $this->configuration['password'],
          $this->configuration['port'],
          $this->configuration['database'],
          $this->getTransferFilename()
        );
        return $remoteCommand;
    }

    public function getLocalCommand()
    {
        $config = $this->getLocalConfiguration();

        $localcommand = sprintf("mysql -h%s -u%s -p%s -P%s %s < %s",
          $config['hostname'],
          $config['username'],
          $config['password'],
          $config['port'],
          $config['database'],
          $this->getTransferFilename()
        );

        return $localcommand;
    }

    public function getTransferFilename()
    {
        return $this->getOutputDirectory() . $this->uniqueFilenameForOutput;
    }

}