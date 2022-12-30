<?php

namespace Application\Cli;

use Application\Abstract\AbstractCommand;
use Application\Abstract\AbstractResolver;
use Application\Bootstrap\Autoloader;
use Application\Cli\Commands\CommandsListCommand;
use Application\Cli\Resolvers\ActionsResolver;
use Application\Cli\Resolvers\CommandsResolver;
use Application\Exceptions\Cli\CommandNotFoundException;
use Application\Logger\Logger;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Exception;

class Cli
{
    /**
     * @var array
     */
    protected array $commands = [];

    /**
     * @var array
     */
    protected array $actions = [];

    /**
     * @var array
     */
    protected array $arguments = [];

    /**
     * @var string
     */
    protected string $commandName;

    /**
     * @var string
     */
    protected string $output;

    /**
     * @param Autoloader $autoloader
     * @param Logger $logger
     * @param ContainerBuilder $containerBuilder
     * @param CommandsResolver $commandsResolver
     * @param ActionsResolver $actionsResolver
     */
    public function __construct(
        protected Autoloader $autoloader,
        protected Logger $logger,
        protected ContainerBuilder $containerBuilder,
        protected CommandsResolver $commandsResolver,
        protected ActionsResolver $actionsResolver
    ) {
        $this->resolve($this->commandsResolver);
        $this->resolve($this->actionsResolver);
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $this->buildArguments();

        try {
            $this->output = $this->runConsoleCommand();
        } catch (\Throwable $throwable) {
            $extra = [];

            if ($this->logger->isEnabledLoggingDetails()) {
                $extra = explode("\n", trim($throwable->getTraceAsString()));
            }

            $this->output = $this->formatOutput($throwable->getMessage(), $extra) . "\n";
        }
    }

    /**
     * @return array
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * @param array $commands
     */
    public function setCommands(array $commands): void
    {
        $this->commands = $commands;
    }

    /**
     * @return array
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param array $actions
     */
    public function setActions(array $actions): void
    {
        $this->actions = $actions;
    }

    /**
     * @param array $arguments
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setCommandName(string $name): void
    {
        $this->commandName = $name;
    }

    /**
     * @return string
     */
    public function getCommandName(): string
    {
        return $this->commandName;
    }

    public function output(): void
    {
        print($this->output);
    }

    /**
     * @return void
     */
    protected function buildArguments(): void
    {
        $arguments = $_SERVER['argv'] ?? [];

        if (count($arguments) > 0) {
            array_shift($arguments);
        }

        if (count($arguments) == 0) {
            $command = CommandsListCommand::NAME;
        } else {
            $command = array_shift($arguments);
        }

        $this->setArguments($arguments);
        $this->setCommandName($command);
    }

    /**
     * @return string
     * @throws CommandNotFoundException
     */
    protected function runConsoleCommand(): string
    {
        $command = $this->findCommandMatch();
        $command = $this->commands[$command];
        $command = $this->createCommandInstanceFromName($command);

        $this->containerBuilder->set($command::class, $command);

        $command();

        return $this->formatCommandOutput($command);
    }

    /**
     * @return string
     * @throws CommandNotFoundException
     */
    protected function findCommandMatch(): string
    {
        $command = $this->getCommandName();

        if (isset($this->commands[$command])) {
            return $command;
        }

        $percents = [];
        foreach ($this->commands as $name => $classname) {
            similar_text(metaphone(strrev($name)), metaphone(strrev($command)), $result);
            if ($result > 50) {
                $percents[$name] = $result;
            }
        }

        if (!$percents) {
            throw new CommandNotFoundException(message: 'Command Not Found', context: $command);
        }

        try {
            arsort($percents);
            return key($percents);
        } catch (Exception $exception) {
            throw new CommandNotFoundException($command);
        }
    }

    /**
     * @param $filename
     * @return AbstractCommand|mixed
     * @throws Exception
     */
    protected function createCommandInstanceFromName($filename): mixed
    {
        $class = 'Application\Cli\Commands\\'.$filename;
        $this->containerBuilder->autowire($class, $class);

        return $this->containerBuilder->get($class);
    }

    /**
     * @param mixed $output
     * @param array|string $extra
     * @return string
     */
    protected function formatOutput(mixed $output, array|string $extra = ''): string
    {
        if (!$output) {
            $output = '';
        }

        if (is_array($output)) {
            $output = $this->formatOutputMap($output, '[~]');
        }

        if (is_array($extra)) {
            $extra = $this->formatOutputMap($extra, str_repeat(' ', 4) . '-');
        }

        return implode('', [$output, $extra]);
    }

    /**
     * @param AbstractCommand $command
     * @return string
     */
    protected function formatCommandOutput(AbstractCommand $command): string
    {
        $lines = $command->getLines();
        $resultMap = $command->getResultMap();

        return $this->formatOutput($lines, $resultMap);
    }

    /**
     * @param array $map
     * @param string $prefix
     * @return string
     */
    protected function formatOutputMap(array $map, string $prefix = ''): string
    {
        $output = '';
        $prefix = $prefix ? $prefix . ' ' : '';

        foreach ($map as $item) {
            $output .= sprintf("%s%s\n", (string) $prefix, (string) $item);
        }

        return $output;
    }

    /**
     * @param AbstractResolver $resolver
     * @return void
     */
    protected function resolve(AbstractResolver $resolver): void
    {
        $directories = $resolver->getDirectories();

        foreach ($directories as $directory) {
            $this->autoloader->loadDirectory($directory, function (string $filename) use ($resolver, $directory) {
                $path = $resolver->getNamePathFromFilename($filename);
                $property = strtolower($resolver->getNamePlural());
                $this->{$property}[$path] = $filename;
            });
        }
    }
}