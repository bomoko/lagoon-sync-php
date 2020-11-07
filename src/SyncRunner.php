<?php

namespace App;

use App\Syncer\SyncerInterface;
use mikehaertl\shellcommand\Command;

/**
 * Class SyncRunner
 *
 * This Class will take a Syncer and actually attempt to run the commands
 * remotely and locally. It has three phases - running the remote command,
 * pulling down the file, and then running the local command.
 *
 * Since this is a Demo, I'm going to do this in the dodgiest way possible
 * But in the full system this will have to be rock solid - in fact, what
 * we would do is actually offload all of this processing to the lagoon-cli
 * tool
 *
 * @package App
 */
class SyncRunner
{

    protected $syncer;

    protected $remoteOpenshiftProjectName;

    public function __construct(
      SyncerInterface $syncer,
      $remoteOpenshiftProjectName
    ) {
        $this->syncer = $syncer;
        $this->remoteOpenshiftProjectName = $remoteOpenshiftProjectName;
    }

    public function run()
    {
        //Presumably each of these would throw some error
        $this->runRemoteCommand();
        $this->transferFile();
        $this->runLocalCommand();
        return true;
    }


    protected function runRemoteCommand()
    {
        $execString = sprintf("ssh -t -o \"UserKnownHostsFile=/dev/null\" -o \"StrictHostKeyChecking=no\" -p 32222 %s@ssh.lagoon.amazeeio.cloud '%s'",
          $this->remoteOpenshiftProjectName,
          $this->syncer->getRemoteCommand());
        $command = new Command($execString);
        if ($command->execute()) {
            echo $command->getOutput();
        } else {
            echo $command->getError();
            $exitCode = $command->getExitCode();
        }
    }

    protected function transferFile()
    {
        //NOTE: These will be the same filename locally and remotely for now - we'll want to configure this to be overridden
        $remoteFile = $this->syncer->getTransferFilename();
        $localFile = $this->syncer->getTransferFilename();
        $execString = sprintf('rsync -e "ssh -o LogLevel=ERROR -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -p 32222" -a %s@ssh.lagoon.amazeeio.cloud:%s %s',
          $this->remoteOpenshiftProjectName,
          $remoteFile,
          $localFile);
        $command = new Command($execString);
        if ($command->execute()) {
            echo $command->getOutput();
        } else {
            echo $command->getError();
            $exitCode = $command->getExitCode();
        }
    }

    protected function runLocalCommand()
    {
        $remoteFile = $this->syncer->getTransferFilename();
        $localFile = $this->syncer->getTransferFilename();
        $execString = $this->syncer->getLocalCommand();
        $command = new Command($execString);
        if ($command->execute()) {
            echo $command->getOutput();
        } else {
            echo $command->getError();
            $exitCode = $command->getExitCode();
        }
    }

    protected function cleanUp()
    {
        //remove remote file
        //remove local file
    }

}