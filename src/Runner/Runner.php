<?php

namespace Application\Runner;

use Application\Structures\Map;
use Swoole\Process;
use Application\Container\Container;

class Runner
{
    use Map;

    public function __construct(
        protected Container $container
    ) {

    }

    public function run($action)
    {
        foreach ($action() as $key => $closure) {
            if (is_string($closure)) {
                $closure = $this->container->inject($closure);
            }

            $process = new Process($closure);
            $process->useQueue(0, 2);
            $pid = $process->start();

            $this->attachToMap($pid, $process);
            $this->attachToMap($key, $process);
        }

        while ($process = Process::wait(1)) {
            $pid = $process['pid'];
            $process = $this->getFromMap($pid);
            $process->wait();
        }
    }
}