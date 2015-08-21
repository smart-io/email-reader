<?php

namespace Smart\EmailReader\Job\Worker;

interface WorkerDriverInterface
{
    /**
     * @param string $jobName
     * @param mixed $data
     * @return bool
     */
    public function execute($jobName, $data = null);
}
