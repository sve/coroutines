<?php

namespace Application\Abstract;

use Application\Container\Container;
use Application\Logger\Logger;

abstract class AbstractResponder
{
    /**
     * @param ...$arguments
     * @return void
     */
    abstract public function __invoke(...$arguments): void;

    /**
     * @param Container $container
     * @param Logger $logger
     */
    public function __construct(
        protected Container $container,
        protected Logger $logger
    ) {

    }
}