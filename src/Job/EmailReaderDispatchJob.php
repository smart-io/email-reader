<?php

namespace Smart\EmailReader\Job;

use Exception;
use Smart\EmailReader\Dispatcher\DispatcherInterface;
use Smart\EmailReader\Driver\EmailReaderDriverInterface;
use Smart\EmailReader\EmailEntity;
use Smart\EmailReader\Logger\EmailReaderLogger;

abstract class EmailReaderDispatchJob
{
    const JOB_NAME = 'emailreader:dispatch';

    /**
     * @var EmailReaderDriverInterface
     */
    private $emailReaderDriver;

    /**
     * @var DispatcherInterface
     */
    private $emailDispatcher;

    /**
     * @var EmailReaderLogger
     */
    private $emailReaderLogger;

    /**
     * @param EmailReaderDriverInterface $emailReaderDriver
     * @param DispatcherInterface        $emailDispatcher
     * @param EmailReaderLogger          $emailReaderLogger
     */
    public function __construct(
        EmailReaderDriverInterface $emailReaderDriver,
        DispatcherInterface $emailDispatcher,
        EmailReaderLogger $emailReaderLogger
    ) {

        $this->emailReaderDriver = $emailReaderDriver;
        $this->emailDispatcher = $emailDispatcher;
        $this->emailReaderLogger = $emailReaderLogger;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::JOB_NAME;
    }

    /**
     * @param int $emailUId
     *
     * @return mixed
     */
    public function executeJob($emailUId)
    {

        $this->emailReaderLogger->info('Processing email # ' . $emailUId);

        try {

            $mainMailbox = $this->emailReaderDriver->getMailServerconfig()
                ->getMainMailbox();

            $email = $this->emailReaderDriver->getOneEmailByUid($emailUId,
                $mainMailbox);

        } catch (Exception $e) {

            $this->emailReaderLogger->error('Reader driver return an error : '
                . $e->getMessage());
        }

        if (!isset($email) || !($email instanceof EmailEntity)) {

            $this->emailReaderLogger->notice('The email doesn\'t exists anymore on the main mailbox');

            return;
        }

        $this->emailReaderLogger->info('Email subject : '
            . $email->getSubject());

        try {

            $this->emailDispatcher->dispatch($email);

        } catch (Exception $e) {
            $this->emailReaderLogger->error('Dispatcher return an error : '
                . $e->getMessage());
        }

        try {

            $processedMailbox = $this->emailReaderDriver->getMailServerconfig()
                ->getProcessedMailbox();
            $this->emailReaderDriver->moveEmail($email, $processedMailbox);

        } catch (Exception $e) {

            $this->emailReaderLogger->error('Reader driver return an error while moving the email : '
                . $e->getMessage());
        }
    }
}
