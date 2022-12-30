<?php

namespace Application\Configuration;

use Application\Bootstrap\Autoloader;
use Application\Container\Container;
use Application\Structures\Map;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\DependencyInjection\TaggedContainerInterface;

class Configuration
{
    use Map {
        getMap as getPathMap;
        setMap as setPathMap;
        hasInMap as hasInPathMap;
        getFromMap as getFromPathMap;
        pushToMap as pushToPathMap;
        attachToMap as attachToPathMap;
        detachFromMap as unsetFromPathMap;
    }

    /**
     * @var array
     */
    protected array $configurationMap = [];

    /**
     * @var LoaderInterface
     */
    protected LoaderInterface $loader;

    /**
     * @param TaggedContainerInterface $containerBuilder
     * @param ContainerBagInterface $containerBag
     * @param Autoloader $autoloader
     * @param Container $container
     */
    public function __construct(
        protected TaggedContainerInterface $containerBuilder,
        protected ContainerBagInterface $containerBag,
        protected Autoloader $autoloader,
        protected Container $container
    ) {

    }

    /**
     * @return void
     */
    public function load(): void
    {
        $this->createConfigurationLoader();
        $this->importConfigurationFiles();
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function importConfigurationFiles(): void
    {
        foreach ($this->getPathMap() as $path => $namespace) {
            $this->loader->setCurrentDir($path);
            foreach (glob($path . '/*.php') as $filename) {
                $this->loader->load($filename);
                $this->loadFile($filename);
            }
        }
    }

    /**
     * @param string $key
     * @param string|null $default
     * @return mixed|string|null
     */
    public function get(string $key, string $default = null)
    {

        return $this->configurationMap[$key] ?: $default;
    }

    /**
     * @param string $id
     * @param mixed $value
     * @return void
     */
    public function add(string $id, mixed $value): void
    {
        $this->configurationMap = array_merge($this->configurationMap, [
            $id => $value,
        ]);
    }

    /**
     * @return void
     */
    protected function createConfigurationLoader(): void
    {
        $this->loader = new PhpFileLoader($this->containerBuilder, new FileLocator($this->getPathMap()));
    }

    /**
     * @param string $file
     * @return void
     */
    protected function loadFile(string $file): void
    {
        $result = $this->requireFile($file);
        $pathinfo = pathinfo($file);
        $id = $pathinfo['filename'];

        if (is_callable($result)) {
            $this->add($id, $result());

            $id = sprintf('config.%s', $id);
            $this->containerBuilder->registerAttributeForAutoconfiguration($id, $result);
            $this->containerBuilder->set($id, $result);
        }
    }

    /**
     * @param string $filename
     * @return mixed
     */
    protected function requireFile(string $filename): mixed
    {
        return require $filename;
    }
}