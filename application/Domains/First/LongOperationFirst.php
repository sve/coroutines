<?php

namespace Domains\First;

use Application\Abstract\AbstractDomain;
use Responders\ResponderFirst;

class LongOperationFirst extends AbstractDomain
{
    /**
     * @param ...$arguments
     * @return mixed
     */
    public function __invoke(...$arguments): mixed
    {
        sleep(3);
        echo "Done 1\n";

        $this->logger->debug("Done 1");

        return $this->respond(ResponderFirst::class, 'Done First');
    }
}