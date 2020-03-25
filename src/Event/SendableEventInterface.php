<?php
declare(strict_types=1);

namespace Serato\AppEvents\Event;

/**
 * Interface for events that can be sent to an event target
 */
interface SendableEventInterface
{
    /**
     * Returns the category of the event.
     *
     * Categories are a way of bucketing events together. Category names should by broad
     * and logically encapsulate one or more events. eg "checkout", "license_authorization".
     *
     * Category names should use snake casing.
     *
     * @return string
     */
    public function getEventCategory(): string;

    /**
     * Returns the name of the event.
     *
     * Event actions denote the specific action with a category bucket.
     * eg. "order_created", "deactivate"
     *
     * Category names should use snake casing.
     *
     * @return string
     */
    public function getEventAction(): string;

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
}
