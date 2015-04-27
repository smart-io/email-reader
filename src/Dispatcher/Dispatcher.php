<?php

namespace Smart\EmailReader\Dispatcher;

use Smart\EmailQueue\Email;
use Smart\EmailReader\EmailEntity;

abstract class Dispatcher implements DispatcherInterface
{

    /**
     * @var array
     */
    private $handlers = [];

    /**
     * @return array
     */
    public function getHandlers()
    {

        return $this->handlers;
    }

    /**
     * @param string   $subjectRegex
     * @param $callback
     *
     * @return $this
     *
     * @throws DispatcherException
     */
    public function addHandler($subjectRegex, $callback)
    {

        if (!isset($this->handlers[$subjectRegex])) {
            $this->handlers[$subjectRegex] = [];
        }

        $this->handlers[$subjectRegex][] = $callback;

        return $this;
    }

    /**
     * @param DispatcherInterface $dispatcher
     *
     * @return $this
     * @throws DispatcherException
     */
    public function addDispatcher(DispatcherInterface $dispatcher)
    {

        foreach ($dispatcher->getHandlers() as $subjectRegex => $handlers) {

            foreach ($handlers as $handler) {
                $this->addHandler($subjectRegex, $handler);
            }
        }

        return $this;
    }

    /**
     * @param EmailEntity $email
     */
    public function dispatch(
        EmailEntity $email
    ) {

        foreach ($this->handlers as $subjectRegex => $handlers) {

            if (preg_match($subjectRegex, $email->getSubject(), $matches)) {

                foreach ($handlers as $callback) {

                    if (call_user_func_array($callback, [$matches, $email])
                        === false
                    ) {
                        break;
                    }
                }
            }
        }
    }
}
