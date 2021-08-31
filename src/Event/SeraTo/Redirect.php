<?php

declare(strict_types=1);

namespace Serato\AppEvents\Event\SeraTo;

use Serato\AppEvents\Event\AbstractTimeSeriesEvent;

/**
 * ** Redirect **
 *
 * Captures data attributes related to a sera.to redirect event and allows the
 * data to to be send to a logging target.
 *
 * Sets the following fields:
 *
 * `<APP EVENT ROOT ATTR>.id`
 * `<APP EVENT ROOT ATTR>.name`
 * `<APP EVENT ROOT ATTR>.group`
 * `<APP EVENT ROOT ATTR>.short_url`
 * `<APP EVENT ROOT ATTR>.destination_url`
 */
class Redirect extends AbstractTimeSeriesEvent
{
    /**
     * {@inheritdoc}
     */
    public function getEventActionCategory(): string
    {
        return 'sera_to';
    }

    /**
     * {@inheritdoc}
     */
    public function getEventActionName(): string
    {
        return 'redirect';
    }

    /**
     * Sets the redirect ID
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.id`
     *
     * @param string $id
     * @return self
     */
    public function setRedirectId(string $id): self
    {
        return $this->setAppEventRootAttributeData('id', $id);
    }

    /**
     * Sets the redirect name
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.name`
     *
     * @param string $name
     * @return self
     */
    public function setRedirectName(string $name): self
    {
        return $this->setAppEventRootAttributeData('name', $name);
    }

    /**
     * Sets the redirect ID
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.group`
     *
     * @param string $group
     * @return self
     */
    public function setRedirectGroup(string $group): self
    {
        return $this->setAppEventRootAttributeData('group', $group);
    }

    /**
     * Sets the redirect short URL
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.short_url`
     *
     * @param string $url
     * @return self
     */
    public function setRedirectShortUrl(string $url): self
    {
        return $this->setAppEventRootAttributeData('short_url', $url);
    }


    /**
     * Sets the redirect destination URL
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.destination_url`
     *
     * @param string $url
     * @return self
     */
    public function setRedirectDestinationUrl(string $url): self
    {
        return $this->setAppEventRootAttributeData('destination_url', $url);
    }
}
