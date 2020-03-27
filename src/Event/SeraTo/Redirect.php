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
 * `<ROOT ATTR>.sera_to_redirect.id`
 * `<ROOT ATTR>.sera_to_redirect.name`
 * `<ROOT ATTR>.sera_to_redirect.group`
 * `<ROOT ATTR>.sera_to_redirect.short_url`
 * `<ROOT ATTR>.sera_to_redirect.destination_url`
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
     * `<ROOT ATTR>.sera_to_redirect.id`
     *
     * @param string $id
     * @return self
     */
    public function setRedirectId(string $id): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.id', $id);
    }

    /**
     * Sets the redirect name
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.sera_to_redirect.name`
     *
     * @param string $name
     * @return self
     */
    public function setRedirectName(string $name): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.name', $name);
    }

    /**
     * Sets the redirect ID
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.sera_to_redirect.group`
     *
     * @param string $group
     * @return self
     */
    public function setRedirectGroup(string $group): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.group', $group);
    }

    /**
     * Sets the redirect short URL
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.sera_to_redirect.short_url`
     *
     * @param string $url
     * @return self
     */
    public function setRedirectShortUrl(string $url): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.short_url', $url);
    }


    /**
     * Sets the redirect destination URL
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.sera_to_redirect.destination_url`
     *
     * @param string $url
     * @return self
     */
    public function setRedirectDestinationUrl(string $url): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.destination_url', $url);
    }
}
