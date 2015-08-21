<?php

namespace Smart\EmailReader\Job\Dispatcher;

interface JobDispatcherInterface
{
    /**
     * @param string $jobName
     * @param mixed $data
     *
     * @return mixed
     */
    public function dispatch($jobName, $data = null);
}
