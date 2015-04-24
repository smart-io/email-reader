<?php

namespace Smart\EmailReader\Config;

class EmailServerConfig implements EmailServerConfigInterface
{
    const IMAP = 'imap';
    const POP = 'pop';

    /**
     * @var string
     */
    private $domain;

    /**
     * @var string
     */
    private $port = 143;

    /**
     * @var string
     */
    private $service = self::IMAP;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $mainMailbox;

    /**
     * @var string
     */
    private $processedMailbox;

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     *
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param string $port
     *
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param string $service
     *
     * @return $this
     */
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getMainMailbox()
    {
        return $this->mainMailbox;
    }

    /**
     * @param string $mainMailbox
     *
     * @return $this
     */
    public function setMainMailbox($mainMailbox)
    {
        $this->mainMailbox = $mainMailbox;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessedMailbox()
    {
        return $this->processedMailbox;
    }

    /**
     * @param string $processedMailbox
     *
     * @return $this
     */
    public function setProcessedMailbox($processedMailbox)
    {
        $this->processedMailbox = $processedMailbox;

        return $this;
    }
}
