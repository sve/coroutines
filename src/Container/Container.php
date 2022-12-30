<?php

namespace Application\Container;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class Container
{
    /**
     * @var ContainerBuilder
     */
    protected static ContainerBuilder $containerBuilder;

    /**
     * @param ContainerBuilder $containerBuilder
     * @return $this
     */
    public function setContainerBuilder(ContainerBuilder $containerBuilder): self
    {
        self::$containerBuilder = $containerBuilder;

        return $this;
    }

    /**
     * @param string $class
     * @param string|null $id
     * @return object|string|null
     * @throws \Exception
     */
    public function inject(string $class, string $id = null)
    {
        $id = $id ?: $class;

        if (!self::$containerBuilder->has($id)) {
            self::$containerBuilder->autowire($id, $class);
        }

        return self::$containerBuilder->get($id);
    }

    /**
     * @param string $id
     * @return mixed
     * @throws \Exception
     */
    public function get(string $id): mixed
    {
        return self::$containerBuilder->get($id);
    }

    /**
     * @param string $id
     * @param object|null $service
     * @return void
     */
    public function set(string $id, ?object $service): void
    {
        self::$containerBuilder->set($id, $service);
    }
}