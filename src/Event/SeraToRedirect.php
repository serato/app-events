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
 * `<ROOT ATTR>.id`
 * `<ROOT ATTR>.name`
 * `<ROOT ATTR>.group`
 * `<ROOT ATTR>.short_url`
 * `<ROOT ATTR>.destination_url`
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
     * `<ROOT ATTR>.id`
     *
     * @param string $id
     * @return self
     */
    public function setRedirectId(string $id): self
    {
        return $this->setData(self::ROOT_ATTR . '.id', $id);
    }

    /**
     * Sets the redirect name
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.name`
     *
     * @param string $name
     * @return self
     */
    public function setRedirectName(string $name): self
    {
        return $this->setData(self::ROOT_ATTR . '.name', $name);
    }

    /**
     * Sets the redirect ID
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.group`
     *
     * @param string $group
     * @return self
     */
    public function setRedirectGroup(string $group): self
    {
        return $this->setData(self::ROOT_ATTR . '.group', $group);
    }

    /**
     * Sets the redirect short URL
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.short_url`
     *
     * @param string $url
     * @return self
     */
    public function setRedirectShortUrl(string $url): self
    {
        return $this->setData(self::ROOT_ATTR . '.short_url', $url);
    }


    /**
     * Sets the redirect destination URL
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.destination_url`
     *
     * @param string $url
     * @return self
     */
    public function setRedirectDestinationUrl(string $url): self
    {
        return $this->setData(self::ROOT_ATTR . '.destination_url', $url);
    }
}
