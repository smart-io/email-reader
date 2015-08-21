<?php

namespace Smart\EmailReader\Job\PhpAmqpLib;

use PhpAmqpLib\Message\AMQPMessage;
use Smart\EmailReader\Job\EmailReaderDispatchJob as BaseJobClass;

class EmailReaderDispatchJob extends BaseJobClass
{
    /**
     * @param AMQPMessage|null $message
     */
    public function execute(AMQPMessage $message = null)
    {

        if (!($message instanceof AMQPMessage)) {
            return;
        }

        parent::executeJob((int)$message->body);
    }
}
