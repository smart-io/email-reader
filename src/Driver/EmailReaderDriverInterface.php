<?php

namespace Smart\EmailReader\Driver;

use Smart\EmailReader\Config\EmailServerConfigInterface;
use Smart\EmailReader\EmailEntity;

interface EmailReaderDriverInterface
{
    /**
     * @param string $mailbox
     *
     * @return array
     */
    public function getAllEmails($mailbox = null);

    /**
     * @param      $uid
     * @param null $mailbox
     *
     * @return bool|EmailEntity
     */
    public function getOneEmailByUid($uid, $mailbox = null);

    /**
     * @param EmailEntity $email
     * @param string      $mailbox
     *
     * @return $this
     */
    public function moveEmail(EmailEntity $email, $mailbox);

    /**
     * @return EmailServerConfigInterface
     */
    public function getMailServerconfig();

    /**
     * @param EmailServerConfigInterface $mailServerconfig
     *
     * @return $this
     */
    public function setMailServerconfig(
        EmailServerConfigInterface $mailServerconfig
    );
}
