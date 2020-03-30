<?php
declare(strict_types=1);

namespace Serato\AppEvents\EventTarget;

use Serato\AppEvents\Event\SendableEventInterface;

abstract class AbstractEventTarget
{
    /** @var string */
    private $appName;

    /**
     * Constructs the object
     *
     * @param string $appName
     */
    public function __construct(string $appName)
    {
        $this->appName = $appName;
    }

    /**
     * Logs the event
     *
     * @param SendableEventInterface $event
     * @return void
     */
    public function sendEvent(SendableEventInterface $event): void
    {
        $event->setAppName($this->getAppName());
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
}
