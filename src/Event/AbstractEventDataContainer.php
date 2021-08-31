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
 * `labels.application`
 */
abstract class AbstractEventDataContainer extends AbstractDataContainer implements SendableEventInterface
{
    # The path to the root element that contains Serato-specific event data
    private const ROOT_EVENT_ATTR = 'serato.event_data';

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

    /**
     * Sets data under the root event attribute
     *
     * @param string $path
     * @param mixed $item
     * @return mixed
     */
    protected function setAppEventRootAttributeData(string $path, $item)
    {
        return $this->setData(self::ROOT_EVENT_ATTR . '.' . $path, $item);
    }

    /**
     * Returns data for a specified path under the root event attribute
     *
     * @param null|string $path
     * @return null|mixed
     */
    public function getAppEventRootData(?string $path = null)
    {
        return $this->getData(self::ROOT_EVENT_ATTR . ($path === null ? '' : '.' . $path));
    }
}
