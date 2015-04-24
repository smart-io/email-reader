<?php

namespace Smart\EmailReader;

use Fetch\Message;
use Sinergi\Gearman\Dispatcher;
use Smart\EmailReader\Driver\EmailReaderDriverInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EmailReaderSendCommand extends Command
{
    const COMMAND_NAME = 'emailreader:send-new-emails';

    /**
     * @var Dispatcher
     */
    private $gearmanDispatcher;

    /**
     * @var EmailReaderDriverInterface
     */
    private $emailReaderDriver;

    /**
     * @param Dispatcher                 $gearmanDispatcher
     * @param EmailReaderDriverInterface $emailReaderDriver
     */
    public function __construct(
        Dispatcher $gearmanDispatcher,
        EmailReaderDriverInterface $emailReaderDriver
    ) {

        $this->setGearmanDispatcher($gearmanDispatcher);
        $this->setEmailReaderDriver($emailReaderDriver);

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('This sends all new emails to gearman');
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
            . ' emails to gearman: ');


        foreach ($pendingEmails as $pendingEmail) {

            /** @var EmailEntity $pendingEmail */

            $this->getGearmanDispatcher()->execute(
                EmailReaderDispatchJob::JOB_NAME, $pendingEmail->getUid(),
                null, EmailReaderDispatchJob::JOB_NAME
            );
        }

        $output->write('[ <fg=green>DONE</fg=green> ]', true);
    }

    /**
     * @return Dispatcher
     */
    public function getGearmanDispatcher()
    {
        return $this->gearmanDispatcher;
    }

    /**
     * @param Dispatcher $gearmanDispatcher
     *
     * @return $this
     */
    public function setGearmanDispatcher(Dispatcher $gearmanDispatcher)
    {
        $this->gearmanDispatcher = $gearmanDispatcher;

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
