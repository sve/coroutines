<?php

namespace Application\Structures;

trait Map
{
    /**
     * @var array
     */
    protected array $map = [];

    /**
     * @return array
     */
    public function getMap(): array
    {
        return $this->map;
    }

    /**
     * @param array $map
     */
    public function setMap(array $map): void
    {
        $this->map = $map;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasInMap(string $key): bool
    {
        return isset($this->map[$key]);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getFromMap(string $key): mixed
    {
        if (!$this->hasInMap($key)) {
            return null;
        }

        return $this->map[$key];
    }

    /**
     * @param mixed $value
     */
    public function pushToMap(mixed $value): void
    {
        $this->map[] = $value;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function attachToMap(string $key, mixed $value): void
    {
        $this->map[$key] = $value;
    }

    /**
     * @param string $key
     * @return void
     */
    public function detachFromMap(string $key): void
    {
        if ($this->hasInMap($key)) {
            unset($this->map[$key]);
        }
    }
}