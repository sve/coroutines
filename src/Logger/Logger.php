<?php

namespace Application\Logger;

use Application\Configuration\Configuration;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Throwable;

class Logger extends \Monolog\Logger
{
    /**
     * @var array
     */
    protected array $config;

    /**
     * @param Configuration $configuration
     */
    public function __construct(
        protected Configuration $configuration
    ) {
        $this->config = $this->configuration->get('logging');
        parent::__construct($this->config['logger']['name']);
        $this->initialize();
    }

    /**
     * @return mixed
     */
    public function getConfig(): mixed
    {
        return $this->config;
    }

    /**
     * @return bool
     */
    public function isEnabledLoggingErrors(): bool
    {
        return (bool) $this->config['log']['errors'];
    }

    /**
     * @return bool
     */
    public function isEnabledLoggingDetails(): bool
    {
        return (bool) $this->config['log']['details'];
    }

    /**
     * @param $level
     * @param string|\Stringable $message
     * @param array $context
     * @return void
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        if ($this->isEnabledLoggingErrors()) {
            parent::log($level, $message, $this->isEnabledLoggingDetails() ? $context : []);
        }
    }

    /**
     * @param string|int|Level $level
     * @param Throwable $exception
     * @param array|null $context
     * @return void
     */
    public function logException(string|int|Level $level, Throwable $exception, array $context = null): void
    {
        $data = [
            'exception' => (string) $exception,
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ];

        if ($context) {
            $data['context'] = $context;
        }

        $this->log($this->toMonologLevel($level), $exception->getMessage(), $context);
    }

    /**
     * @return void
     */
    protected function initialize(): void
    {
        foreach ($this->config['logger']['handlers'] as $pipe => $handler) {
            $handler = new StreamHandler($handler['path'], $handler['level']);

            if (isset($this->config['pipes'][$pipe]) && $this->config['pipes'][$pipe]) {
                $this->pushHandler($handler);
            }
        }
    }
}