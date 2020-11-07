<?php

namespace App\Command;

use App\Syncer\MariadbSyncer;
use App\SyncConfig;
use App\SyncRunner;
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
          'Which REMOTE environment are we syncing from? (openshiftprojectname)')
          ->addArgument('resource', InputArgument::REQUIRED,
            'Which resource are we syncing?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $syncConfigDetails = SyncConfig::loadSyncConfigFromFile();
        $resource = $input->getArgument('resource');

        $classname = 'App\\Syncer\\' . ucfirst($resource) . 'Syncer';
        if(!class_exists($classname)) {
            throw new \Exception("Syncing of resource type $classname not supported");
        }

        $syncer = new $classname($syncConfigDetails);
        $syncRunner = new SyncRunner($syncer, $input->getArgument('remote'));
        $syncRunner->run();
        return Command::SUCCESS;
    }

}