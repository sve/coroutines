<?php

namespace Responders;

use Application\Abstract\AbstractResponder;

class ResponderFirst extends AbstractResponder
{
    /**
     * @param ...$arguments
     * @return void
     */
    public function __invoke(...$arguments): void
    {
        $this->logger->debug('Responder First:', $arguments);
    }
}