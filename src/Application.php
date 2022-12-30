<?php

namespace Application;

use Application\Bootstrap\Autoloader;
use Application\Cli\Cli;
use Application\Configuration\Configuration;
use Application\Container\Container;
use Application\Logger\Logger;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\TaggedContainerInterface;

class Application
{
    /**
     * @var Autoloader
     */
    protected Autoloader $autoloader;

    /**
     * @var ContainerBuilder
     */
    protected ContainerBuilder $containerBuilder;

    /**
     * @var Configuration
     */
    protected Configuration $configuration;

    /**
     * @var Container
     */
    protected Container $container;

    /**
     * @var Logger
     */
    protected Logger $logger;

    /**
     *
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * @return Autoloader
     */
    public function getAutoloader(): Autoloader
    {
        return $this->autoloader;
    }

    /**
     * @param Autoloader $autoloader
     */
    public function setAutoloader(Autoloader $autoloader): void
    {
        $this->autoloader = $autoloader;
        $this->initializeAutoload();
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $this->autoWireActions();
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function console(): void
    {
        $this->autoWireActions();

        /**
         * @var Cli $cli
         */
        $cli = $this->container->inject(Cli::class);
        $cli->run();
        $cli->output();
    }

    /**
     * @return void
     */
    protected function autoWireActions(): void
    {
        $this->autoloader->autoloadActionsDirectory();
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function initialize(): void
    {
        $this->initializeContainerBuilder();
        $this->initializeContainer();
        $this->initializeConfiguration();
        $this->initializeLogger();
    }

    /**
     * @return void
     */
    protected function initializeAutoload(): void
    {
        $this->containerBuilder->set(Autoloader::class, $this->autoloader);
        $this->autoloader->setPathsCommands([
            sprintf( '%s%s%s', __DIR__, DIRECTORY_SEPARATOR, 'Cli/Commands'),
        ]);

        $pathsActions = [];
        $pathsActions[] = sprintf( '%s%s%s', __DIR__, DIRECTORY_SEPARATOR, 'Cli/Actions');
        $pathsActions[] = sprintf(
            '%s%s%s%s%s',
            __DIR__,
            DIRECTORY_SEPARATOR,
            '../',
            $this->autoloader->getApplicationPath(),
            'Actions'
        );
        $this->autoloader->setPathsActions($pathsActions);
    }

    /**
     * @return void
     */
    protected function initializeContainerBuilder(): void
    {
        $this->containerBuilder = new ContainerBuilder();
        $this->containerBuilder->set(ContainerBuilder::class, $this->containerBuilder);
        $this->containerBuilder->set(TaggedContainerInterface::class, $this->containerBuilder);
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function initializeContainer(): void
    {
        $id = Container::class;
        $this->containerBuilder->autowire($id, $id);
        $this->container = $this->containerBuilder->get($id);
        $this->container->setContainerBuilder($this->containerBuilder);
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function initializeConfiguration(): void
    {
        $this->configuration = $this->container->inject(Configuration::class);
        $configurationPath = sprintf('%s/../config/', __DIR__);
        $this->configuration->attachToPathMap($configurationPath, $configurationPath);
        $this->configuration->load();
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function initializeLogger()
    {
        $this->logger = $this->container->inject(Logger::class);
    }
}