<?php

namespace Application\Cli\Commands;

use Application\Abstract\AbstractCommand;
use Application\Cli\Cli;

class ActionsListCommand extends AbstractCommand
{
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
        $map = $this->cli->getActions();
        $map = array_flip($map);

        $this->addLine('List Actions:');
        $this->setResultMap($map);
    }
}