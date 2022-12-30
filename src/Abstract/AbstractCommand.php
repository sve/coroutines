<?php

namespace Application\Abstract;

use Application\Structures\Map;

abstract class AbstractCommand
{
    use Map {
        getMap as getResultMap;
        setMap as setResultMap;
        hasInMap as hasInResultMap;
        getFromMap as getFromResultMap;
        pushToMap as pushToResultMap;
        attachToMap as attachToResultMap;
        detachFromMap as unsetFromResultMap;
    }

    /**
     * @var array
     */
    protected array $lines = [];

    /**
     * @return void
     */
    abstract public function __invoke(): void;

    /**
     * @return string
     */
    public function __toString(): string
    {
        return strtolower(str_replace('Command', '', static::class));
    }

    /**
     * @return array
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    /**
     * @param array $lines
     * @return void
     */
    public function setLines(array $lines): void
    {
        $this->lines = $lines;
    }

    /**
     * @param string $text
     * @return void
     */
    public function addLine(string $text): void
    {
        $this->lines[] = $text;
    }
}