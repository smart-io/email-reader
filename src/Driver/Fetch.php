<?php

namespace Smart\EmailReader\Driver;

use DateTime;
use EmailReplyParser\EmailReplyParser;
use Fetch\Message;
use Fetch\Server;
use Smart\EmailReader\Config\EmailServerConfigInterface;
use Smart\EmailReader\EmailEntity;

class Fetch implements EmailReaderDriverInterface
{

    /**
     * @var Server
     */
    private $mailServer;

    /**
     * @var EmailServerConfigInterface
     */
    private $mailServerconfig;

    /**
     * @var bool
     */
    private $isConnected = false;

    /**
     * @param EmailServerConfigInterface $mailServerConfig
     */
    public function __construct(EmailServerConfigInterface $mailServerConfig)
    {
        $this->mailServerconfig = $mailServerConfig;
    }

    /**
     * @param string $mailbox
     *
     * @return array
     */
    public function getAllEmails($mailbox = null)
    {

        $this->connectMailServer();

        if ($mailbox === null) {
            $mailbox = $this->getMailServerconfig()->getMainMailbox();
        }

        if ($this->mailServer->getMailBox() !== $mailbox) {

            $this->mailServer->setMailBox($mailbox);
        }

        $emails = [];

        foreach ($this->mailServer->getMessages() as $message) {

            $emails[] = $this->getEmailEntity($message);
        }

        return $emails;
    }

    /**
     * @param      $uid
     * @param null $mailbox
     *
     * @return bool|EmailEntity
     */
    public function getOneEmailByUid($uid, $mailbox = null)
    {
        $this->connectMailServer();

        $message = $this->mailServer->getMessageByUid($uid);

        if ($message instanceof Message) {

//            if (!empty($mailbox)
//                && $message->getImapBox()->getMailBox() !== $mailbox
//            ) {
//                return false;
//            }

            return $this->getEmailEntity($message);
        }

        return false;
    }

    /**
     * @param EmailEntity $email
     * @param string      $mailbox
     *
     * @return $this
     */
    public function moveEmail(EmailEntity $email, $mailbox)
    {

        $this->connectMailServer();

        $email = $this->mailServer->getMessageByUid($email->getUid());
        $email->moveToMailBox($mailbox);

        return $this;
    }

    /**
     * @return EmailServerConfigInterface
     */
    public function getMailServerconfig()
    {
        return $this->mailServerconfig;
    }

    /**
     * @param EmailServerConfigInterface $mailServerconfig
     *
     * @return $this
     */
    public function setMailServerconfig(
        EmailServerConfigInterface $mailServerconfig
    ) {
        $this->mailServerconfig = $mailServerconfig;

        return $this;
    }

    private function connectMailServer()
    {
        if ($this->isConnected) {
            return;
        }

        $this->mailServer = new Server(
            $this->getMailServerconfig()->getDomain(),
            $this->getMailServerconfig()->getPort(),
            $this->getMailServerconfig()->getService()
        );

        $this->mailServer->setFlag('novalidate-cert');

        $this->mailServer->setAuthentication(
            $this->getMailServerconfig()->getUsername(),
            $this->getMailServerconfig()->getPassword()
        );

        $this->isConnected = true;
    }

    /**
     * @param Message $message
     *
     * @return EmailEntity
     */
    private function getEmailEntity(Message $message)
    {

        $recipientName = explode('<', $message->getAddresses('to', true));
        $recipientName = trim($recipientName[0]);

        $senderName = explode('<', $message->getAddresses('from', true));
        $senderName = trim($senderName[0]);

        $recipientEmail = $message->getAddresses('to', false);
        $recipientEmail = trim($recipientEmail[0]);

        $senderEmail = $message->getAddresses('from', false);
        $senderEmail = trim($senderEmail[0]);

        $emailTextBody = trim($message->getMessageBody(false));
        $emailHtmlBody = trim($message->getMessageBody(true));

        $emailToParse = $emailTextBody ?: $emailHtmlBody;
        $lastReply = (new EmailReplyParser())->parseReply($emailToParse);

        $attachments = $message->getAttachments() ?: [];

        return (new EmailEntity())
            ->setUid($message->getUid())
            ->setHeaders((array)$message->getHeaders())
            ->setDatetime((new DateTime)->setTimestamp($message->getDate()))
            ->setSubject($message->getSubject())
            ->setTextBody($emailTextBody)
            ->setHtmlBody($emailHtmlBody)
            ->setLastReply($lastReply)
            ->setRecipientName($recipientName)
            ->setRecipientEmail($recipientEmail)
            ->setSenderName($senderName)
            ->setSenderEmail($senderEmail)
            ->setAttachments($attachments);
    }
}
