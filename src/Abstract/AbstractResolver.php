<?php

namespace Application\Abstract;

use Application\Bootstrap\Autoloader;

abstract class AbstractResolver
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @return array
     */
    abstract public function getDirectories(): array;

    /**
     * @param Autoloader $autoloader
     */
    public function __construct(
        protected Autoloader $autoloader
    ) {

    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getNamePlural(): string
    {
        return "{$this->name}s";
    }

    /**
     * @return string
     */
    public function getNameLowerCase(): string
    {
        return $this->name;
    }

    /**
     * @param string $filename
     * @return string
     */
    public function getNameFromFilename(string $filename): string
    {
        return substr_replace($filename, '', -(strlen($this->name)));
    }

    /**
     * @param string $filename
     * @return string
     */
    public function getNamePathFromFilename(string $filename): string
    {
        $name = $this->getNameFromFilename($filename);
        $name = preg_replace('/([A-Z])/', ':$1', $name);

        return strtolower(ltrim($name, ':'));
    }
}