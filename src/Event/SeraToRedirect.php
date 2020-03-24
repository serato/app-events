<?php
declare(strict_types=1);

namespace Serato\AppEvents\Event;

/**
 * ** SeraToRedirect **
 *
 * Captures data attributes related to a sera.to redirect event and allows the
 * data to to be send to a logging target.
 *
 * Sets the following fields:
 *
 * `resource.id`
 * `resource.name`
 * `resource.group`
 * `resource.short_url`
 * `resource.destination_url`
 */
class SeraToRedirect extends AbstractTimeSeriesEvent
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'sera_to_redirect';
    }

    /**
     * Sets the redirect ID
     *
     * Sets the following field(s):
     *
     * `resource.id`
     *
     * @param string $id
     * @return self
     */
    public function setRedirectId(string $id): self
    {
        return $this->setData('resource.id', $id);
    }

    /**
     * Sets the redirect name
     *
     * Sets the following field(s):
     *
     * `resource.name`
     *
     * @param string $name
     * @return self
     */
    public function setRedirectName(string $name): self
    {
        return $this->setData('resource.name', $name);
    }

    /**
     * Sets the redirect ID
     *
     * Sets the following field(s):
     *
     * `resource.group`
     *
     * @param string $group
     * @return self
     */
    public function setRedirectGroup(string $group): self
    {
        return $this->setData('resource.group', $group);
    }

    /**
     * Sets the redirect short URL
     *
     * Sets the following field(s):
     *
     * `resource.short_url`
     *
     * @param string $url
     * @return self
     */
    public function setRedirectShortUrl(string $url): self
    {
        return $this->setData('resource.short_url', $url);
    }


    /**
     * Sets the redirect destination URL
     *
     * Sets the following field(s):
     *
     * `resource.destination_url`
     *
     * @param string $url
     * @return self
     */
    public function setRedirectDestinationUrl(string $url): self
    {
        return $this->setData('resource.destination_url', $url);
    }
}
