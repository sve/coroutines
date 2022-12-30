<?php

namespace Actions;

use Application\Abstract\AbstractAction;
use Application\Runner\Runner;
use Domains\First\LongOperationFirst;
use Domains\Second\LongOperationSecond;
use Domains\Third\LongOperationThird;
use Application\Logger\Logger;
use Generator;

class SomeAction extends AbstractAction
{
    /**
     * @param Logger $logger
     * @param Runner $runner
     * @param LongOperationSecond $longOperationSecond
     * @param LongOperationThird $longOperationThird
     */
    public function __construct(
        protected Logger              $logger,
        protected Runner              $runner,
        protected LongOperationSecond $longOperationSecond,
        protected LongOperationThird  $longOperationThird,
    ) {

    }

    /**
     * @return Generator
     */
    public function __invoke(): Generator
    {
        yield LongOperationFirst::class;
        yield $this->longOperationSecond;
        yield 'three' => fn () => $this->longOperationThird->run();
        yield function () {
            sleep(4);
            echo "Foobar\n";
            $this->logger->debug("foobar");
        };
        yield function () {
            sleep(4);
            echo "Foobar\n";
            $this->logger->debug("foobar");
        };
    }
}