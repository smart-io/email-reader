<?php
/**
 * ${CARET}
 */
namespace Smart\EmailReader\Dispatcher;

use Smart\EmailReader\EmailEntity;

interface DispatcherInterface
{
    /**
     * @return array
     */
    public function getHandlers();

    /**
     * @param string   $subjectRegex
     * @param callable $callback
     *
     * @return $this
     *
     * @throws DispatcherException
     */
    public function addHandler($subjectRegex, callable $callback);

    /**
     * @param DispatcherInterface $dispatcher
     *
     * @return $this
     * @throws DispatcherException
     */
    public function addDispatcher(DispatcherInterface $dispatcher);

    /**
     * @param EmailEntity $email
     */
    public function dispatch(EmailEntity $email);
}
