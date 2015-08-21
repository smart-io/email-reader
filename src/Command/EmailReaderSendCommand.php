<?php

namespace Smart\EmailReader\Command;

use Fetch\Message;
use Smart\EmailReader\Driver\EmailReaderDriverInterface;
use Smart\EmailReader\Job\EmailReaderDispatchJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Smart\EmailReader\Job\Dispatcher\JobDispatcherInterface;
use Smart\EmailReader\EmailEntity;

class EmailReaderSendCommand extends Command
{
    const COMMAND_NAME = 'emailreader:send-new-emails';

    /**
     * @var JobDispatcherInterface
     */
    private $jobDispatcher;

    /**
     * @var EmailReaderDriverInterface
     */
    private $emailReaderDriver;

    /**
     * @param JobDispatcherInterface                 $jobDispatcher
     * @param EmailReaderDriverInterface $emailReaderDriver
     */
    public function __construct(
        JobDispatcherInterface $jobDispatcher,
        EmailReaderDriverInterface $emailReaderDriver
    ) {

        $this->setJobDispatcher($jobDispatcher);
        $this->setEmailReaderDriver($emailReaderDriver);

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('This sends all new emails to job worker');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $pendingEmails = $this->getEmailReaderDriver()->getAllEmails();

        if (empty($pendingEmails)) {
            $output->write('No pending emails');

            return;
        }

        $output->write('Sending ' . count($pendingEmails)
            . ' emails to job worker: ');


        foreach ($pendingEmails as $pendingEmail) {

            /** @var EmailEntity $pendingEmail */

            $this->getJobDispatcher()->dispatch(
                EmailReaderDispatchJob::JOB_NAME,
                $pendingEmail->getUid()
            );
        }

        $output->write('[ <fg=green>DONE</fg=green> ]', true);
    }

    /**
     * @return JobDispatcherInterface
     */
    public function getJobDispatcher()
    {
        return $this->jobDispatcher;
    }

    /**
     * @param JobDispatcherInterface $jobDispatcher
     *
     * @return $this
     */
    public function setJobDispatcher(JobDispatcherInterface $jobDispatcher)
    {
        $this->jobDispatcher = $jobDispatcher;

        return $this;
    }

    /**
     * @return EmailReaderDriverInterface
     */
    public function getEmailReaderDriver()
    {
        return $this->emailReaderDriver;
    }

    /**
     * @param EmailReaderDriverInterface $emailReaderDriver
     *
     * @return $this
     */
    public function setEmailReaderDriver(
        EmailReaderDriverInterface $emailReaderDriver
    ) {
        $this->emailReaderDriver = $emailReaderDriver;

        return $this;
    }
}
