<?php

namespace Application\Cli\Resolvers;

use Application\Abstract\AbstractResolver;

class CommandsResolver extends AbstractResolver
{
    protected string $name = 'Command';

    /**
     * @return array
     */
    public function getDirectories(): array
    {
        return $this->autoloader->getPathsCommands();
    }
}