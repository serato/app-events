<?php
declare(strict_types=1);

namespace Serato\AppEvents\LogTarget;

use Serato\AppEvents\Event\LoggableEventInterface;

abstract class AbstractLogTarget
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
     * @param LoggableEventInterface $event
     * @return void
     */
    public function logEvent(LoggableEventInterface $event): void
    {
        $event
            ->setAppName($this->getAppName())
            ->setEnvironment($this->getEnvironment());
        
        $this->log($event);
    }

    /**
     * Implements event logging
     *
     * @param LoggableEventInterface $event
     * @return void
     */
    abstract protected function log(LoggableEventInterface $event): void;

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
