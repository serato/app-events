<?php
declare(strict_types=1);

namespace Serato\AppEvents\Event;

use Serato\AppEvents\Exception\InvalidDataValueException;

/**
 * ** AbstractEventDataContainer **
 *
 * Abstract class that all event data containers inherit from.
 *
 * Sets the following fields:
 *
 * `event.kind`
 */
abstract class AbstractEventDataContainer extends AbstractDataContainer implements SendableEventInterface
{
    public function __construct()
    {
        $this->setData('event.kind', 'event');
    }

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
    abstract public function getEventActionCategory(): string;

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
    abstract public function getEventActionName(): string;

    /**
     * Sets the application name
     *
     * Sets the following field(s):
     *
     * `labels.application`
     *
     * @param string $appName
     * @return mixed
     */
    public function setAppName(string $appName)
    {
        return $this->setData('labels.application', $appName);
    }

    /**
     * Returns the application name
     *
     * @return string|null
     */
    public function getAppName(): ?string
    {
        return $this->getData('labels.application') === null ? null : (string)$this->getData('labels.application');
    }
}
