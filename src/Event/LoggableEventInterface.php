<?php
declare(strict_types=1);

namespace Serato\AppEvents\Event;

/**
 * Interface for events that can be logged
 */
interface LoggableEventInterface
{
    /**
     * Returns the name of the event
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Sets the name of the application that the event came from
     *
     * @param string $appName
     * @return self
     */
    public function setAppName(string $appName): self;

    /**
     * Returns the application name
     *
     * @return string|null
     */
    public function getAppName(): ?string;

    /**
     * Sets the environment that the application is running in
     *
     * @param string $env
     * @return self
     */
    public function setEnvironment(string $env): self;

    /**
     * Returns the environment
     *
     * @return string|null
     */
    public function getEnvironment(): ?string;
}
