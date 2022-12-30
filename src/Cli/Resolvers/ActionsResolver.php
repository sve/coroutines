<?php

namespace Application\Cli\Resolvers;

use Application\Abstract\AbstractResolver;

class ActionsResolver extends AbstractResolver
{
    protected string $name = 'Action';

    /**
     * @return array
     */
    public function getDirectories(): array
    {
        return $this->autoloader->getPathsActions();
    }
}