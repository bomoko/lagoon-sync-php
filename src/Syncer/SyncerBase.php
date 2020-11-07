<?php

namespace App\Syncer;


abstract class SyncerBase
{
    protected $configuration;

    protected function getLocalConfiguration()
    {
        $retConfig = $this->configuration;
        $localOverrides = [];
        if(isset($this->configuration['local']['overrides'])) {
            $localOverrides = $this->configuration['local']['overrides'];
        }

        return array_merge($retConfig, $localOverrides);
    }
}