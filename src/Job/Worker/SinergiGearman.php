<?php

namespace Smart\EmailReader\Job\Worker;

use Sinergi\Gearman\Dispatcher;

class SinergiGearman implements WorkerDriverInterface
{
    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param string $jobName
     * @param mixed $data
     * @return void
     */
    public function execute($jobName, $data = null)
    {
        $this->dispatcher->background($jobName, $data, null, $jobName);
    }
}
