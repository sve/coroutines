<?php

namespace Application\Cli\Commands;

use Application\Abstract\AbstractCommand;
use Application\Cli\Cli;

class CommandsListCommand extends AbstractCommand
{
    public const NAME = 'commands:list';

    /**
     * @param Cli $cli
     */
    public function __construct(
        protected Cli $cli
    ) {

    }

    /**
     * @return void
     */
    public function __invoke(): void
    {
        $map = $this->cli->getCommands();
        $map = array_flip($map);

        $this->addLine('List Commands:');
        $this->setResultMap($map);
    }
}