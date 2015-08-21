<?php

namespace Smart\EmailReader\Job\Worker;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class PhpAmqpLib implements WorkerDriverInterface
{
    /**
     * @var AMQPChannel
     */
    protected $channel;

    /**
     * @param AMQPChannel $channel
     */
    public function __construct(AMQPChannel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * @param string $jobName
     * @param mixed $data
     * @return void
     */
    public function execute($jobName, $data = null)
    {
        $this->channel->queue_declare($jobName, false, false, false, false);
        $message = new AMQPMessage($data ?: '', ['delivery_mode' => 2]);
        $this->channel->basic_publish($message, '', $jobName);
    }
}
