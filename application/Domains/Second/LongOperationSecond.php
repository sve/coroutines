<?php

namespace Domains\Second;

use Application\Abstract\AbstractDomain;

class LongOperationSecond extends AbstractDomain
{
    /**
     * @param ...$arguments
     * @return mixed
     */
    public function __invoke(...$arguments): mixed
    {
        sleep(2);
        echo "Done 2\n";

        $this->logger->debug("Done 2");

        return 2;
    }
}