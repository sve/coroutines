<?php

namespace Application\Abstract;

use Application\Container\Container;
use Application\Logger\Logger;
use Monolog\Level;
use Exception;
use Throwable;

abstract class AbstractException extends Exception implements Throwable
{
    /**
     * @var Container
     */
    protected Container $container;
    /**
     * @var Logger
     */
    protected Logger $logger;
    /**
     * @var Level
     */
    protected Level $level = Level::Debug;
    /**
     * @var array
     */
    protected array $context;

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @param Container $container
     */
    public function setContainer(Container $container): void
    {
        $this->container = $container;
    }

    /**
     * @return Logger
     */
    public function getLogger(): Logger
    {
        return $this->logger;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger(Logger $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @return Level
     */
    public function getLevel(): Level
    {
        return $this->level;
    }

    /**
     * @param Level $level
     */
    public function setLevel(Level $level): void
    {
        $this->level = $level;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @param array $context
     */
    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    /**
     * @param ...$arguments
     */
    public function __construct(...$arguments)
    {
        if (isset($arguments['message'])) {
            $this->message = $arguments['message'];
        } else {
            $this->message = $this->buildMessage();
        }

        $this->context = $arguments;
    }

    /**
     * @param ...$arguments
     * @return static
     */
    public static function of(...$arguments): self
    {
        $exception = new (static::class);
        $exception->context = $arguments;

        return $exception;
    }

    /**
     * @param $level
     * @return $this
     */
    public function level($level): self
    {
        $this->level = Logger::toMonologLevel($level);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function __destruct()
    {
        $this->container = new Container();
        $this->logger = $this->container->get(Logger::class);
        $this->logger->logException($this->level, $this, $this->context);
    }

    /**
     * @return string
     */
    private function buildMessage(): string
    {
        $message = basename(str_replace('\\', '/', static::class));

        $pos = strpos($message, 'Exception');
        if ($pos !== false) {
            $message = substr_replace($message,'',$pos,strlen('Exception'));
        }

        return trim(preg_replace('/[A-Z]/', ' $0', $message));
    }
}