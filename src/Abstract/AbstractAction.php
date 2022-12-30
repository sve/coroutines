<?php

namespace Application\Abstract;

use Generator;

abstract class AbstractAction
{
    /**
     * @return Generator
     */
    abstract public function __invoke(): Generator;
}