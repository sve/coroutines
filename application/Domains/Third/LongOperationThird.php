<?php

namespace Domains\Third;

use Application\Abstract\AbstractDomain;

class LongOperationThird extends AbstractDomain
{
    /**
     * @param ...$arguments
     * @return mixed
     */
    public function __invoke(...$arguments): mixed
    {
        sleep(2);
        echo "Done 3\n";

        $this->logger->debug("Done 3");

        return 3;
    }
}