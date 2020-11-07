<?php

namespace App\Syncer;

interface SyncerInterface
{

    public function __construct(\App\SyncConfig $config);

    /**
     * This will return the command that will be run on the target machine
     * The idea is that this command _should_ generate a unique dump
     * that can then be rsynced locally
     *
     * @return string
     */
    public function getRemoteCommand();

    public function getLocalCommand();

    public function getTransferResourceName();
}