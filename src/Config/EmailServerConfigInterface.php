<?php

namespace Smart\EmailReader\Config;

interface EmailServerConfigInterface
{
    /**
     * @return string
     */
    public function getDomain();

    /**
     * @param string $domain
     *
     * @return $this
     */
    public function setDomain($domain);

    /**
     * @return string
     */
    public function getPort();

    /**
     * @param string $port
     *
     * @return $this
     */
    public function setPort($port);

    /**
     * @return string
     */
    public function getService();

    /**
     * @param string $service
     *
     * @return $this
     */
    public function setService($service);

    /**
     * @return string
     */
    public function getUsername();

    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username);

    /**
     * @return string
     */
    public function getPassword();

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password);

    /**
     * @return string
     */
    public function getMainMailbox();

    /**
     * @param string $mainMailbox
     *
     * @return $this
     */
    public function setMainMailbox($mainMailbox);

    /**
     * @return string
     */
    public function getProcessedMailbox();

    /**
     * @param string $processedMailbox
     *
     * @return $this
     */
    public function setProcessedMailbox($processedMailbox);
}
