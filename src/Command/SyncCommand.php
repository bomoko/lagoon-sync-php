<?php

namespace App\Command;

use App\Syncer\MariadbSyncer;
use App\SyncConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class SyncCommand extends Command
{

    protected static $defaultName = "sync";

    protected function configure()
    {
        $this->addArgument('remote', InputArgument::REQUIRED,
          'Which REMOTE environment are we syncing from?')
          ->addArgument('resource', InputArgument::OPTIONAL,
            'Which resource are we syncing?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $syncConfigDetails = SyncConfig::loadSyncConfigFromFile();
        $syncer = new MariadbSyncer($syncConfigDetails);
        var_dump($syncer->getRemoteCommand());
        return Command::SUCCESS;
    }

}