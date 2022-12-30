<?php

namespace Application\Abstract;

use Application\Container\Container;
use Application\Logger\Logger;

abstract class AbstractDomain
{
    /**
     * @var string
     */
    protected string $key;

    /**
     * @param ...$arguments
     * @return mixed
     */
    abstract function __invoke(...$arguments): mixed;

    /**
     * @param Container $container
     * @param Logger $logger
     */
    public function __construct(
        protected Container $container,
        protected Logger $logger
    ) {

    }

    /**
     * @return void
     */
    public function run(): void
    {
        $this->__invoke();
    }

    /**
     * @param string $respond
     * @param ...$arguments
     * @return mixed
     */
    public function respond(string $respond, ...$arguments): mixed
    {
        try {
            $respond = $this->container->inject($respond);
            return call_user_func_array($respond, $arguments);
        } catch (\Exception $exception) {
            $this->logger->debug($exception);
        }
    }
}