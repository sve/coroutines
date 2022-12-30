<?php

namespace Application\Cli\Commands;

use Application\Abstract\AbstractCommand;
use Application\Cli\Cli;
use Application\Container\Container;
use Application\Exceptions\Cli\ActionNotFoundException;
use Application\Runner\Runner;

class ActionRunCommand extends AbstractCommand
{
    /**
     * @param Cli $cli
     * @param Container $container
     * @param Runner $runner
     */
    public function __construct(
        protected Cli $cli,
        protected Container $container,
        protected Runner $runner
    ) {

    }

    /**
     * @return void
     * @throws ActionNotFoundException
     */
    public function __invoke(): void
    {
        $arguments = $this->cli->getArguments();
        $actions = $this->cli->getActions();
        $action = array_shift($arguments);

        if (empty($action) || !isset($actions[$action])) {
            throw new ActionNotFoundException();
        }

        $class = $actions[$action];
        $namespace = sprintf("%s\\%s", 'Actions', $class);
        $action = $this->container->inject($namespace);

        $this->runner->run($action);
    }
}