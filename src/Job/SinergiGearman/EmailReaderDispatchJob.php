<?php

namespace Smart\EmailReader\Job\SinergiGearman;

use GearmanJob;
use Sinergi\Gearman\JobInterface;
use Smart\EmailReader\Job\EmailReaderDispatchJob as BaseJobClass;

class EmailReaderDispatchJob extends BaseJobClass implements JobInterface
{
    /**
     * @param GearmanJob|null $job
     *
     * @return void
     */
    public function execute(GearmanJob $job = null)
    {

        if (!($job instanceof GearmanJob)) {
            return;
        }

        parent::executeJob((int)unserialize($job->workload()));
    }
}
