<?php
declare(strict_types=1);

namespace Serato\AppEvents\EventTarget;

use Serato\AppEvents\Event\SendableEventInterface;

abstract class AbstractEventTarget
{
    /** @var string */
    private $appName;

    /** @var string */
    private $env;

    /**
     * Constructs the object
     *
     * @param string $appName
     * @param string $env
     */
    public function __construct(string $appName, string $env)
    {
        $this->appName = $appName;
        $this->env = $env;
    }

    /**
     * Logs the event
     *
     * @param SendableEventInterface $event
     * @return void
     */
    public function sendEvent(SendableEventInterface $event): void
    {
        $event
            ->setAppName($this->getAppName())
            ->setEnvironment($this->getEnvironment());
        
        $this->send($event);
    }

    /**
     * Implements event logging
     *
     * @param SendableEventInterface $event
     * @return void
     */
    abstract protected function send(SendableEventInterface $event): void;

    /**
     * Returns the application name
     *
     * @return string
     */
    public function getAppName(): string
    {
        return $this->appName;
    }

    /**
     * Returns the environment
     *
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->env;
    }
}
